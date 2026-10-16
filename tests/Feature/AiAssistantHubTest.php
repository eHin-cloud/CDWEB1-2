<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Role;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AiAssistantHubTest extends TestCase
{
    use RefreshDatabase;

    protected User $landlord;
    protected Tenant $tenant;
    protected Building $building;
    protected Room $room;

    protected function setUp(): void
    {
        parent::setUp();

        // Xóa sạch rate limiter trước mỗi test
        RateLimiter::clear('ai_hub_1');
        RateLimiter::clear('ai_hub_127.0.0.1');

        $role = Role::firstOrCreate(
            ['slug' => 'landlord'],
            ['name' => 'Chủ trọ']
        );

        $this->tenant = Tenant::create([
            'name' => 'Khu Căn Hộ Dịch Vụ SmartRoom',
            'email' => 'landlord@smartroom.vn',
            'phone' => '0988112233',
        ]);

        $this->landlord = User::create([
            'tenant_id' => $this->tenant->id,
            'role_id' => $role->id,
            'name' => 'Nguyễn Anh Quý',
            'username' => 'anhquy_ai_admin',
            'role' => 'landlord',
            'password' => bcrypt('password123'),
        ]);

        $this->building = Building::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Tòa Nhà Diamond',
            'address' => 'Số 12 Ngõ 105 Xuân Thủy, Cầu Giấy, Hà Nội',
            'total_floors' => 5,
        ]);

        $this->room = Room::create([
            'tenant_id' => $this->tenant->id,
            'building_id' => $this->building->id,
            'room_number' => '101',
            'floor' => 1,
            'status' => 'empty',
            'price' => 4500000,
            'area' => 30,
            'amenities' => ['Điều hòa', 'Bình nóng lạnh', 'Máy giặt', 'Ban công thoáng mát'],
        ]);
    }

    /**
     * ERR_14_01: Để trống câu hỏi khi gửi AI
     */
    public function test_err_14_01_empty_prompt_validation(): void
    {
        $response = $this->actingAs($this->landlord)
            ->postJson('/api/ai/assistant-hub', [
                'prompt' => '',
                'agent_mode' => 'landlord_advisor',
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('error_code', 'ERR_14_01')
            ->assertJsonPath('message', 'Vui lòng nhập câu hỏi hoặc yêu cầu cần trợ lý AI hỗ trợ.');
    }

    /**
     * ERR_14_02: Nội dung yêu cầu dưới 5 ký tự
     */
    public function test_err_14_02_prompt_under_5_chars_validation(): void
    {
        $response = $this->actingAs($this->landlord)
            ->postJson('/api/ai/assistant-hub', [
                'prompt' => 'Alo',
                'agent_mode' => 'room_finder',
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('error_code', 'ERR_14_02')
            ->assertJsonPath('message', 'Nội dung yêu cầu quá ngắn (tối thiểu 5 ký tự) để AI có đủ ngữ cảnh xử lý.');
    }

    /**
     * ERR_14_03: Kết nối máy chủ Gemini AI Timeout / Fallback từ kho tri thức
     */
    public function test_err_14_03_fallback_knowledge_when_ai_timeout(): void
    {
        // Khi không có API key hoặc timeout, hệ thống tự động fallback
        $response = $this->actingAs($this->landlord)
            ->postJson('/api/ai/assistant-hub', [
                'prompt' => 'Tìm phòng dưới 5 triệu có máy giặt gần Cầu Giấy',
                'agent_mode' => 'room_finder',
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('is_fallback', true)
            ->assertJsonPath('error_code', 'ERR_14_03')
            ->assertJsonPath('warning_message', 'Kết nối máy chủ AI quá thời gian chờ (6s), tự động kích hoạt câu trả lời từ kho tri thức dự phòng.');
    }

    /**
     * ERR_14_04: Vượt quá giới hạn tần suất truy vấn (tối đa 20 req/phút)
     */
    public function test_err_14_04_rate_limit_exceeded(): void
    {
        $rateKey = 'ai_hub_' . $this->landlord->id;
        RateLimiter::clear($rateKey);

        // Gửi 20 request hợp lệ
        for ($i = 0; $i < 20; $i++) {
            RateLimiter::hit($rateKey, 60);
        }

        // Request thứ 21 sẽ bị chặn 429
        $response = $this->actingAs($this->landlord)
            ->postJson('/api/ai/assistant-hub', [
                'prompt' => 'Kiểm tra tần suất truy vấn gửi AI',
                'agent_mode' => 'room_finder',
            ]);

        $response->assertStatus(429)
            ->assertJsonPath('success', false)
            ->assertJsonPath('error_code', 'ERR_14_04')
            ->assertJsonPath('message', 'Bạn đã gửi quá nhiều yêu cầu trong thời gian ngắn (tối đa 20 req/phút). Vui lòng đợi 30 giây!');

        RateLimiter::clear($rateKey);
    }

    /**
     * ERR_14_05: Soạn bài đăng SEO & thông điệp sao chép thành công
     */
    public function test_err_14_05_landlord_advisor_generates_seo_post(): void
    {
        $response = $this->actingAs($this->landlord)
            ->postJson('/api/ai/landlord-advisor', [
                'prompt' => 'Soạn bài đăng phòng 101 chuẩn SEO',
                'context_id' => $this->room->id,
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('agent_mode', 'landlord_advisor')
            ->assertJsonPath('quick_action', 'copy_content')
            ->assertJsonPath('copy_success_message', 'Đã sao chép nội dung bài viết marketing chuẩn SEO vào bộ nhớ tạm thành công!');
    }

    /**
     * ERR_14_06: AI phân loại sự cố khẩn cấp và tự động tạo ticket kỹ thuật
     */
    public function test_err_14_06_incident_triage_emergency_ticket_creation(): void
    {
        $response = $this->actingAs($this->landlord)
            ->postJson('/api/ai/incident-triage', [
                'prompt' => 'Chập điện bốc khói khét lẹt ở ổ cắm điện gần bếp',
                'context_id' => $this->room->id,
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('is_emergency', true)
            ->assertJsonPath('ticket_created', true)
            ->assertJsonPath('error_code', 'ERR_14_06')
            ->assertJsonPath('message', 'Sự cố kỹ thuật khẩn cấp đã được AI ghi nhận và chuyển ngay đến đội ngũ bảo trì tòa nhà!');

        // Xác minh ticket khẩn cấp đã được lưu vào database
        $this->assertDatabaseHas('tickets', [
            'tenant_id' => $this->tenant->id,
            'urgency' => 'high',
            'status' => 'pending',
        ]);
    }

    /**
     * Kiểm tra giao diện Admin Dashboard render đầy đủ UI elements FEAT_14
     */
    public function test_admin_dashboard_renders_ai_assistant_hub(): void
    {
        $response = $this->actingAs($this->landlord)
            ->get(route('smartroom.admin'));

        $response->assertOk()
            ->assertSee('ai-assistant-hub-container', false)
            ->assertSee('ai-hub-prompt', false)
            ->assertSee('btn-send-ai-hub', false)
            ->assertSee('ai-hub-response-box', false)
            ->assertSee('modal-ai-emergency-ticket', false)
            ->assertSee('Trợ Lý AI Hỗ Trợ Toàn Diện', false)
            ->assertSee('Sự cố kỹ thuật khẩn cấp đã được AI ghi nhận và chuyển ngay đến đội ngũ bảo trì tòa nhà!', false);
    }
}
