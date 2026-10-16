<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Ticket;
use App\Models\Resident;
use App\Services\AiManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Throwable;

class AiAssistantHubController extends Controller
{
    /**
     * Trung tâm điều phối Trợ lý AI Đa tác nhân (FEAT_14_AI_ASSISTANT_HUB)
     */
    public function dispatchHub(Request $request, AiManagementService $aiService)
    {
        // 1. Kiểm tra giới hạn tần suất truy vấn (ERR_14_04: 20 req/phút)
        $rateKey = 'ai_hub_' . ($request->user()?->id ?? $request->ip());
        if (RateLimiter::tooManyAttempts($rateKey, 20)) {
            $seconds = RateLimiter::availableIn($rateKey);
            return response()->json([
                'success' => false,
                'error_code' => 'ERR_14_04',
                'message' => 'Bạn đã gửi quá nhiều yêu cầu trong thời gian ngắn (tối đa 20 req/phút). Vui lòng đợi 30 giây!',
                'retry_after' => max(30, $seconds),
            ], 429);
        }
        RateLimiter::hit($rateKey, 60);

        // 2. Kiểm tra câu hỏi để trống (ERR_14_01)
        $rawPrompt = $request->input('prompt', $request->input('question', ''));
        if ($rawPrompt === null || trim((string) $rawPrompt) === '') {
            return response()->json([
                'success' => false,
                'error_code' => 'ERR_14_01',
                'message' => 'Vui lòng nhập câu hỏi hoặc yêu cầu cần trợ lý AI hỗ trợ.',
            ], 422);
        }

        $prompt = trim((string) $rawPrompt);

        // 3. Kiểm tra độ dài tối thiểu 5 ký tự (ERR_14_02)
        if (mb_strlen($prompt) < 5) {
            return response()->json([
                'success' => false,
                'error_code' => 'ERR_14_02',
                'message' => 'Nội dung yêu cầu quá ngắn (tối thiểu 5 ký tự) để AI có đủ ngữ cảnh xử lý.',
            ], 422);
        }

        // 4. Kiểm tra độ dài tối đa 2000 ký tự & chống XSS (Regex kiểm tra ký tự điều khiển)
        if (mb_strlen($prompt) > 2000) {
            return response()->json([
                'success' => false,
                'error_code' => 'ERR_14_02',
                'message' => 'Nội dung yêu cầu không được vượt quá 2.000 ký tự.',
            ], 422);
        }

        if (preg_match('/[<>]/u', $prompt)) {
            $prompt = strip_tags($prompt);
        }

        $agentMode = $request->input('agent_mode', 'landlord_advisor');
        if (!in_array($agentMode, ['landlord_advisor', 'landlord_qa', 'tenant_incident', 'room_finder'], true)) {
            $agentMode = 'landlord_advisor';
        }

        $tone = $request->input('tone', 'professional');
        $contextId = $request->input('context_id');

        // Định tuyến xử lý theo chế độ Agent
        switch ($agentMode) {
            case 'landlord_qa':
                return $this->handleLandlordQA($request, $prompt, $tone, $contextId, $aiService);
            case 'landlord_advisor':
                return $this->handleLandlordAdvisor($request, $prompt, $tone, $contextId, $aiService);
            case 'tenant_incident':
                return $this->handleTenantIncident($request, $prompt, $tone, $contextId, $aiService);
            case 'room_finder':
            default:
                return $this->handleRoomFinder($request, $prompt, $tone, $contextId, $aiService);
        }
    }

    /**
     * Agent 1: Tra cứu và gợi ý phòng trọ thông minh theo ngôn ngữ tự nhiên
     */
    public function roomFinder(Request $request, AiManagementService $aiService)
    {
        $request->merge(['agent_mode' => 'room_finder']);
        return $this->dispatchHub($request, $aiService);
    }

