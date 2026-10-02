<?php

namespace Tests\Feature;

use App\Models\Room;
use Tests\TestCase;

class RoomComparisonTest extends TestCase
{
    /**
     * Test so sánh phòng thành công với 2-3 phòng hợp lệ
     */
    public function test_compare_rooms_success(): void
    {
        $rooms = Room::take(2)->get();
        if ($rooms->count() < 2) {
            $this->markTestSkipped('Cần ít nhất 2 phòng trong database để test so sánh.');
        }

        $roomIds = $rooms->pluck('id')->toArray();

        $response = $this->postJson('/api/renty/rooms/compare', [
            'room_ids' => $roomIds,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'comparison' => [
                    '*' => [
                        'id',
                        'room_number',
                        'price',
                        'price_formatted',
                        'price_per_m2',
                        'price_per_m2_formatted',
                        'area',
                        'deposit',
                        'estimated_monthly_total',
                        'building_name',
                        'address',
                        'rating_avg',
                        'reviews_count',
                        'badges' => [
                            'best_price',
                            'largest_area',
                            'top_rated',
                        ],
                        'amenities_checklist',
                        'radar_scores',
                    ],
                ],
            ]);

        $this->assertCount(2, $response->json('comparison'));
    }

    /**
     * Test chặn khi không truyền danh sách phòng
     */
    public function test_compare_rooms_fails_when_empty(): void
    {
        $response = $this->postJson('/api/renty/rooms/compare', [
            'room_ids' => [],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['room_ids']);
    }

    /**
     * Test chặn khi truyền vượt quá 3 phòng (theo kịch bản tối đa 3 phòng)
     */
    public function test_compare_rooms_fails_when_more_than_3(): void
    {
        $response = $this->postJson('/api/renty/rooms/compare', [
            'room_ids' => [1, 2, 3, 4],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['room_ids']);
    }

    /**
     * Test chặn khi truyền ID phòng không tồn tại
     */
    public function test_compare_rooms_fails_when_room_not_exists(): void
    {
        $response = $this->postJson('/api/renty/rooms/compare', [
            'room_ids' => [999999, 999998],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['room_ids.0', 'room_ids.1']);
    }

    /**
     * Test endpoint AI so sánh phòng trả về phân tích đối chiếu hợp lệ
     */
    public function test_compare_rooms_ai_endpoint_success(): void
    {
        $rooms = Room::take(2)->get();
        if ($rooms->count() < 2) {
            $this->markTestSkipped('Cần ít nhất 2 phòng trong database để test so sánh AI.');
        }

        $roomIds = $rooms->pluck('id')->toArray();

        $response = $this->postJson('/api/renty/rooms/compare-ai', [
            'room_ids' => $roomIds,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'insight' => [
                    'summary',
                    'best_economic',
                    'best_space',
                    'recommendations',
                    'verdict',
                ],
            ]);
    }
}
