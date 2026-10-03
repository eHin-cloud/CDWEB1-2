<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\IotDevice;
use App\Models\IotMeterTelemetry;
use App\Models\Role;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UtilityRecord;
use App\Services\IotSmartMeteringService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IotSmartMeteringTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $landlord;
    protected Room $room;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name' => 'Ký Túc Xá Công Nghệ Số',
            'email' => 'techhouse@example.com',
            'phone' => '0912345678',
        ]);

        $roleLandlord = Role::firstOrCreate(
            ['slug' => 'landlord'],
            ['name' => 'Landlord', 'description' => 'Chủ cơ sở lưu trú']
        );

        $this->landlord = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'role_id' => $roleLandlord->id,
        ]);

        $building = Building::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Tòa Nhà Thông Minh A',
            'address' => '123 Đường Công Nghệ, Q.9, TP.HCM',
        ]);

        $this->room = Room::create([
            'tenant_id' => $this->tenant->id,
            'building_id' => $building->id,
            'room_number' => '101',
            'floor' => 1,
            'price' => 3500000,
            'status' => 'occupied',
            'electric_meter_serial' => 'EM-IOT-101',
            'water_meter_serial' => 'WM-IOT-101',
        ]);
    }

    public function test_can_ingest_telemetry_via_api_and_auto_link_to_room(): void
    {
        $payload = [
            'meter_serial' => 'EM-IOT-101',
            'meter_type' => 'electricity',
            'protocol' => 'esp32_wifi',
            'reading' => 1250.75,
            'voltage' => 221.4,
            'current' => 3.25,
            'power' => 719.55,
            'signal_quality' => -68,
        ];

        $response = $this->postJson('/api/v1/iot/telemetry', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'room_number' => '101',
                'current_reading' => 1250.75,
            ]);

        $this->assertDatabaseHas('iot_meter_telemetries', [
            'room_id' => $this->room->id,
            'meter_serial' => 'EM-IOT-101',
            'meter_type' => 'electricity',
            'reading' => 1250.75,
        ]);

        $this->assertDatabaseHas('iot_devices', [
            'meter_serial' => 'EM-IOT-101',
            'meter_type' => 'electricity',
            'room_id' => $this->room->id,
            'status' => 'online',
        ]);
    }

    public function test_detects_power_overload_anomaly_and_sets_warning(): void
    {
        $payload = [
            'meter_serial' => 'EM-IOT-101',
            'meter_type' => 'electricity',
            'reading' => 1260.00,
            'voltage' => 220.0,
            'current' => 23.5,
            'power' => 5170.0, // Vượt ngưỡng 4500W
        ];

        $response = $this->postJson('/api/v1/iot/telemetry', $payload);

        $response->assertStatus(200);
        $this->assertNotEmpty($response->json('alerts'));

        $this->assertDatabaseHas('iot_devices', [
            'meter_serial' => 'EM-IOT-101',
            'status' => 'warning',
        ]);
    }

    public function test_can_fetch_room_realtime_metrics(): void
    {
        // Tạo vài điểm đo
        IotMeterTelemetry::create([
            'room_id' => $this->room->id,
            'meter_type' => 'electricity',
            'meter_serial' => 'EM-IOT-101',
            'reading' => 1200.00,
            'power' => 450,
            'recorded_at' => now()->subHours(2),
        ]);

        IotMeterTelemetry::create([
            'room_id' => $this->room->id,
            'meter_type' => 'electricity',
            'meter_serial' => 'EM-IOT-101',
            'reading' => 1205.50,
            'power' => 600,
            'recorded_at' => now(),
        ]);

        $response = $this->getJson("/api/v1/iot/rooms/{$this->room->id}/realtime");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'room' => [
                        'room_number' => '101',
                    ],
                    'latest' => [
                        'electric_reading' => 1205.50,
                        'electric_power' => 600,
                    ],
                ],
            ]);
    }

    public function test_auto_sync_to_utility_records_without_manual_input(): void
    {
        // Ghi nhận telemetry điện và nước mới nhất
        IotMeterTelemetry::create([
            'room_id' => $this->room->id,
            'meter_type' => 'electricity',
            'meter_serial' => 'EM-IOT-101',
            'reading' => 1350.00,
            'recorded_at' => now(),
        ]);

        IotMeterTelemetry::create([
            'room_id' => $this->room->id,
            'meter_type' => 'water',
            'meter_serial' => 'WM-IOT-101',
            'reading' => 45.00,
            'recorded_at' => now(),
        ]);

        $service = app(IotSmartMeteringService::class);
        $result = $service->autoSyncToUtilityRecords($this->tenant->id);

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['synced_count']);

        $currentMonth = Carbon::now()->format('Y-m');
        $this->assertDatabaseHas('utility_records', [
            'room_id' => $this->room->id,
            'billing_month' => $currentMonth,
            'new_electricity' => 1350,
            'new_water' => 45,
            'status' => 'sent',
        ]);
    }

    public function test_landlord_can_simulate_iot_packet_via_endpoint(): void
    {
        $response = $this->actingAs($this->landlord)
            ->postJson('/smartroom/admin/iot/simulate', [
                'room_id' => $this->room->id,
                'meter_type' => 'electricity',
                'protocol' => 'lorawan',
                'reading' => 1400.25,
                'voltage' => 222.0,
                'power' => 850.0,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Đã phát gói tin telemetry mô phỏng thành công!',
            ]);

        $this->assertDatabaseHas('iot_meter_telemetries', [
            'room_id' => $this->room->id,
            'meter_type' => 'electricity',
            'reading' => 1400.25,
        ]);
    }
}
