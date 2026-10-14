<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\ContactRequest;
use App\Models\Role;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ContactRequestFeature07Test extends TestCase
{
    use RefreshDatabase;

    private User $landlord;
    private Tenant $tenant;
    private Building $building;
    private Room $room;

    protected function setUp(): void
    {
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('SQLite driver is required for isolated tests.');
        }

        parent::setUp();

        $this->tenant = Tenant::create([
            'name' => 'SmartRoom Cầu Giấy',
            'email' => 'contact-test@smartroom.local',
            'verification_status' => 'kyc_verified',
        ]);

        $landlordRole = Role::firstOrCreate(['slug' => 'landlord'], ['name' => 'Chủ trọ']);

        $this->landlord = User::create([
            'tenant_id' => $this->tenant->id,
            'role_id' => $landlordRole->id,
            'name' => 'Nguyễn Chủ Trọ',
            'username' => 'landlord_contact_test',
            'email' => 'chutro@smartroom.local',
            'role' => 'landlord',
            'password' => Hash::make('password'),
        ]);

        $this->building = Building::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Tòa Nhà Xanh',
            'address' => '123 Cầu Giấy, Hà Nội',
        ]);

        $this->room = Room::create([
            'tenant_id' => $this->tenant->id,
            'building_id' => $this->building->id,
            'room_number' => '102',
            'floor' => 1,
            'status' => 'empty',
            'price' => 3500000,
            'area' => 25,
        ]);
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
    }

    public function test_err_07_01_requires_customer_name(): void
    {
        $response = $this->postJson(route('api.renty.contact_requests.store'), [
            'room_id' => $this->room->id,
            'customer_name' => '',
            'customer_phone' => '0987654321',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'error_code' => 'ERR_07_01',
            'message' => 'Vui lòng nhập họ và tên để chủ trọ tiện xưng hô liên hệ.',
        ]);
    }

    public function test_err_07_02_requires_customer_phone(): void
    {
        $response = $this->postJson(route('api.renty.contact_requests.store'), [
            'room_id' => $this->room->id,
            'customer_name' => 'Nguyễn Văn B',
            'customer_phone' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'error_code' => 'ERR_07_02',
            'message' => 'Vui lòng nhập số điện thoại để nhận cuộc gọi tư vấn.',
        ]);
    }

    public function test_err_07_03_rejects_invalid_phone_format_or_length(): void
    {
        // Test SĐT không đủ 10 số (chỉ có 7 số)
        $responseShort = $this->postJson(route('api.renty.contact_requests.store'), [
            'room_id' => $this->room->id,
            'customer_name' => 'Nguyễn Văn B',
            'customer_phone' => '0987654',
        ]);

        $responseShort->assertStatus(422);
        $responseShort->assertJson([
            'success' => false,
            'error_code' => 'ERR_07_03',
            'message' => 'Số điện thoại liên hệ phải có chính xác 10 chữ số.',
        ]);

        // Test SĐT chứa ký tự chữ
        $responseLetters = $this->postJson(route('api.renty.contact_requests.store'), [
            'room_id' => $this->room->id,
            'customer_name' => 'Nguyễn Văn B',
            'customer_phone' => '098abc4321',
        ]);

        $responseLetters->assertStatus(422);
        $responseLetters->assertJson([
            'success' => false,
            'error_code' => 'ERR_07_03',
        ]);
    }

    public function test_err_07_04_rejects_appointment_time_in_the_past(): void
    {
        $pastTime = Carbon::now()->subDays(1)->format('Y-m-d H:i:s');

        $response = $this->postJson(route('api.renty.contact_requests.store'), [
            'room_id' => $this->room->id,
            'customer_name' => 'Nguyễn Văn B',
            'customer_phone' => '0987654321',
            'appointment_time' => $pastTime,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'error_code' => 'ERR_07_04',
            'message' => 'Ngày hẹn đến xem phòng không thể là ngày trong quá khứ.',
        ]);
    }

    public function test_rate_limiting_blocks_spam_phone_exceeding_threshold(): void
    {
        $phone = '0987654321';

        // Gửi 3 yêu cầu hợp lệ
        for ($i = 0; $i < 3; $i++) {
            $futureTime = Carbon::now()->addDays($i + 1)->format('Y-m-d H:i:s');
            $res = $this->postJson(route('api.renty.contact_requests.store'), [
                'room_id' => $this->room->id,
                'customer_name' => 'Nguyễn Văn B ' . $i,
                'customer_phone' => $phone,
                'appointment_time' => $futureTime,
            ]);
            $res->assertStatus(201);
        }

        // Lần thứ 4 gửi trong vòng 10 phút sẽ bị chặn với mã ERR_07_SPAM (HTTP 429)
        $futureTime = Carbon::now()->addDays(5)->format('Y-m-d H:i:s');
        $spamResponse = $this->postJson(route('api.renty.contact_requests.store'), [
            'room_id' => $this->room->id,
            'customer_name' => 'Nguyễn Văn B Spam',
            'customer_phone' => $phone,
            'appointment_time' => $futureTime,
        ]);

        $spamResponse->assertStatus(429);
        $spamResponse->assertJson([
            'success' => false,
            'error_code' => 'ERR_07_SPAM',
        ]);
    }

    public function test_err_07_05_happy_case_creates_contact_request_successfully(): void
    {
        $futureTime = Carbon::now()->addDays(2)->setHour(14)->setMinute(0)->format('Y-m-d H:i:s');

        $response = $this->from('/renty/room/' . $this->room->id)->post(route('renty.contact_request.store'), [
            'room_id' => $this->room->id,
            'customer_name' => 'Nguyễn Văn B',
            'customer_phone' => '0987654321',
            'appointment_time' => $futureTime,
            'note' => 'Tôi muốn hẹn xem phòng vào buổi chiều 14h.',
        ]);

        $response->assertRedirect('/renty/room/' . $this->room->id);
        $response->assertSessionHas('success', 'Gửi yêu cầu hẹn xem phòng thành công! Chủ trọ sẽ sớm liên hệ lại.');

        $this->assertDatabaseHas('contact_requests', [
            'room_id' => $this->room->id,
            'name' => 'Nguyễn Văn B',
            'phone' => '0987654321',
            'status' => 'pending',
            'message' => 'Tôi muốn hẹn xem phòng vào buổi chiều 14h.',
        ]);
    }

    public function test_api_endpoint_post_contact_requests(): void
    {
        $futureTime = Carbon::now()->addDays(3)->format('Y-m-d H:i:s');

        $response = $this->postJson('/api/renty/contact-requests', [
            'room_id' => $this->room->id,
            'name' => 'Trần Thị C',
            'phone' => '0912345678',
            'appointment_time' => $futureTime,
            'message' => 'Liên hệ tư vấn chi phí phòng.',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'error_code' => 'ERR_07_05',
            'message' => 'Gửi yêu cầu hẹn xem phòng thành công! Chủ trọ sẽ sớm liên hệ lại.',
        ]);

        $this->assertDatabaseHas('contact_requests', [
            'room_id' => $this->room->id,
            'name' => 'Trần Thị C',
            'phone' => '0912345678',
            'status' => 'pending',
        ]);
    }

    public function test_admin_contact_requests_route_and_permissions(): void
    {
        // 1. Web redirect về profile/tab contact-section
        $webResponse = $this->actingAs($this->landlord)->get(route('smartroom.admin.contact_requests'));
        $webResponse->assertRedirect(route('smartroom.admin', ['tab' => 'contact-section']));

        // 2. API/JSON trả danh sách
        $jsonResponse = $this->actingAs($this->landlord)->getJson(route('smartroom.admin.contact_requests'));
        $jsonResponse->assertStatus(200);
        $jsonResponse->assertJsonStructure([
            'success',
            'data',
        ]);
    }

    public function test_err_07_06_landlord_can_update_status_and_audit_log(): void
    {
        $contact = ContactRequest::create([
            'room_id' => $this->room->id,
            'name' => 'Khách Cần Gọi',
            'phone' => '0987111222',
            'message' => 'Cần xem phòng sớm.',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->landlord)->post(route('smartroom.admin.contact_request.status', $contact->id), [
            'status' => 'processed',
        ]);

        $response->assertRedirect(route('smartroom.admin', ['tab' => 'contact-section']));
        $response->assertSessionHas('success', 'Đã cập nhật trạng thái yêu cầu tư vấn thành công.');

        $this->assertDatabaseHas('contact_requests', [
            'id' => $contact->id,
            'status' => 'processed',
        ]);

        // Kiểm tra audit log đã được ghi
        $this->assertDatabaseHas('admin_activity_logs', [
            'action' => 'update',
            'module' => 'contact_requests',
            'subject_id' => $contact->id,
        ]);
    }

    public function test_landlord_can_delete_contact_request(): void
    {
        $contact = ContactRequest::create([
            'room_id' => $this->room->id,
            'name' => 'Khách Hủy Hẹn',
            'phone' => '0987333444',
            'message' => 'Đã tìm được phòng khác.',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->landlord)->delete(route('smartroom.admin.contact_request.delete', $contact->id));

        $response->assertRedirect(route('smartroom.admin', ['tab' => 'contact-section']));
        $response->assertSessionHas('success', 'Xóa yêu cầu tư vấn thành công!');

        $this->assertDatabaseMissing('contact_requests', [
            'id' => $contact->id,
        ]);

        // Kiểm tra audit log
        $this->assertDatabaseHas('admin_activity_logs', [
            'action' => 'delete',
            'module' => 'contact_requests',
        ]);
    }

    public function test_existing_renty_contact_request_route_compatibility(): void
    {
        $response = $this->from('/renty')->post(route('renty.contact_request.store'), [
            'room_id' => $this->room->id,
            'name' => 'Khách Tương Thích Cũ',
            'phone' => '0988888999',
            'message' => 'Form cũ không có appointment_time.',
        ]);

        $response->assertRedirect('/renty');
        $this->assertDatabaseHas('contact_requests', [
            'room_id' => $this->room->id,
            'name' => 'Khách Tương Thích Cũ',
            'phone' => '0988888999',
            'status' => 'pending',
            'appointment_time' => null,
        ]);
    }
}
