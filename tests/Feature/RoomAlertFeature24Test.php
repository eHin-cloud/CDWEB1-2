<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Role;
use App\Models\Room;
use App\Models\RoomAlert;
use App\Models\Tenant;
use App\Models\User;
use App\Services\RoomAlertService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RoomAlertFeature24Test extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private Building $building;
    private Room $room;
    private User $user;

    protected function setUp(): void
    {
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('SQLite driver is required for isolated tests.');
        }

        parent::setUp();

        $this->tenant = Tenant::create([
            'name' => 'SmartRoom Cầu Giấy',
            'email' => 'caugiay@smartroom.local',
            'verification_status' => 'kyc_verified',
        ]);

        $role = Role::firstOrCreate(['slug' => 'customer'], ['name' => 'Khách thuê']);

        $this->user = User::create([
            'tenant_id' => $this->tenant->id,
            'role_id' => $role->id,
            'name' => 'Khách Tìm Phòng',
            'username' => 'khach_alert_test',
            'email' => 'khach_alert@test.local',
            'phone' => '0912345678',
            'role' => 'customer',
            'password' => Hash::make('password'),
        ]);

        $this->building = Building::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Tòa Nhà Xanh Cầu Giấy',
            'address' => 'Số 10 Dịch Vọng Hậu, Quận Cầu Giấy, Hà Nội',
        ]);

        $this->room = Room::create([
            'tenant_id' => $this->tenant->id,
            'building_id' => $this->building->id,
            'room_number' => '301',
            'floor' => 3,
            'area' => 25,
            'price' => 3500000,
            'status' => 'occupied',
        ]);
    }

    /**
     * DoD 1: Để trống toàn bộ ô và bấm Kích hoạt -> Báo lỗi đỏ yêu cầu nhập khu vực, ngân sách và liên hệ
     */
    public function test_validation_error_when_all_fields_empty(): void
    {
        $response = $this->postJson('/api/renty/room-alerts', []);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'code' => 'ERR_24_01',
                'field' => 'contact_info',
                'message' => 'Vui lòng nhập số điện thoại hoặc email nhận thông báo.',
            ])
            ->assertJsonValidationErrors(['contact_info', 'target_district', 'max_budget']);
    }

    /**
     * ERR_24_02: Email hoặc số điện thoại sai định dạng hợp lệ
     */
    public function test_validation_error_when_contact_info_is_invalid_format(): void
    {
        $response = $this->postJson('/api/renty/room-alerts', [
            'contact_info' => 'invalid_email_or_phone',
            'target_district' => 'Quận Cầu Giấy',
            'max_budget' => 4000000,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'code' => 'ERR_24_02',
                'field' => 'contact_info',
                'message' => 'Địa chỉ email không đúng định dạng hợp lệ.',
            ]);
    }

    /**
     * ERR_24_03: Để trống khu vực mong muốn
     */
    public function test_validation_error_when_target_district_is_empty(): void
    {
        $response = $this->postJson('/api/renty/room-alerts', [
            'contact_info' => 'user@example.com',
            'target_district' => '',
            'max_budget' => 4000000,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'code' => 'ERR_24_03',
                'field' => 'target_district',
                'message' => 'Vui lòng chọn quận/huyện bạn đang muốn tìm phòng.',
            ]);
    }

    /**
     * DoD 2: Nhập ngân sách = 0 -> Báo lỗi 'Ngân sách tối đa phải lớn hơn 0 VNĐ'
     */
    public function test_validation_error_when_max_budget_is_zero(): void
    {
        $response = $this->postJson('/api/renty/room-alerts', [
            'contact_info' => 'user@example.com',
            'target_district' => 'Quận Cầu Giấy',
            'max_budget' => 0,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'code' => 'ERR_24_BUDGET',
                'field' => 'max_budget',
                'message' => 'Ngân sách tối đa phải lớn hơn 0 VNĐ',
            ]);
    }

    /**
     * DoD 3: Nhập thông tin hợp lệ với Email -> Lưu thông tin đăng ký alert thành công (ERR_24_04) & Gửi email xác nhận
     */
    public function test_store_room_alert_successfully_with_email(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/renty/room-alerts', [
            'contact_info' => 'khach1@smartroom.local',
            'target_district' => 'Quận Cầu Giấy',
            'max_budget' => 4000000,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'code' => 'ERR_24_04',
                'message' => 'Đăng ký nhận thông báo phòng trống thành công! Bạn sẽ nhận tin ngay khi có phòng mới.',
                'data' => [
                    'contact_info' => 'khach1@smartroom.local',
                    'target_district' => 'Quận Cầu Giấy',
                    'max_budget' => 4000000,
                    'status' => 'active',
                    'email_sent' => true,
                ],
            ]);

        $this->assertDatabaseHas('room_alerts', [
            'contact_info' => 'khach1@smartroom.local',
            'target_district' => 'Quận Cầu Giấy',
            'max_budget' => 4000000,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('notification_logs', [
            'recipient_contact' => 'khach1@smartroom.local',
            'type' => 'room_alert_registered',
        ]);
    }

    /**
     * DoD 3: Nhập thông tin hợp lệ với SĐT 10 số và liên kết phòng cụ thể (Multi-tenancy)
     */
    public function test_store_room_alert_successfully_with_phone_and_room(): void
    {
        $response = $this->postJson('/api/renty/room-alerts', [
            'contact_info' => '0930000001',
            'target_district' => 'Quận Cầu Giấy',
            'max_budget' => 3800000,
            'room_id' => $this->room->id,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'code' => 'ERR_24_04',
                'data' => [
                    'contact_info' => '0930000001',
                    'room_id' => $this->room->id,
                ],
            ]);

        $this->assertDatabaseHas('room_alerts', [
            'tenant_id' => $this->tenant->id,
            'room_id' => $this->room->id,
            'contact_info' => '0930000001',
            'status' => 'active',
        ]);
    }

    /**
     * GET /api/renty/room-alerts/my requires auth
     */
    public function test_my_alerts_requires_authentication(): void
    {
        $response = $this->getJson('/api/renty/room-alerts/my');
        $response->assertStatus(401);
    }

    /**
     * GET /api/renty/room-alerts/my returns list of alerts
     */
    public function test_my_alerts_returns_user_alerts(): void
    {
        RoomAlert::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'contact_info' => $this->user->email,
            'target_district' => 'Quận Cầu Giấy',
            'max_budget' => 4000000,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user, 'api')->getJson('/api/renty/room-alerts/my');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'count' => 1,
            ]);
    }

    /**
     * Kịch bản kích hoạt chuông báo: khi phòng chuyển sang 'empty', RoomAlertService tự động gửi email thông báo
     */
    public function test_room_alert_service_notifies_matching_alerts(): void
    {
        Mail::fake();

        $alert = RoomAlert::create([
            'tenant_id' => $this->tenant->id,
            'contact_info' => 'nhanthongbao@smartroom.local',
            'target_district' => 'Quận Cầu Giấy',
            'max_budget' => 4000000,
            'status' => 'active',
        ]);

        // Ban đầu phòng đang occupied, gọi service sẽ không gửi thông báo
        $notified = RoomAlertService::notifySubscribersForRoom($this->room);
        $this->assertEquals(0, $notified);
        $this->assertEquals('active', $alert->fresh()->status);

        // Chuyển phòng sang 'empty'
        $this->room->status = 'empty';
        $this->room->save();

        // Kiểm tra alert đã được cập nhật thành 'notified'
        $alertFresh = $alert->fresh();
        $this->assertEquals('notified', $alertFresh->status);
        $this->assertNotNull($alertFresh->notified_at);

        $this->assertDatabaseHas('notification_logs', [
            'recipient_contact' => 'nhanthongbao@smartroom.local',
            'type' => 'room_alert_empty_notified',
        ]);
    }

    /**
     * Hủy chuông báo DELETE /api/renty/room-alerts/{id}
     */
    public function test_destroy_room_alert_successfully(): void
    {
        $alert = RoomAlert::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'contact_info' => $this->user->email,
            'target_district' => 'Quận Cầu Giấy',
            'max_budget' => 4000000,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user, 'api')->deleteJson('/api/renty/room-alerts/' . $alert->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Đã hủy chuông báo phòng thành công.',
            ]);

        $this->assertEquals('cancelled', $alert->fresh()->status);
    }
}
