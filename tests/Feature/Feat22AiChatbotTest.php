<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\ChatbotHistory;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Feat22AiChatbotTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('SQLite driver is required for isolated tests.');
        }

        parent::setUp();
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
    }

    /**
     * DoD 1: Để ô chat rỗng và nhấn nút Gửi -> Báo lỗi đỏ ERR_22_01
     */
    public function test_dod1_empty_chat_message_returns_err_22_01_validation_error(): void
    {
        // Gửi chuỗi rỗng
        $response = $this->postJson('/renty/chatbot/chat', [
            'message' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('error_code', 'ERR_22_01')
            ->assertJsonPath('message', 'Vui lòng nhập nội dung câu hỏi trước khi gửi.');

        // Gửi chỉ toàn khoảng trắng
        $responseWhitespace = $this->postJson('/renty/chatbot/chat', [
            'message' => '    ',
        ]);

        $responseWhitespace->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('error_code', 'ERR_22_01')
            ->assertJsonPath('message', 'Vui lòng nhập nội dung câu hỏi trước khi gửi.');
    }

    /**
     * DoD 2: Gửi câu hỏi 'Tìm phòng Thủ Đức dưới 3 triệu' -> Chatbot trả lời kèm các liên kết phòng trọ thỏa mãn
     */
    public function test_dod2_search_thu_duc_under_3m_returns_matching_room_cards(): void
    {
        $tenant = Tenant::create([
            'name' => 'Renty HCM Tenant',
            'email' => 'renty-hcm-test@example.test',
        ]);

        $buildingThuDuc = Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Chung cư mini Renty Thủ Đức Central',
            'address' => 'Số 18 Võ Văn Ngân, TP. Thủ Đức, TP. Hồ Chí Minh',
            'description' => 'Gần ĐH Sư Phạm Kỹ Thuật, ngã tư Thủ Đức.',
        ]);

        $buildingCauGiay = Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Renty Cầu Giấy',
            'address' => 'Cầu Giấy, Hà Nội',
            'description' => 'Khu Cầu Giấy Hà Nội.',
        ]);

        // Phòng 1: Thủ Đức, 2.5 triệu (thỏa mãn)
        $room1 = Room::create([
            'tenant_id' => $tenant->id,
            'building_id' => $buildingThuDuc->id,
            'room_number' => 'TD-101',
            'floor' => 1,
            'status' => 'empty',
            'room_type' => 'studio',
            'price' => 2500000,
            'area' => 22,
            'amenities' => ['ban công', 'wc'],
            'description' => 'Phòng studio Thủ Đức thoáng mát.',
        ]);

        // Phòng 2: Thủ Đức, 2.8 triệu (thỏa mãn)
        $room2 = Room::create([
            'tenant_id' => $tenant->id,
            'building_id' => $buildingThuDuc->id,
            'room_number' => 'TD-102',
            'floor' => 1,
            'status' => 'empty',
            'room_type' => 'standard',
            'price' => 2800000,
            'area' => 25,
            'amenities' => ['ban công', 'wc', 'gác lửng'],
            'description' => 'Phòng tiêu chuẩn Thủ Đức.',
        ]);

        // Phòng 3: Thủ Đức nhưng giá 4 triệu (vượt quá 3 triệu, không được trả về)
        Room::create([
            'tenant_id' => $tenant->id,
            'building_id' => $buildingThuDuc->id,
            'room_number' => 'TD-301',
            'floor' => 3,
            'status' => 'empty',
            'room_type' => 'vip',
            'price' => 4000000,
            'area' => 35,
            'amenities' => ['ban công', 'wc'],
            'description' => 'Phòng VIP Thủ Đức giá 4 triệu.',
        ]);

        // Phòng 4: Cầu Giấy Hà Nội giá 2.5 triệu (khác khu vực, không được trả về)
        Room::create([
            'tenant_id' => $tenant->id,
            'building_id' => $buildingCauGiay->id,
            'room_number' => 'CG-101',
            'floor' => 1,
            'status' => 'empty',
            'room_type' => 'standard',
            'price' => 2500000,
            'area' => 20,
            'amenities' => ['wc'],
            'description' => 'Phòng Cầu Giấy giá rẻ.',
        ]);

        config()->set('services.ai.api_key', null);

        $response = $this->postJson('/renty/chatbot/chat', [
            'message' => 'Tìm phòng Thủ Đức dưới 3 triệu',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $rooms = $response->json('rooms');
        $this->assertNotEmpty($rooms, 'Chatbot phải trả về danh sách phòng thỏa mãn.');
        $this->assertCount(2, $rooms, 'Chỉ có 2 phòng Thủ Đức có giá <= 3 triệu.');

        // Kiểm tra tất cả phòng trả về đều có giá <= 3.000.000 và nằm ở Thủ Đức
        foreach ($rooms as $room) {
            $this->assertLessThanOrEqual(3000000, $room['price']);
            $this->assertStringContainsString('Thủ Đức', $room['title'] . ' ' . $room['address']);
        }
    }

    /**
     * Bảng 4 - Mã lỗi ERR_22_03: Câu hỏi không liên quan đến thuê phòng
     */
    public function test_irrelevant_question_returns_err_22_03(): void
    {
        $response = $this->postJson('/renty/chatbot/chat', [
            'message' => 'Thời tiết hôm nay thế nào?',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('error_code', 'ERR_22_03')
            ->assertJsonPath('message', 'Trợ lý ảo Renty chỉ hỗ trợ tư vấn thông tin thuê phòng và tiện ích lưu trú.')
            ->assertJsonPath('response', 'Trợ lý ảo Renty chỉ hỗ trợ tư vấn thông tin thuê phòng và tiện ích lưu trú.')
            ->assertJsonPath('rooms', []);

        $responseMath = $this->postJson('/renty/chatbot/chat', [
            'message' => '1 + 1 bằng mấy',
        ]);

        $responseMath->assertOk()
            ->assertJsonPath('error_code', 'ERR_22_03');
    }

    /**
     * Kiểm tra endpoint GET /renty/chatbot/history
     */
    public function test_chatbot_history_requires_auth_and_returns_saved_history(): void
    {
        // 1. Chưa đăng nhập -> 401
        $guestResponse = $this->getJson('/renty/chatbot/history');
        $guestResponse->assertStatus(401);

        // 2. Đăng nhập người dùng
        $user = User::factory()->create();

        // Tạo 1 bản ghi lịch sử chat cho user này
        ChatbotHistory::create([
            'user_id' => $user->id,
            'message' => 'Tìm phòng Cầu Giấy dưới 4 triệu',
            'response' => 'Renty AI tìm thấy phòng phù hợp...',
            'matched_room_ids' => [1, 2],
            'used_ai' => false,
        ]);

        $authResponse = $this->actingAs($user, 'api')->getJson('/renty/chatbot/history');

        $authResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.0.message', 'Tìm phòng Cầu Giấy dưới 4 triệu')
            ->assertJsonPath('data.0.user_id', $user->id);
    }

    /**
     * Kiểm tra tương thích ngược với field 'prompt'
     */
    public function test_backward_compatibility_with_prompt_field(): void
    {
        $response = $this->postJson('/renty/chatbot/chat', [
            'prompt' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('error_code', 'ERR_22_01');
    }
}
