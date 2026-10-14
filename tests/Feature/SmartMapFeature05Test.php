<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Role;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature Test Suite: TÍNH NĂNG 05 - BẢN ĐỒ PHÒNG TRỌ THÔNG MINH (FEAT_05_SMART_MAP)
 * Thành viên phụ trách: Nguyễn Thanh Hiền
 * Spec: BaoCao/NhomC_baocao_2026102.docx (Table 46 -> Table 51)
 */
class SmartMapFeature05Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['slug' => 'guest'], ['name' => 'Khách Vãng Lai']);
        Role::firstOrCreate(['slug' => 'tenant'], ['name' => 'Khách Thuê']);
        Role::firstOrCreate(['slug' => 'landlord'], ['name' => 'Chủ Trọ']);
        Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);
    }

    protected function createRoom(array $attrs = []): Room
    {
        return Room::create(array_merge([
            'floor' => 1,
            'price' => 3200000,
            'area' => 25,
            'status' => 'empty',
        ], $attrs));
    }

    /**
     * Kịch bản 1: Mở trang bản đồ thông minh (Table 47 & 50 - DoD STT 1)
     * URL: GET /renty/map hoặc /renty?view=map
     */
    public function test_smart_map_page_renders_with_ui_elements(): void
    {
        $tenant = Tenant::create([
            'name' => 'Tòa Nhà Xanh Cầu Giấy',
            'email' => 'landlord_map@example.com',
            'listing_badge' => 'verified',
        ]);

        $building = Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Cơ sở Cầu Giấy 1',
            'address' => 'Số 123 Đường Cầu Giấy, Hà Nội',
        ]);

        $this->createRoom([
            'tenant_id' => $tenant->id,
            'building_id' => $building->id,
            'room_number' => '101',
        ]);

        // 1. Kiểm tra route /renty/map
        $response = $this->get('/renty/map');
        $response->assertStatus(200);

        // STT 1: Map Canvas (map_view)
        $response->assertSee('map_view');
        $response->assertSee('renty-interactive-map');

        // STT 2: Side Panel (room_list_panel)
        $response->assertSee('room_list_panel');

        // STT 3: Radius Slider (range_radius)
        $response->assertSee('range_radius');

        // 2. Kiểm tra redirect từ /map sang /renty/map
        $responseRedirect = $this->get('/map');
        $responseRedirect->assertRedirect('/renty/map');
    }

    /**
     * Kịch bản 2: API lấy danh sách Marker phòng trọ trên bản đồ (Table 47: GET /api/renty/rooms/map)
     */
    public function test_api_rooms_map_returns_valid_markers(): void
    {
        $tenant = Tenant::create(['name' => 'Tenant Map', 'email' => 'tenant_map@example.com']);
        $building = Building::create(['tenant_id' => $tenant->id, 'name' => 'Tòa Nhà Bản Đồ', 'address' => 'Hà Nội']);
        $this->createRoom([
            'tenant_id' => $tenant->id,
            'building_id' => $building->id,
            'room_number' => '201',
            'price' => 3000000,
            'area' => 24,
            'status' => 'empty',
        ]);

        $response = $this->getJson('/api/renty/rooms/map');
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonStructure([
            'success',
            'markers' => [
                '*' => [
                    'room_id',
                    'room_number',
                    'price',
                    'area',
                    'building_name',
                    'address',
                    'latitude',
                    'longitude',
                ]
            ]
        ]);
    }
}