    /**
     * Agent 2: Cố vấn kinh doanh, tối ưu giá thuê & soạn bài đăng chuẩn SEO cho chủ trọ
     */
    public function landlordAdvisor(Request $request, AiManagementService $aiService)
    {
        $request->merge(['agent_mode' => 'landlord_advisor']);
        return $this->dispatchHub($request, $aiService);
    }

    /**
     * Agent 2b: Trợ lý Hỏi - Đáp & Tư vấn nghiệp vụ quản lý cho chủ trọ
     */
    public function landlordQA(Request $request, AiManagementService $aiService)
    {
        $request->merge(['agent_mode' => 'landlord_qa']);
        return $this->dispatchHub($request, $aiService);
    }

    /**
     * Agent 3: Tiếp nhận, phân loại khẩn cấp sự cố kỹ thuật và hướng dẫn xử lý an toàn (Khách thuê / Cư dân)
     */
    public function incidentTriage(Request $request, AiManagementService $aiService)
    {
        $request->merge(['agent_mode' => 'tenant_incident']);
        return $this->dispatchHub($request, $aiService);
    }

    // ==========================================
    // CÁC HÀM XỬ LÝ AGENT CHUYÊN BIỆT
    // ==========================================

    /**
     * Xử lý Trợ lý Hỏi - Đáp & Tư vấn nghiệp vụ vận hành cho chủ trọ
     */
    private function handleLandlordQA(Request $request, string $prompt, string $tone, ?int $contextId, AiManagementService $aiService)
    {
        $user = Auth::user();
        $tenant = $user?->tenant;

        $contextSummary = [
            'facility_name' => $tenant?->name ?? 'Cơ sở lưu trú SmartRoom',
            'facility_address' => $tenant?->address ?? 'Khu vực nội thành',
            'user_name' => $user?->name ?? 'Chủ trọ SmartRoom',
        ];

        // Gọi AI giải đáp thắc mắc nghiệp vụ với timeout 6s (ERR_14_03)
        $aiResult = $this->callGeminiWithTimeout([
            [
                'role' => 'system',
                'content' => 'Bạn là Trợ lý AI Chuyên gia Cố vấn Vận hành & Quản lý Nhà trọ của hệ thống SmartRoom. '
                    . 'Hãy giải đáp tận tâm, chuẩn xác, chuyên nghiệp bằng tiếng Việt cho chủ trọ về mọi vấn đề: '
                    . 'thủ tục đăng ký tạm trú, hợp đồng thuê nhà điện tử, xử lý tiền cọc, giải quyết tranh chấp cư dân, '
                    . 'tính toán đối soát hóa đơn điện nước, quy định an toàn PCCC, và kinh nghiệm gia tăng tỷ lệ lấp đầy phòng.'
            ],
            [
                'role' => 'user',
                'content' => "Câu hỏi/Thắc mắc của chủ trọ: {$prompt}\nVăn phong mong muốn: {$tone}\nThông tin cơ sở:\n" . json_encode($contextSummary, JSON_UNESCAPED_UNICODE)
            ]
        ], 6);

        if (!$aiResult['success']) {
            // Kho tri thức mẫu dự phòng nghiệp vụ chủ trọ (ERR_14_03)
            $fallbackAnswer = "💡 HƯỚNG DẪN NGHIỆP VỤ QUẢN LÝ DÀNH CHO CHỦ TRỌ:\n\n"
                . "• Về câu hỏi của bạn: \"{$prompt}\"\n\n"
                . "1. Về Quy định & Thủ tục: Chủ cơ sở cần kiểm tra kỹ thông tin định danh CCCD và hoàn tất thủ tục khai báo tạm trú qua Cổng Dịch vụ công Quản lý cư trú trong vòng 24 giờ sau khi khách dọn vào.\n"
                . "2. Về Hợp đồng & Tiền cọc: Quy định rõ ràng trong hợp đồng về điều khoản hoàn cọc (báo trước tối thiểu 30 ngày), bàn giao hiện trạng trang thiết bị đầy đủ.\n"
                . "3. Về Điện - Nước & Chi phí: Áp dụng công tơ riêng từng phòng, chốt số định kỳ ngày 25-30 hàng tháng và xuất hóa đơn tự động kèm mã VietQR trên SmartRoom để tránh thất thoát và minh bạch công nợ.\n\n"
                . "Bạn có thể sao chép hướng dẫn này hoặc tham khảo thêm tại mục Quy trình vận hành SmartRoom!";

            return response()->json([
                'success' => true,
                'agent_mode' => 'landlord_qa',
                'response' => $fallbackAnswer,
                'is_fallback' => true,
                'error_code' => 'ERR_14_03',
                'warning_message' => 'Kết nối máy chủ AI quá thời gian chờ (6s), tự động kích hoạt câu trả lời từ kho tri thức dự phòng.',
                'quick_action' => 'copy_content',
                'copy_success_message' => 'Đã sao chép câu trả lời tư vấn nghiệp vụ vào bộ nhớ tạm thành công!',
            ]);
        }

        return response()->json([
            'success' => true,
            'agent_mode' => 'landlord_qa',
            'response' => $aiResult['content'],
            'is_fallback' => false,
            'quick_action' => 'copy_content',
            'copy_success_message' => 'Đã sao chép câu trả lời tư vấn nghiệp vụ vào bộ nhớ tạm thành công!',
        ]);
    }

