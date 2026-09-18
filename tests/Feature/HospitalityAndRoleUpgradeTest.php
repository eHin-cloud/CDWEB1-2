<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Room;
use App\Models\Building;
use App\Models\Tenant;
use App\Models\Role;
use App\Models\HotelBooking;
use App\Models\UtilityRecord;
use App\Services\BillingEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HospitalityAndRoleUpgradeTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected Building $building;
    protected Room $room;
    protected User $receptionist;
    protected User $housekeeper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::firstOrCreate(
            ['name' => 'Demo Hotel & Apartment Group'],
            ['address' => 'Hà Nội', 'phone' => '0988000001', 'email' => 'hotel@demo.smartroom.local']
        );

        $this->building = Building::firstOrCreate(
            ['name' => 'Tòa Nhà Khách Sạn Demo'],
            [
                'tenant_id' => $this->tenant->id,
                'address' => '123 Cầu Giấy, Hà Nội',
                'property_type' => 'hotel'
            ]
        );

        $this->room = Room::firstOrCreate(
            ['room_number' => 'KS-901', 'building_id' => $this->building->id],
            [
                'tenant_id' => $this->tenant->id,
                'floor' => 9,
                'price' => 9000000,
                'price_per_day' => 450000,
                'price_per_hour' => 150000,
                'price_extra_hour' => 50000,
                'rental_type' => 'day',
                'room_type' => 'vip',
                'status' => 'empty',
                'cleaning_status' => 'clean',
                'area' => 32,
            ]
        );

        $recRole = Role::firstOrCreate(['slug' => 'receptionist'], ['name' => 'Lễ tân']);
        $hkRole = Role::firstOrCreate(['slug' => 'housekeeper'], ['name' => 'Buồng phòng']);

        $this->receptionist = User::create([
            'tenant_id' => $this->tenant->id,
            'role_id' => $recRole->id,
            'name' => 'Lễ tân Test',
            'username' => 'test_receptionist',
            'role' => 'receptionist',
            'password' => bcrypt('password'),
        ]);

        $this->housekeeper = User::create([
            'tenant_id' => $this->tenant->id,
            'role_id' => $hkRole->id,
            'name' => 'Buồng phòng Test',
            'username' => 'test_housekeeper',
            'role' => 'housekeeper',
            'password' => bcrypt('password'),
        ]);
    }

    /**
     * 1. Test Động cơ tính tiền (BillingEngine)
     */
    public function test_billing_engine_monthly_and_hotel_calculations(): void
    {
        // Test tính tiền tháng (Chung cư/Trọ)
        $utility = new UtilityRecord([
            'old_electricity' => 100,
            'new_electricity' => 150, // dùng 50 số
            'electricity_price' => 3500, // 175.000đ
            'old_water' => 20,
            'new_water' => 25, // dùng 5 khối
            'water_price' => 25000, // 125.000đ
        ]);
        $this->room->price = 4000000;

        $monthlyCalc = BillingEngine::calculateMonthlyRent($this->room, $utility);
        // 4.000.000 + 175.000 + 125.000 + 150.000 (phí dv) = 4.450.000đ
        $this->assertEquals(4450000, $monthlyCalc['total_amount']);

        // Test tính tiền giờ khách sạn (Block 2h đầu 150k + 2h phụ trội 100k = 250k - 50k cọc = 200k)
        $fixedCheckin = \Carbon\Carbon::create(2026, 6, 12, 10, 0, 0);
        $fixedCheckout = \Carbon\Carbon::create(2026, 6, 12, 14, 0, 0);

        $booking = new HotelBooking([
            'rental_type' => 'hour',
            'unit_rate' => 150000,
            'check_in_at' => $fixedCheckin,
            'actual_check_out_at' => $fixedCheckout,
            'deposit_amount' => 50000,
        ]);
        $booking->setRelation('room', $this->room);

        $hotelHourCalc = BillingEngine::calculateHotelCheckout($booking, $fixedCheckout);
        // 150k (2h đầu) + 2 * 50k (2h sau) - 50k (cọc) = 200.000đ
        $this->assertEquals(200000, $hotelHourCalc['total_amount']);
    }

    /**
     * 2. Test luồng Check-in và Check-out Khách sạn của Lễ tân
     */
    public function test_hotel_reception_checkin_checkout_flow(): void
    {
        $this->room->update(['status' => 'empty', 'cleaning_status' => 'clean']);

        // A. Check-in
        $response = $this->actingAs($this->receptionist)->post(route('admin.hotel.checkin'), [
            'room_id' => $this->room->id,
            'guest_name' => 'Nguyễn Văn Khách',
            'guest_phone' => '0912345678',
            'rental_type' => 'day',
            'deposit_amount' => 100000,
        ]);

        $response->assertSessionHas('success');
        $this->room->refresh();
        $this->assertEquals('occupied', $this->room->status);

        $booking = HotelBooking::where('room_id', $this->room->id)->latest('id')->first();
        $this->assertNotNull($booking);
        $this->assertEquals('Nguyễn Văn Khách', $booking->guest_name);

        // B. Thêm Minibar
        $minibarResponse = $this->actingAs($this->receptionist)->postJson(route('admin.hotel.folio.add_item', $booking->id), [
            'item_name' => 'Nước ngọt Coca-Cola',
            'item_type' => 'minibar',
            'quantity' => 2,
            'unit_price' => 20000,
        ]);
        $minibarResponse->assertJson(['success' => true]);

        // C. Check-out
        $checkoutResponse = $this->actingAs($this->receptionist)->postJson(route('admin.hotel.checkout', $booking->id), [
            'payment_method' => 'vietqr',
        ]);

        $checkoutResponse->assertJson(['success' => true]);
        $this->room->refresh();
        // Phòng chuyển sang trạng thái cleaning (Bẩn/cần dọn)
        $this->assertEquals('cleaning', $this->room->status);
        $this->assertEquals('dirty', $this->room->cleaning_status);
    }

    /**
     * 3. Test Buồng phòng cập nhật vệ sinh dọn dẹp
     */
    public function test_housekeeper_clean_room_flow(): void
    {
        $this->room->update(['status' => 'cleaning', 'cleaning_status' => 'dirty']);

        // Truy cập danh sách buồng phòng
        $indexResponse = $this->actingAs($this->housekeeper)->get(route('admin.housekeeping.index'));
        $indexResponse->assertStatus(200);

        // Bấm Đã dọn xong
        $updateResponse = $this->actingAs($this->housekeeper)->post(route('admin.housekeeping.update', $this->room->id), [
            'cleaning_status' => 'clean'
        ]);

        $updateResponse->assertSessionHas('success');
        $this->room->refresh();
        // Phòng chuyển sang sạch và sẵn sàng đón khách
        $this->assertEquals('empty', $this->room->status);
        $this->assertEquals('clean', $this->room->cleaning_status);
    }

    /**
     * 4. Test Bảo mật: Lễ tân không thể vào Sổ thu chi của Chủ trọ
     */
    public function test_receptionist_cannot_access_landlord_financial_reports(): void
    {
        // Route /smartroom/admin/reports được bảo vệ bởi middleware 'role:landlord'
        $response = $this->actingAs($this->receptionist)->get(route('admin.reports.index'));
        // Bị redirect hoặc trả về 403 do không có role landlord
        $this->assertTrue(in_array($response->getStatusCode(), [302, 403]));
    }
}
