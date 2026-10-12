<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Contract;
use App\Models\Resident;
use App\Models\Review;
use App\Models\Role;
use App\Models\Room;
use App\Models\RoomReport;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * FEAT_25_REVIEW_REPORT: Đánh giá phòng thực tế & Báo cáo sai phạm
 * Phụ trách: Huỳnh Văn Vĩnh Em
 */
class RoomReviewReportFeature25Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('SQLite driver is required for tests.');
        }

        parent::setUp();
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
    }

    private function createTenant(string $name = 'Renty Building 25'): Tenant
    {
        return Tenant::create([
            'name' => $name,
            'email' => 'renty_' . uniqid() . '@example.com',
            'domain' => 'renty-' . uniqid(),
        ]);
    }

    private function createRoom(Tenant $tenant, string $roomNumber = '101'): Room
    {
        $building = Building::firstOrCreate([
            'tenant_id' => $tenant->id,
            'name' => 'Tòa Nhà Xanh',
        ], [
            'address' => '456 Cầu Giấy, Hà Nội',
        ]);

        return Room::create([
            'tenant_id' => $tenant->id,
            'building_id' => $building->id,
            'room_number' => $roomNumber,
            'floor' => 1,
            'price' => 4500000,
            'status' => 'occupied',
            'cleaning_status' => 'clean',
        ]);
    }

    private function createVerifiedTenantUser(Tenant $tenant, Room $room): User
    {
        $residentRole = Role::firstOrCreate(['slug' => 'resident'], ['name' => 'Cư dân']);

        $user = User::create([
            'name' => 'Nguyễn Văn Thuê Trọ',
            'username' => 'tenant_' . uniqid(),
            'email' => 'tenant_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role_id' => $residentRole->id,
            'role' => 'user',
            'tenant_id' => $tenant->id,
            'phone' => '0912345678',
        ]);

        $resident = Resident::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'user_id' => $user->id,
            'name' => $user->name,
            'phone' => $user->phone,
            'email' => $user->email,
            'start_date' => now()->subMonths(3)->toDateString(),
            'status' => 'active',
            'temporary_residence_status' => 'registered',
            'version' => 1,
        ]);

        Contract::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'resident_id' => $resident->id,
            'contract_code' => 'HD-' . uniqid(),
            'start_date' => now()->subMonths(3)->toDateString(),
            'end_date' => now()->addMonths(9)->toDateString(),
            'deposit' => 4500000,
            'terms' => 'Điều khoản mẫu hợp đồng thuê phòng trọ.',
            'status' => 'active',
            'is_signed' => true,
        ]);

        return $user;
    }

    private function createRegularUser(): User
    {
        $guestRole = Role::firstOrCreate(['slug' => 'guest'], ['name' => 'Khách']);

        return User::create([
            'name' => 'Người Dùng Khách',
            'username' => 'guest_' . uniqid(),
            'email' => 'guest_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role_id' => $guestRole->id,
            'role' => 'guest',
            'phone' => '0999888777',
        ]);
    }

    /**
     * 1. Kiểm tra Review yêu cầu đăng nhập (auth middleware)
     */
    public function test_review_requires_authentication(): void
    {
        $tenant = $this->createTenant();
        $room = $this->createRoom($tenant);

        $response = $this->postJson("/renty/room/{$room->id}/review", [
            'rating' => 5,
            'comment' => 'Phòng rất đẹp và thoáng mát sạch sẽ',
        ]);

        $response->assertStatus(401);
    }

    /**
     * 2. Kiểm tra chỉ người có hợp đồng thuê mới được review (verified_tenant middleware)
     */
    public function test_only_verified_tenant_with_contract_can_review(): void
    {
        $tenant = $this->createTenant();
        $room = $this->createRoom($tenant);
        $regularUser = $this->createRegularUser();

        $response = $this->actingAs($regularUser)->postJson("/renty/room/{$room->id}/review", [
            'rating' => 5,
            'comment' => 'Phòng này nhìn trên ảnh có vẻ ổn',
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Chỉ người có hợp đồng thuê phòng mới được quyền gửi đánh giá thực tế.',
        ]);
    }

    /**
     * 3. Kịch bản lỗi 1 (ERR_25_01 / DoD STT 1): Bấm gửi nhận xét khi chưa chọn số sao
     */
    public function test_validation_error_when_rating_is_missing_err_25_01(): void
    {
        $tenant = $this->createTenant();
        $room = $this->createRoom($tenant);
        $verifiedUser = $this->createVerifiedTenantUser($tenant, $room);

        $response = $this->actingAs($verifiedUser)->postJson("/renty/room/{$room->id}/review", [
            'rating' => null,
            'comment' => 'Phòng ở rất tiện nghi và đầy đủ trang thiết bị.',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'code' => 'ERR_25_01',
            'message' => 'Vui lòng chọn số sao xếp hạng (từ 1 đến 5 sao).',
        ]);
    }

    /**
     * 4. Kịch bản lỗi 2 (ERR_25_02 / DoD STT 2): Nhập nhận xét quá ngắn (< 10 ký tự, ví dụ: 'Tốt')
     */
    public function test_validation_error_when_comment_is_too_short_err_25_02(): void
    {
        $tenant = $this->createTenant();
        $room = $this->createRoom($tenant);
        $verifiedUser = $this->createVerifiedTenantUser($tenant, $room);

        $response = $this->actingAs($verifiedUser)->postJson("/renty/room/{$room->id}/review", [
            'rating' => 5,
            'comment' => 'Tốt',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'code' => 'ERR_25_02',
            'message' => 'Nội dung nhận xét đánh giá phải có độ dài tối thiểu 10 ký tự.',
        ]);
    }

    /**
     * 5. Kịch bản Happy Case (DoD STT 3): Chọn 5 sao và viết nhận xét chi tiết -> Lưu thành công vào DB
     */
    public function test_successful_review_submission(): void
    {
        $tenant = $this->createTenant();
        $room = $this->createRoom($tenant);
        $verifiedUser = $this->createVerifiedTenantUser($tenant, $room);

        $payload = [
            'rating' => 5,
            'comment' => 'Phòng ở rất sạch sẽ, an ninh tốt, chủ nhà hỗ trợ cực kỳ nhiệt tình và chu đáo.',
        ];

        $response = $this->actingAs($verifiedUser)->postJson("/renty/room/{$room->id}/review", $payload);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'message' => 'Cảm ơn bạn đã gửi đánh giá thực tế!',
        ]);

        $this->assertDatabaseHas('reviews', [
            'room_id' => $room->id,
            'user_id' => $verifiedUser->id,
            'rating' => 5,
            'comment' => $payload['comment'],
        ]);
    }

    /**
     * 6. Kịch bản lỗi 3 (ERR_25_03): Báo cáo vi phạm chưa chọn lý do
     */
    public function test_validation_error_when_report_reason_is_missing_err_25_03(): void
    {
        $tenant = $this->createTenant();
        $room = $this->createRoom($tenant);

        $response = $this->postJson("/renty/room/{$room->id}/report", [
            'report_reason' => '',
            'description' => 'Mô tả chi tiết nhưng chưa chọn lý do vi phạm',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'code' => 'ERR_25_03',
            'message' => 'Vui lòng chọn lý do vi phạm (Lừa cọc, Ảnh sai thực tế, Giá ảo...).',
        ]);
    }

    /**
     * 7. Kịch bản thành công 4 (ERR_25_04): Gửi báo cáo sai phạm thành công
     */
    public function test_successful_report_submission_err_25_04(): void
    {
        $tenant = $this->createTenant();
        $room = $this->createRoom($tenant);

        $payload = [
            'report_reason' => 'Ảnh sai thực tế phòng',
            'description' => 'Phòng ngoài đời nhỏ hơn ảnh và không có ban công như tin đăng.',
        ];

        $response = $this->postJson("/renty/room/{$room->id}/report", $payload);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'code' => 'ERR_25_04',
            'message' => 'Đã tiếp nhận báo cáo vi phạm. Quản trị viên sẽ kiểm duyệt tin này trong 12h.',
        ]);

        $this->assertDatabaseHas('room_reports', [
            'room_id' => $room->id,
            'reason' => 'Ảnh sai thực tế phòng',
            'status' => 'pending',
        ]);
    }
}