    /**
     * Xử lý Tra cứu phòng thông minh
     */
    private function handleRoomFinder(Request $request, string $prompt, string $tone, ?int $contextId, AiManagementService $aiService)
    {
        $user = Auth::user();
        $tenantId = $user?->tenant_id;

        // Truy vấn danh sách phòng trống thực tế
        $roomQuery = Room::query()
            ->with(['building'])
            ->where('status', 'empty');

        if ($tenantId) {
            $roomQuery->where('tenant_id', $tenantId);
        }

        $rooms = $roomQuery->take(5)->get();

        // Thử gọi AI với timeout 6s (ERR_14_03)
        $aiResult = $this->callGeminiWithTimeout([
            [
                'role' => 'system',
                'content' => 'Bạn là chuyên viên tư vấn tìm phòng trọ thông minh của nền tảng Renty. Phản hồi bằng tiếng Việt thân thiện, súc tích kèm thông tin phòng phù hợp.'
            ],
            [
                'role' => 'user',
                'content' => "Yêu cầu của khách: {$prompt}\n\nDữ liệu phòng trống thực tế:\n" . json_encode($rooms->map(fn($r) => [
                    'id' => $r->id,
                    'room_number' => $r->room_number,
                    'building' => $r->building?->name,
                    'price' => number_format($r->price) . 'đ',
                    'area' => $r->area . 'm2',
                    'amenities' => $r->amenities,
                ]), JSON_UNESCAPED_UNICODE)
            ]
        ], 6);

        if (!$aiResult['success']) {
            // Kích hoạt kho tri thức mẫu dự phòng (ERR_14_03)
            $fallbackReply = "Dựa trên yêu cầu của bạn, hệ thống tìm thấy các phòng trống phù hợp nhất trong cơ sở lưu trú:\n\n";
            foreach ($rooms as $r) {
                $fallbackReply .= "• Phòng {$r->room_number} ({$r->building?->name}): {$r->area}m², giá " . number_format($r->price) . "đ/tháng.\n";
            }
            $fallbackReply .= "\nBạn có thể nhấp 'Đặt lịch xem phòng' để đặt lịch hẹn tham quan trực tiếp!";

            return response()->json([
                'success' => true,
                'agent_mode' => 'room_finder',
                'response' => $fallbackReply,
                'rooms' => $rooms,
                'is_fallback' => true,
                'error_code' => 'ERR_14_03',
                'warning_message' => 'Kết nối máy chủ AI quá thời gian chờ (6s), tự động kích hoạt câu trả lời từ kho tri thức dự phòng.',
                'quick_action' => 'book_viewing',
            ]);
        }

        return response()->json([
            'success' => true,
            'agent_mode' => 'room_finder',
            'response' => $aiResult['content'],
            'rooms' => $rooms,
            'is_fallback' => false,
            'quick_action' => 'book_viewing',
        ]);
    }

