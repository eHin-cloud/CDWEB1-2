<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SmartSearchFeature21Test extends TestCase
{
    use DatabaseTransactions;

    protected Tenant $tenant;
    protected Building $building;
    protected Room $room1;
    protected Room $room2;
    protected Room $room3;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name' => 'Renty System Tenant',
            'email' => 'renty_admin@smartroom.local',
        ]);

        $this->building = Building::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Renty Smart Building Cầu Giấy',
            'address' => '123 Cầu Giấy, Hà Nội',
        ]);

        // Phòng 1: Giá 3.000.000, diện tích 25m2, có Máy lạnh và Máy giặt
        $this->room1 = Room::create([
            'tenant_id' => $this->tenant->id,
            'building_id' => $this->building->id,
            'room_number' => '101',
            'floor' => 1,
            'price' => 3000000,
            'area' => 25,
            'status' => 'empty',
            'description' => 'Phòng full đồ có máy giặt và điều hòa',
            'amenities' => ['air_conditioner', 'washing_machine', 'wc'],
        ]);

        // Phòng 2: Giá 5.000.000, diện tích 35m2, chỉ có Máy lạnh
        $this->room2 = Room::create([
            'tenant_id' => $this->tenant->id,
            'building_id' => $this->building->id,
            'room_number' => '201',
            'floor' => 2,
            'price' => 5000000,
            'area' => 35,
            'status' => 'empty',
            'description' => 'Phòng rộng thoáng chỉ có máy lạnh',
            'amenities' => ['air_conditioner', 'balcony'],
        ]);

        // Phòng 3: Giá 2.500.000, diện tích 20m2, có gác lửng
        $this->room3 = Room::create([
            'tenant_id' => $this->tenant->id,
            'building_id' => $this->building->id,
            'room_number' => '301',
            'floor' => 3,
            'price' => 2500000,
            'area' => 20,
            'status' => 'empty',
            'description' => 'Phòng gác lửng giá rẻ',
            'amenities' => ['loft', 'wc'],
        ]);
    }

    /**
     * Test web endpoint /renty/search and /search redirect
     */
    public function test_web_search_endpoints(): void
    {
        $response = $this->get('/renty/search');
        $response->assertStatus(200);

        $redirect = $this->get('/search');
        $redirect->assertRedirect(route('renty.search'));
    }

    /**
     * DoD 1: Nhập min_price > max_price (Ví dụ: min = 8,000,000, max = 3,000,000)
     * Mong đợi: Lỗi ERR_21_01 với message 'Giá tối thiểu không thể lớn hơn giá tối đa.'
     */
    public function test_error_err_21_01_when_min_price_greater_than_max_price(): void
    {
        $response = $this->getJson('/api/renty/filter?min_price=8000000&max_price=3000000');

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'error_code' => 'ERR_21_01',
            'message' => 'Giá tối thiểu không thể lớn hơn giá tối đa.',
        ]);
    }

    /**
     * Test lỗi ERR_21_02: min_area > max_area
     */
    public function test_error_err_21_02_when_min_area_greater_than_max_area(): void
    {
        $response = $this->getJson('/api/renty/filter?min_area=40&max_area=20');

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'error_code' => 'ERR_21_02',
            'message' => 'Diện tích tối thiểu không thể lớn hơn diện tích tối đa.',
        ]);
    }

    /**
     * Test lỗi ERR_21_03: Nhập ký tự chữ vào ô khoảng giá hoặc diện tích
     */
    public function test_error_err_21_03_when_non_numeric_price_or_area_input(): void
    {
        $responsePrice = $this->getJson('/api/renty/filter?min_price=abc');
        $responsePrice->assertStatus(422);
        $responsePrice->assertJson([
            'success' => false,
            'error_code' => 'ERR_21_03',
            'message' => 'Giá phòng và diện tích chỉ được nhập số.',
        ]);

        $responseArea = $this->getJson('/api/renty/filter?max_area=xyz');
        $responseArea->assertStatus(422);
        $responseArea->assertJson([
            'success' => false,
            'error_code' => 'ERR_21_03',
            'message' => 'Giá phòng và diện tích chỉ được nhập số.',
        ]);
    }

    /**
     * DoD 2: Chọn tiện ích 'Máy giặt' và 'Máy lạnh' (air_conditioner và washing_machine)
     * Mong đợi: Chỉ trả về phòng có cả hai tiện ích trên (ở đây là room1)
     */
    public function test_filter_by_multiple_amenities(): void
    {
        $response = $this->getJson('/api/renty/filter?amenities=air_conditioner,washing_machine');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'count' => 1,
        ]);

        $rooms = $response->json('rooms');
        $this->assertCount(1, $rooms);
        $this->assertEquals($this->room1->id, $rooms[0]['id']);
    }

    /**
     * DoD 3: Thiết lập khoảng giá chuẩn từ 2tr đến 4tr
     * Mong đợi: Trả về room1 (3tr) và room3 (2.5tr), loại trừ room2 (5tr)
     */
    public function test_filter_by_valid_price_range(): void
    {
        $response = $this->getJson('/api/renty/filter?min_price=2000000&max_price=4000000');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'count' => 2,
        ]);

        $roomIds = collect($response->json('rooms'))->pluck('id')->all();
        $this->assertContains($this->room1->id, $roomIds);
        $this->assertContains($this->room3->id, $roomIds);
        $this->assertNotContains($this->room2->id, $roomIds);
    }

    /**
     * Test ERR_21_04: Không tìm thấy phòng nào khớp bộ lọc
     */
    public function test_empty_state_err_21_04_when_no_rooms_match(): void
    {
        $response = $this->getJson('/api/renty/filter?min_price=90000000&max_price=100000000');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'count' => 0,
            'rooms' => [],
            'error_code' => 'ERR_21_04',
            'message' => 'Không tìm thấy phòng nào phù hợp với bộ lọc bạn đã chọn.',
        ]);
    }

    /**
     * Test phân trang của API /api/renty/filter
     */
    public function test_filter_api_supports_pagination(): void
    {
        $response = $this->getJson('/api/renty/filter?per_page=1&page=1');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'count' => 1,
            'total' => 3,
            'page' => 1,
            'per_page' => 1,
            'total_pages' => 3,
        ]);
        $this->assertCount(1, $response->json('rooms'));
    }
}
