<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Room;
use App\Models\Tenant;
use App\Services\HybridSearchService;
use App\Services\VietnameseTokenizerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HybridSearchRrfTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected Building $building;
    protected Room $roomExactKeyword;
    protected Room $roomSemanticIntent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name' => 'Demo Landlord',
            'email' => 'landlord@test.com',
        ]);

        $this->building = Building::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Renty Cầu Giấy Complex',
            'address' => 'Số 50 Cầu Giấy, Hà Nội',
            'description' => 'Tòa nhà văn minh, an ninh cao, gần các trường đại học.',
        ]);

        // Phòng A: Khớp chính xác mã/tiện ích từ khoá "máy giặt ban công" (Điểm mạnh của Sparse BM25)
        $this->roomExactKeyword = Room::create([
            'tenant_id' => $this->tenant->id,
            'building_id' => $this->building->id,
            'room_number' => '102',
            'floor' => 1,
            'price' => 3200000,
            'status' => 'empty',
            'description' => 'Phòng có máy giặt riêng và ban công thoáng gió',
            'amenities' => ['máy giặt', 'ban công', 'khép kín'],
        ]);

        // Phòng B: Khớp ngữ nghĩa "tiện nghi cao cấp cho sinh viên" (Điểm mạnh của Dense Semantic)
        $this->roomSemanticIntent = Room::create([
            'tenant_id' => $this->tenant->id,
            'building_id' => $this->building->id,
            'room_number' => '205',
            'floor' => 2,
            'price' => 2800000,
            'status' => 'empty',
            'description' => 'Phòng giá rẻ cho sinh viên tiết kiệm, sạch sẽ thoáng mát',
            'amenities' => ['gác lửng', 'điều hòa'],
        ]);
    }

    /**
     * Test trực tiếp HybridSearchService với thuật toán RRF (k = 60)
     */
    public function test_hybrid_search_service_computes_rrf_and_dual_ranks(): void
    {
        $service = app(HybridSearchService::class);

        $rooms = collect([
            [
                'id' => 1,
                'room_number' => '102',
                'title' => 'Phòng 102 Cầu Giấy',
                'building_name' => 'Renty Cầu Giấy',
                'address' => 'Số 50 Cầu Giấy',
                'area_name' => 'Cầu Giấy',
                'price' => 3200000,
                'amenities' => ['máy giặt', 'ban công'],
                'description' => 'Phòng đẹp có máy giặt và ban công',
                'location_description' => 'Gần đại học',
            ],
            [
                'id' => 2,
                'room_number' => '205',
                'title' => 'Phòng 205 Sinh Viên',
                'building_name' => 'Renty Cầu Giấy',
                'address' => 'Số 50 Cầu Giấy',
                'area_name' => 'Cầu Giấy',
                'price' => 2800000,
                'amenities' => ['gác lửng'],
                'description' => 'Phòng sinh viên giá rẻ',
                'location_description' => 'Gần bến xe',
            ],
        ]);

        $results = $service->search('máy giặt ban công', $rooms, ['rrf_k' => 60]);

        $this->assertNotEmpty($results);
        $topRoom = $results->first();

        // Phòng số 1 chứa exact keywords phải được xếp hạng cao nhất
        $this->assertEquals(1, $topRoom['id']);
        $this->assertEquals('hybrid_rrf', $topRoom['retrieval_method']);
        $this->assertGreaterThan(0, $topRoom['rrf_score']);
        $this->assertEquals(1, $topRoom['sparse_rank']);
        $this->assertArrayHasKey('dense_rank', $topRoom);
        $this->assertArrayHasKey('vietnamese_tokens', $topRoom);
    }

    /**
     * Test API endpoint /api/renty/rooms/smart-search trả về metadata Hybrid Search & RRF
     */
    public function test_smart_search_api_returns_hybrid_search_metadata(): void
    {
        $response = $this->getJson('/api/renty/rooms/smart-search?q=máy giặt ban công');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'original_query',
                'hybrid_search' => [
                    'enabled',
                    'retrieval_architecture',
                    'fusion_algorithm',
                    'rrf_k',
                    'vietnamese_tokens',
                    'vietnamese_compounds',
                ],
                'rooms',
            ]);

        $hybridMeta = $response->json('hybrid_search');
        $this->assertTrue($hybridMeta['enabled']);
        $this->assertEquals('dual_sparse_dense', $hybridMeta['retrieval_architecture']);
        $this->assertEquals('reciprocal_rank_fusion_rrf', $hybridMeta['fusion_algorithm']);
        $this->assertEquals(60, $hybridMeta['rrf_k']);

        // Kiểm tra token tách từ Viet74K có chứa từ ghép
        $this->assertContains('máy giặt', $hybridMeta['vietnamese_tokens']);
        $this->assertContains('ban công', $hybridMeta['vietnamese_tokens']);
    }
}