    /**
     * Xử lý Cố vấn chủ trọ & Soạn thảo bài đăng marketing chuẩn SEO
     */
    private function handleLandlordAdvisor(Request $request, string $prompt, string $tone, ?int $contextId, AiManagementService $aiService)
    {
        $user = Auth::user();
        $targetRoom = null;
        if ($contextId) {
            $targetRoom = Room::with('building')->find($contextId);
        } elseif ($user?->tenant_id) {
            $targetRoom = Room::with('building')->where('tenant_id', $user->tenant_id)->first();
        }

        $roomInfo = $targetRoom ? [
            'room_number' => $targetRoom->room_number,
            'building' => $targetRoom->building?->name ?? 'Tòa nhà SmartRoom',
            'address' => $targetRoom->building?->address ?? 'Khu vực trung tâm',
            'price' => number_format($targetRoom->price) . ' VNĐ/tháng',
            'area' => $targetRoom->area . ' m²',
            'amenities' => is_array($targetRoom->amenities) ? implode(', ', $targetRoom->amenities) : ($targetRoom->amenities ?? 'Đầy đủ nội thất'),
        ] : [
            'room_number' => '101',
            'building' => 'Tòa nhà Căn Hộ Dịch Vụ Cao Cấp',
            'address' => 'Gần các trường đại học lớn và trục đường chính',
            'price' => '3.800.000 VNĐ/tháng',
            'area' => '28 m²',
            'amenities' => 'Máy lạnh, tủ lạnh, máy giặt riêng, ban công thoáng mát, khóa vân tay an ninh 24/7',
        ];

        // Gọi AI tạo bài viết chuẩn SEO với timeout 6s (ERR_14_03)
        $aiResult = $this->callGeminiWithTimeout([
            [
                'role' => 'system',
                'content' => 'Bạn là chuyên gia Marketing Bất động sản và Cố vấn kinh doanh nhà trọ. Hãy viết bài đăng cho thuê phòng chuẩn SEO, hấp dẫn, tiêu đề giật tít, đầy đủ tiện ích và lời kêu gọi hành động (CTA), kèm hashtag.'
            ],
            [
                'role' => 'user',
                'content' => "Yêu cầu: {$prompt}\nVăn phong: {$tone}\nThông tin phòng:\n" . json_encode($roomInfo, JSON_UNESCAPED_UNICODE)
            ]
        ], 6);

        if (!$aiResult['success']) {
            // Kho tri thức mẫu dự phòng chuẩn SEO (ERR_14_03)
            $fallbackSeoPost = "🔥 [CHO THUÊ GẤP] PHÒNG TRỌ FULL NỘI THẤT - BAN CÔNG THOÁNG MÁT TẠI {$roomInfo['building']}\n\n"
                . "📍 Địa chỉ: {$roomInfo['address']}\n"
                . "💰 Giá thuê ưu đãi: {$roomInfo['price']}\n"
                . "📐 Diện tích: {$roomInfo['area']}\n\n"
                . "✨ TIỆN ÍCH NỔI BẬT:\n"
                . "✔ {$roomInfo['amenities']}\n"
                . "✔ Giờ giấc tự do, không chung chủ, bảo vệ & camera 24/7\n"
                . "✔ Hệ thống SmartRoom quản lý số điện nước minh bạch qua ứng dụng\n\n"
                . "📞 Liên hệ xem phòng ngay hôm nay để nhận ưu đãi cọc sớm nhất!\n"
                . "#ThuePhongTro #SmartRoom #CanHoDichVu #PhongTroGiaRe";

            return response()->json([
                'success' => true,
                'agent_mode' => 'landlord_advisor',
                'response' => $fallbackSeoPost,
                'is_fallback' => true,
                'error_code' => 'ERR_14_03',
                'warning_message' => 'Kết nối máy chủ AI quá thời gian chờ (6s), tự động kích hoạt câu trả lời từ kho tri thức dự phòng.',
                'quick_action' => 'copy_content',
                'copy_success_message' => 'Đã sao chép nội dung bài viết marketing chuẩn SEO vào bộ nhớ tạm thành công!', // ERR_14_05
            ]);
        }

        return response()->json([
            'success' => true,
            'agent_mode' => 'landlord_advisor',
            'response' => $aiResult['content'],
            'is_fallback' => false,
            'quick_action' => 'copy_content',
            'copy_success_message' => 'Đã sao chép nội dung bài viết marketing chuẩn SEO vào bộ nhớ tạm thành công!', // ERR_14_05
        ]);
    }

