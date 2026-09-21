<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmartSearchApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $tenant = \App\Models\Tenant::create([
            'name' => 'Demo Tenant',
            'email' => 'tenant@example.com',
        ]);

        $b1 = \App\Models\Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Tòa Nhà Cầu Giấy',
            'address' => 'Số 10 Cầu Giấy, Hà Nội',
        ]);

        \App\Models\Room::create([
            'tenant_id' => $tenant->id,
            'building_id' => $b1->id,
            'room_number' => '101',
            'floor' => 1,
            'price' => 3500000,
            'status' => 'empty',
            'description' => 'Phòng đẹp khép kín Cầu Giấy',
        ]);

        $b2 = \App\Models\Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Tòa Nhà Thanh Xuân',
            'address' => 'Số 20 Nguyễn Trãi, Thanh Xuân, Hà Nội',
        ]);

        \App\Models\Room::create([
            'tenant_id' => $tenant->id,
            'building_id' => $b2->id,
            'room_number' => '201',
            'floor' => 2,
            'price' => 3800000,
            'status' => 'empty',
            'description' => 'Phòng khép kín Thanh Xuân có ban công',
        ]);
    }

    /**
     * Test API tìm kiếm thông thường với địa danh chính xác
     */
    public function test_smart_search_with_valid_location(): void
    {
        $response = $this->getJson('/api/renty/rooms/smart-search?q=Cầu Giấy');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'original_query',
                'corrected_query',
                'did_you_mean',
                'has_correction',
                'recognized_terms',
                'filters',
                'count',
                'rooms',
                'suggestions',
            ]);
    }

    /**
     * Test tự động phát hiện lỗi chính tả / gõ sai Telex và sinh gợi ý "Did You Mean"
     */
    public function test_smart_search_detects_and_corrects_typos(): void
    {
        // Gõ nhầm 'cau giya' thay vì 'Cầu Giấy'
        $response = $this->getJson('/api/renty/rooms/smart-search?q=cau giya');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'has_correction' => true,
                'did_you_mean' => 'Cầu Giấy',
            ]);

        $this->assertNotEmpty($response->json('rooms'));

        // Gõ nhầm 'thah xuan' thay vì 'Thanh Xuân'
        $response2 = $this->getJson('/api/renty/rooms/smart-search?q=thah xuan');

        $response2->assertStatus(200)
            ->assertJson([
                'success' => true,
                'has_correction' => true,
                'did_you_mean' => 'Thanh Xuân',
            ]);

        $this->assertNotEmpty($response2->json('rooms'));
    }

    /**
     * Test phân tích ngôn ngữ tự nhiên bóc tách giá (VD: dưới 4 triệu)
     */
    public function test_smart_search_parses_natural_price_query(): void
    {
        $response = $this->getJson('/api/renty/rooms/smart-search?q=phong duoi 4 trieu');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertEquals(4000000, $response->json('filters.max_price'));

        // Kiểm tra các phòng trả về đều có giá <= 4.000.000
        $rooms = $response->json('rooms');
        foreach ($rooms as $room) {
            $this->assertLessThanOrEqual(4000000, $room['price']);
        }
    }

    /**
     * Test API gợi ý nhanh (Autocomplete / Quick suggestions)
     */
    public function test_smart_search_suggestions_endpoint(): void
    {
        $response = $this->getJson('/api/renty/rooms/suggest?q=khep kn');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'has_correction' => true,
                'did_you_mean' => 'Khép kín',
            ]);

        // Gợi ý mặc định khi chưa nhập gì
        $emptyResponse = $this->getJson('/api/renty/rooms/suggest');
        $emptyResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
        $this->assertNotEmpty($emptyResponse->json('suggestions'));
    }
}
