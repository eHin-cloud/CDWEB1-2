<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class SmsZaloService
{
    protected string $mode;
    protected ?string $oaId;
    protected ?string $accessToken;
    protected ?string $paymentTemplateId;

    public function __construct()
    {
        $this->mode = config('services.zalo.mode', 'sandbox');
        $this->oaId = config('services.zalo.oa_id');
        $this->accessToken = config('services.zalo.access_token');
        $this->paymentTemplateId = config('services.zalo.template_payment');
    }

    /**
     * Gửi tin nhắn SMS qua API Gateway (ghi log vào sms_zalo channel).
     */
    public function sendSms(string $phone, string $message): bool
    {
        Log::channel('sms_zalo')->info("SMS Dispatched to {$phone}: {$message}");
        return true;
    }

    /**
     * Gửi tin nhắn Zalo ZNS (Zalo Notification Service).
     * Tự động điều hướng giữa Sandbox Mock và Live API tùy theo cấu hình ZALO_MODE.
     */
    public function sendZalo(string $phone, string $message, array $templateData = []): array
    {
        // Chuẩn hóa định dạng số điện thoại (ví dụ: 090xxx -> 8490xxx)
        $formattedPhone = $this->formatPhoneNumber($phone);

        if ($this->mode === 'sandbox' || empty($this->accessToken)) {
            // Chế độ Sandbox / Mock: Ghi log chi tiết và giả lập phản hồi thành công
            Log::channel('sms_zalo')->info("Zalo ZNS [SANDBOX MOCK] Dispatched to {$formattedPhone}", [
                'phone' => $formattedPhone,
                'message' => $message,
                'template_data' => $templateData,
                'mode' => 'sandbox',
            ]);

            return [
                'success' => true,
                'mode' => 'sandbox',
                'message' => 'Giả lập gửi Zalo ZNS thành công (Sandbox mode)',
                'recipient' => $formattedPhone,
            ];
        }

        // Chế độ Live: Gọi Zalo Cloud API thật
        try {
            $templateId = $this->paymentTemplateId;
            $payload = [
                'phone' => $formattedPhone,
                'template_id' => $templateId,
                'template_data' => !empty($templateData) ? $templateData : [
                    'customer_name' => 'Cư dân',
                    'message' => $message,
                ],
                'tracking_id' => 'smartroom_' . uniqid(),
            ];

            $response = Http::withHeaders([
                'access_token' => $this->accessToken,
                'Content-Type' => 'application/json',
            ])->timeout(10)->post('https://business.openapi.zalo.me/message/template', $payload);

            $result = $response->json();

            if ($response->successful() && isset($result['error']) && (int) $result['error'] === 0) {
                Log::info("Zalo ZNS Live sent successfully to {$formattedPhone}", $result);
                return [
                    'success' => true,
                    'mode' => 'live',
                    'data' => $result['data'] ?? null,
                ];
            }

            Log::error("Zalo ZNS Live API error for {$formattedPhone}", [
                'status' => $response->status(),
                'response' => $result,
            ]);

            return [
                'success' => false,
                'mode' => 'live',
                'error' => $result['message'] ?? 'Lỗi không xác định từ Zalo API',
                'error_code' => $result['error'] ?? -1,
            ];
        } catch (Throwable $exception) {
            Log::error("Zalo ZNS exception for {$formattedPhone}: " . $exception->getMessage());
            return [
                'success' => false,
                'mode' => 'live',
                'error' => $exception->getMessage(),
            ];
        }
    }

    /**
     * Gửi mẫu nhắc nợ ZNS có cấu trúc dữ liệu.
     */
    public function sendPaymentReminder(
        string $phone,
        string $customerName,
        string $roomNumber,
        string $billingMonth,
        int $totalAmount,
        ?string $vietQrUrl = null
    ): array {
        $message = "Kính gửi {$customerName}, phòng {$roomNumber} có hóa đơn tháng {$billingMonth} "
            . "cần thanh toán: " . number_format($totalAmount) . " đ. Hạn nộp: trước ngày 10.";

        $templateData = [
            'customer_name' => $customerName,
            'room_number' => $roomNumber,
            'billing_month' => $billingMonth,
            'total_amount' => number_format($totalAmount) . ' đ',
            'payment_url' => $vietQrUrl ?: '',
        ];

        return $this->sendZalo($phone, $message, $templateData);
    }

    /**
     * Chuẩn hóa số điện thoại theo chuẩn quốc tế 84xxxxxxxxx cho Zalo.
     */
    protected function formatPhoneNumber(string $phone): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($cleaned, '0')) {
            return '84' . substr($cleaned, 1);
        }
        return $cleaned;
    }
}