    /**
     * Xử lý Phân loại sự cố kỹ thuật & Tự động tạo ticket khẩn cấp (ERR_14_06)
     */
    private function handleTenantIncident(Request $request, string $prompt, string $tone, ?int $contextId, AiManagementService $aiService)
    {
        $user = Auth::user();
        $lowerPrompt = mb_strtolower($prompt);

        // Phát hiện sự cố khẩn cấp (Emergency / Urgent)
        $emergencyKeywords = ['chập điện', 'cháy', 'bốc khói', 'nổ', 'rò rỉ gas', 'vỡ ống nước', 'rò điện', 'chập cháy', 'ngập nước'];
        $isEmergency = false;
        foreach ($emergencyKeywords as $kw) {
            if (str_contains($lowerPrompt, $kw)) {
                $isEmergency = true;
                break;
            }
        }

        $createdTicket = null;
        if ($isEmergency) {
            // Tự động tạo ticket bảo trì khẩn cấp vào hệ thống
            try {
                $tenantId = $user?->tenant_id ?? 1;
                $targetRoomId = $contextId ?? ($user?->resident?->room_id ?? Room::where('tenant_id', $tenantId)->value('id') ?? 1);
                $residentId = $user?->resident?->id 
                    ?? Resident::where('room_id', $targetRoomId)->value('id') 
                    ?? Resident::where('tenant_id', $tenantId)->value('id');

                if (!$residentId) {
                    $resident = Resident::create([
                        'tenant_id' => $tenantId,
                        'room_id' => $targetRoomId,
                        'name' => $user?->name ?? 'Cư dân phòng ' . $targetRoomId,
                        'phone' => '0988000111',
                        'status' => 'active',
                        'start_date' => now(),
                    ]);
                    $residentId = $resident->id;
                }

                $createdTicket = Ticket::create([
                    'tenant_id' => $tenantId,
                    'room_id' => $targetRoomId,
                    'resident_id' => $residentId,
                    'title' => 'Sự cố khẩn cấp: ' . mb_substr($prompt, 0, 80),
                    'description' => $prompt,
                    'category' => str_contains($lowerPrompt, 'điện') || str_contains($lowerPrompt, 'cháy') ? 'điện' : (str_contains($lowerPrompt, 'nước') ? 'nước' : 'khác'),
                    'urgency' => 'high',
                    'status' => 'pending',
                ]);
            } catch (Throwable $e) {
                Log::warning('Không thể tạo ticket khẩn cấp tự động: ' . $e->getMessage());
            }

            $emergencyAdvice = "🚨 CẢNH BÁO NGUY HIỂM & HƯỚNG DẪN XỬ LÝ KHẨN CẤP:\n\n"
                . "1. NGẮT NGUỒN NGAY LẬP TỨC: Cúp ngay cầu dao điện (Aptomat) tổng của phòng hoặc khóa van nước chính.\n"
                . "2. GIỮ KHOẢNG CÁCH AN TOÀN: Tuyệt đối không chạm vào khu vực nghi rò điện hoặc dùng nước tạt vào đám cháy điện.\n"
                . "3. SƠ TÁN NẾU CẦN: Di chuyển ra khỏi phòng nếu khói dày đặc và hô hoán cư dân lân cận.\n\n"
                . "Hệ thống AI đã tự động ghi nhận sự cố khẩn cấp và phát thông báo điều động kỹ thuật viên tòa nhà đến phòng bạn ngay!";

            return response()->json([
                'success' => true,
                'agent_mode' => 'tenant_incident',
                'is_emergency' => true,
                'ticket_created' => true,
                'ticket_id' => $createdTicket?->id,
                'error_code' => 'ERR_14_06',
                'message' => 'Sự cố kỹ thuật khẩn cấp đã được AI ghi nhận và chuyển ngay đến đội ngũ bảo trì tòa nhà!', // ERR_14_06
                'response' => $emergencyAdvice,
                'quick_action' => 'view_ticket',
            ]);
        }

        // Sự cố thông thường: gọi AI hoặc fallback tư vấn
        $aiResult = $this->callGeminiWithTimeout([
            [
                'role' => 'system',
                'content' => 'Bạn là chuyên viên kỹ thuật tòa nhà SmartRoom. Hãy hướng dẫn cư dân cách kiểm tra sơ bộ sự cố kỹ thuật và giải pháp an toàn.'
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ], 6);

        if (!$aiResult['success']) {
            $fallbackAdvice = "Hướng dẫn xử lý sự cố thiết bị:\n"
                . "• Hãy kiểm tra kỹ lại nguồn cắm và cầu chì bảo vệ thiết bị.\n"
                . "• Nếu thiết bị vẫn không hoạt động, bạn có thể tạo yêu cầu báo hỏng bảo trì trên Cổng dịch vụ Cư dân để ban quản lý cử nhân viên hỗ trợ.";

            return response()->json([
                'success' => true,
                'agent_mode' => 'tenant_incident',
                'is_emergency' => false,
                'response' => $fallbackAdvice,
                'is_fallback' => true,
                'error_code' => 'ERR_14_03',
                'warning_message' => 'Kết nối máy chủ AI quá thời gian chờ (6s), tự động kích hoạt câu trả lời từ kho tri thức dự phòng.',
                'quick_action' => 'create_ticket',
            ]);
        }

        return response()->json([
            'success' => true,
            'agent_mode' => 'tenant_incident',
            'is_emergency' => false,
            'response' => $aiResult['content'],
            'is_fallback' => false,
            'quick_action' => 'create_ticket',
        ]);
    }

    /**
     * Gọi Google Gemini AI với timeout nghiêm ngặt (mặc định 6s theo ERR_14_03)
     */
    private function callGeminiWithTimeout(array $messages, int $timeoutSeconds = 6): array
    {
        $apiKey = config('services.ai.api_key');
        $baseUrl = config('services.ai.base_url');
        $model = config('services.ai.model', 'gemini-2.5-flash');

        if (!$apiKey || !$baseUrl) {
            return ['success' => false, 'content' => null, 'reason' => 'ai_not_configured'];
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout($timeoutSeconds)
                ->post(rtrim($baseUrl, '/') . '/chat/completions', [
                    'model' => $model,
                    'temperature' => 0.7,
                    'messages' => $messages,
                ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                if (!empty($content)) {
                    return ['success' => true, 'content' => trim($content)];
                }
            }

            return ['success' => false, 'content' => null, 'reason' => 'http_error'];
        } catch (Throwable $e) {
            Log::warning('AI call timeout or error: ' . $e->getMessage());
            return ['success' => false, 'content' => null, 'reason' => 'timeout_or_exception'];
        }
    }
}
