<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\Building;
use App\Models\Room;
use App\Models\User;
use App\Models\Role;
use App\Models\HotelBooking;
use App\Models\HotelFolioItem;
use App\Models\HousekeepingLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class HousekeepingFrontdeskDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. LẤY HOẶC TẠO TENANT CƠ SỞ CHÍNH
        $tenant = Tenant::where('id', 1)->first() ?: Tenant::first();
        if (!$tenant) {
            $tenant = Tenant::create([
                'name' => 'Hệ thống Chung cư mini SmartRoom Cầu Giấy',
                'email' => 'contact@smartroom-caugiay.vn',
                'phone' => '0988000001',
                'owner_name' => 'Trần Văn Hoàng',
                'bank_name' => 'MB',
                'bank_account_no' => '999988880001',
                'bank_account_name' => 'TRAN VAN HOANG',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 95,
            ]);
        } else {
            $tenant->update([
                'bank_name' => 'MB',
                'bank_account_no' => '999988880001',
                'bank_account_name' => 'TRAN VAN HOANG',
            ]);
        }
        $tenantId = $tenant->id;

        // 2. KHỞI TẠO CÁC ROLE CẦN THIẾT
        $receptionistRole = Role::where('slug', 'receptionist')->first();
        $housekeeperRole = Role::where('slug', 'housekeeper')->first();
        $landlordRole = Role::where('slug', 'landlord')->first();

        $defaultPassword = Hash::make('123456');

        // 3. TẠO HOẶC CẬP NHẬT TÀI KHOẢN TEST CHUYÊN DỤNG (Mật khẩu: 123456 hoặc password)
        // A. Lễ Tân 1 (letan1 & letan1_1)
        $receptionist1 = User::updateOrCreate(
            ['username' => 'letan1'],
            [
                'name' => 'Lê Thị Thu Thảo (Lễ Tân)',
                'email' => 'letan1@demo.smartroom.local',
                'phone' => '0961999001',
                'password' => $defaultPassword,
                'role' => 'receptionist',
                'role_id' => $receptionistRole?->id,
                'tenant_id' => $tenantId,
                'status' => 'active',
                'like' => 'Lễ Tân 1 Cầu Giấy',
            ]
        );

        if ($existingLt = User::where('username', 'letan1_1')->first()) {
            $existingLt->update([
                'password' => $defaultPassword,
                'role' => 'receptionist',
                'role_id' => $receptionistRole?->id,
                'tenant_id' => $tenantId,
                'status' => 'active',
            ]);
        }

        // B. Buồng Phòng 1 (buongphong1 & buongphong1_1)
        $housekeeper1 = User::updateOrCreate(
            ['username' => 'buongphong1'],
            [
                'name' => 'Nguyễn Văn Dũng (Buồng Phòng 1)',
                'email' => 'buongphong1@demo.smartroom.local',
                'phone' => '0951999001',
                'password' => $defaultPassword,
                'role' => 'housekeeper',
                'role_id' => $housekeeperRole?->id,
                'tenant_id' => $tenantId,
                'status' => 'active',
                'like' => 'Buồng Phòng 1 Cầu Giấy',
            ]
        );

        if ($existingBp1 = User::where('username', 'buongphong1_1')->first()) {
            $existingBp1->update([
                'password' => $defaultPassword,
                'role' => 'housekeeper',
                'role_id' => $housekeeperRole?->id,
                'tenant_id' => $tenantId,
                'status' => 'active',
            ]);
        }

        // C. Buồng Phòng 2 (buongphong2 & buongphong1_2)
        $housekeeper2 = User::updateOrCreate(
            ['username' => 'buongphong2'],
            [
                'name' => 'Trần Thị Lan (Buồng Phòng 2)',
                'email' => 'buongphong2@demo.smartroom.local',
                'phone' => '0951999002',
                'password' => $defaultPassword,
                'role' => 'housekeeper',
                'role_id' => $housekeeperRole?->id,
                'tenant_id' => $tenantId,
                'status' => 'active',
                'like' => 'Buồng Phòng 2 Cầu Giấy',
            ]
        );

        if ($existingBp2 = User::where('username', 'buongphong1_2')->first()) {
            $existingBp2->update([
                'password' => $defaultPassword,
                'role' => 'housekeeper',
                'role_id' => $housekeeperRole?->id,
                'tenant_id' => $tenantId,
                'status' => 'active',
            ]);
        }

        // D. Chủ Trọ
        $landlord1 = User::where('username', 'chutro1')->first();
        if ($landlord1) {
            $landlord1->update([
                'password' => $defaultPassword,
                'tenant_id' => $tenantId,
                'status' => 'active',
            ]);
        } else {
            $landlord1 = User::create([
                'username' => 'chutro1',
                'name' => 'Trần Văn Hoàng (Chủ Cơ Sở)',
                'email' => 'chutro1@demo.smartroom.local',
                'phone' => '0988999001',
                'password' => $defaultPassword,
                'role' => 'landlord',
                'role_id' => $landlordRole?->id,
                'tenant_id' => $tenantId,
                'status' => 'active',
                'like' => 'Chủ trọ Cầu Giấy',
            ]);
        }

        // 4. LẤY HOẶC TẠO TÒA NHÀ TEST CHÍNH
        $buildingA = Building::where('tenant_id', $tenantId)->first();
        if (!$buildingA) {
            $buildingA = Building::create([
                'tenant_id' => $tenantId,
                'name' => 'Chung cư mini SmartRoom Cầu Giấy - Tòa A',
                'address' => 'Số 12 Ngõ 105 Xuân Thủy, Cầu Giấy, Hà Nội',
                'description' => 'Tòa nhà cao cấp SmartRoom hiện đại, hỗ trợ khách thuê theo tháng, ngày, giờ.',
                'property_type' => 'hotel',
                'checkin_time' => '14:00:00',
                'checkout_time' => '12:00:00',
            ]);
        }

        $buildingId = $buildingA->id;

        // 5. CẤU HÌNH DANH SÁCH PHÒNG TEST FSM VỚI ĐẦY ĐỦ CÁC KỊCH BẢN THỰC TẾ
        // KỊCH BẢN 1: P.201 - Thuê theo ngày (500,000 đ/ngày) - Đang ở - Có Minibar & Đơn đặt mua hộ
        $room201 = Room::updateOrCreate(
            ['tenant_id' => $tenantId, 'building_id' => $buildingId, 'room_number' => '201'],
            [
                'floor' => 2,
                'room_type' => 'vip',
                'rental_type' => 'day',
                'price' => 12000000,
                'price_per_day' => 500000, // 500,000 đ / ngày theo đúng yêu cầu người dùng
                'price_per_hour' => 140000,
                'price_extra_hour' => 40000,
                'status' => 'occupied',
                'housekeeping_status' => 'inspected',
                'cleaning_status' => 'clean',
                'assigned_staff_id' => $housekeeper1->id,
                'inspected_by' => $receptionist1->id,
                'inspected_at' => Carbon::now()->subDays(1),
                'area' => 35,
                'description' => 'Phòng VIP Suite giường King-size, ban công view thoáng mát, minibar đầy đủ đồ uống.',
                'amenities' => ['wifi', 'tv', 'ac', 'fridge', 'water_heater', 'minibar', 'balcony', 'smart_lock'],
                'version' => 1,
            ]
        );

        $booking201 = HotelBooking::updateOrCreate(
            ['booking_code' => 'HB-CG201'],
            [
                'tenant_id' => $tenantId,
                'room_id' => $room201->id,
                'guest_name' => 'Đặng Quốc Tuấn',
                'guest_phone' => '0912345678',
                'guest_cccd' => '001095012345',
                'rental_type' => 'day',
                'check_in_at' => Carbon::now()->subHours(24),
                'expected_check_out_at' => Carbon::now()->addHours(2),
                'unit_rate' => 500000,
                'deposit_amount' => 200000,
                'status' => 'checked_in',
                'payment_status' => 'unpaid',
                'note' => 'Khách VIP nhận phòng hôm qua, yêu cầu thêm 1 thùng nước khoáng Aquafina.',
            ]
        );

        // Minibar & mặt hàng mua hộ cho P.201
        HotelFolioItem::updateOrCreate(
            ['booking_id' => $booking201->id, 'item_name' => 'Bia Tiger lon 330ml'],
            ['item_type' => 'minibar', 'quantity' => 2, 'unit_price' => 25000, 'subtotal' => 50000]
        );
        HotelFolioItem::updateOrCreate(
            ['booking_id' => $booking201->id, 'item_name' => 'Nước khoáng Lavie 500ml'],
            ['item_type' => 'minibar', 'quantity' => 2, 'unit_price' => 10000, 'subtotal' => 20000]
        );
        HotelFolioItem::updateOrCreate(
            ['booking_id' => $booking201->id, 'item_name' => 'Snack khoai tây Pringles'],
            ['item_type' => 'minibar', 'quantity' => 1, 'unit_price' => 45000, 'subtotal' => 45000]
        );
        HotelFolioItem::updateOrCreate(
            ['booking_id' => $booking201->id, 'item_name' => 'Mua hộ 1 thùng nước suối Aquafina 24 chai'],
            ['item_type' => 'service', 'quantity' => 1, 'unit_price' => 120000, 'subtotal' => 120000]
        );

        // KỊCH BẢN 2: P.102 - Thuê theo giờ (100,000 đ/2h đầu + 30,000 đ/h phụ trội) - Đang ở 3.5h
        $room102 = Room::updateOrCreate(
            ['tenant_id' => $tenantId, 'building_id' => $buildingId, 'room_number' => '102'],
            [
                'floor' => 1,
                'room_type' => 'deluxe',
                'rental_type' => 'hour',
                'price' => 8500000,
                'price_per_day' => 450000,
                'price_per_hour' => 100000, // 100k / 2h đầu
                'price_extra_hour' => 30000, // 30k / h phụ trội
                'status' => 'occupied',
                'housekeeping_status' => 'inspected',
                'cleaning_status' => 'clean',
                'assigned_staff_id' => $housekeeper2->id,
                'inspected_by' => $receptionist1->id,
                'inspected_at' => Carbon::now()->subHours(4),
                'area' => 28,
                'description' => 'Phòng Deluxe sang trọng, cách âm cao cấp, đèn LED trang trí đổi màu.',
                'amenities' => ['wifi', 'tv', 'ac', 'fridge', 'minibar', 'smart_lock'],
                'version' => 1,
            ]
        );

        $booking102 = HotelBooking::updateOrCreate(
            ['booking_code' => 'HB-CG102'],
            [
                'tenant_id' => $tenantId,
                'room_id' => $room102->id,
                'guest_name' => 'Lê Hoàng Long',
                'guest_phone' => '0933888999',
                'guest_cccd' => '001098054321',
                'rental_type' => 'hour',
                'check_in_at' => Carbon::now()->subMinutes(210), // ở được 3.5 tiếng
                'unit_rate' => 100000,
                'deposit_amount' => 50000,
                'status' => 'checked_in',
                'payment_status' => 'unpaid',
                'note' => 'Khách thuê theo giờ.',
            ]
        );

        HotelFolioItem::updateOrCreate(
            ['booking_id' => $booking102->id, 'item_name' => 'Nước tăng lực RedBull lon'],
            ['item_type' => 'minibar', 'quantity' => 2, 'unit_price' => 25000, 'subtotal' => 50000]
        );

        // KỊCH BẢN 3: P.202 - Cần dọn dẹp (Dirty) - Khẩn cấp - Chưa phân công
        $room202 = Room::updateOrCreate(
            ['tenant_id' => $tenantId, 'building_id' => $buildingId, 'room_number' => '202'],
            [
                'floor' => 2,
                'room_type' => 'standard',
                'rental_type' => 'day',
                'price' => 7000000,
                'price_per_day' => 400000,
                'price_per_hour' => 110000,
                'price_extra_hour' => 35000,
                'status' => 'empty',
                'housekeeping_status' => 'dirty',
                'cleaning_status' => 'dirty',
                'assigned_staff_id' => null,
                'priority' => 'urgent',
                'inspection_notes' => 'Khách trước vừa trả phòng lúc 12:00, cần dọn gấp đón khách mới lúc 14:30.',
                'area' => 24,
                'description' => 'Phòng Standard khép kín, tiện nghi cơ bản.',
                'amenities' => ['wifi', 'ac', 'fridge'],
                'version' => 1,
            ]
        );

        // KỊCH BẢN 4: P.203 - Đang dọn dẹp (Cleaning) - Đã phân công cho Buồng Phòng 1
        $room203 = Room::updateOrCreate(
            ['tenant_id' => $tenantId, 'building_id' => $buildingId, 'room_number' => '203'],
            [
                'floor' => 2,
                'room_type' => 'standard',
                'rental_type' => 'day',
                'price' => 7000000,
                'price_per_day' => 420000,
                'price_per_hour' => 110000,
                'price_extra_hour' => 35000,
                'status' => 'cleaning',
                'housekeeping_status' => 'cleaning',
                'cleaning_status' => 'cleaning',
                'assigned_staff_id' => $housekeeper1->id,
                'priority' => 'high',
                'inspection_notes' => 'Đang tiến hành thay drap giường, hút bụi và khử khuẩn phòng.',
                'area' => 25,
                'description' => 'Phòng Standard 2 giường đơn.',
                'amenities' => ['wifi', 'ac', 'fridge'],
                'version' => 1,
            ]
        );

        // KỊCH BẢN 5: P.204 - Đã dọn xong (Clean) - Chờ Lễ tân Nghiệm thu đạt chuẩn
        $room204 = Room::updateOrCreate(
            ['tenant_id' => $tenantId, 'building_id' => $buildingId, 'room_number' => '204'],
            [
                'floor' => 2,
                'room_type' => 'deluxe',
                'rental_type' => 'day',
                'price' => 8500000,
                'price_per_day' => 450000,
                'price_per_hour' => 120000,
                'price_extra_hour' => 35000,
                'status' => 'empty',
                'housekeeping_status' => 'clean',
                'cleaning_status' => 'clean',
                'assigned_staff_id' => $housekeeper1->id,
                'priority' => 'normal',
                'inspection_notes' => 'Buồng phòng đã hoàn thành vệ sinh sạch sẽ, chờ Lễ tân kiểm tra nghiệm thu.',
                'area' => 28,
                'description' => 'Phòng Deluxe không gian ấm cúng, view cây xanh.',
                'amenities' => ['wifi', 'tv', 'ac', 'fridge', 'minibar'],
                'version' => 1,
            ]
        );

        // KỊCH BẢN 6: P.205 - Đã nghiệm thu (Inspected) - Sẵn sàng Check-in đón khách mới ngay lập tức
        $room205 = Room::updateOrCreate(
            ['tenant_id' => $tenantId, 'building_id' => $buildingId, 'room_number' => '205'],
            [
                'floor' => 2,
                'room_type' => 'vip',
                'rental_type' => 'day',
                'price' => 10000000,
                'price_per_day' => 520000,
                'price_per_hour' => 150000,
                'price_extra_hour' => 45000,
                'status' => 'empty',
                'housekeeping_status' => 'inspected',
                'cleaning_status' => 'inspected',
                'assigned_staff_id' => $housekeeper2->id,
                'inspected_by' => $receptionist1->id,
                'inspected_at' => Carbon::now()->subMinutes(45),
                'inspection_notes' => 'Lễ tân đã kiểm tra: Khăn tắm mới, minibar đầy đủ tem niêm phong, phòng thơm mát chuẩn 5 sao.',
                'area' => 32,
                'description' => 'Phòng Studio Balcony cao cấp, đã nghiệm thu sạch sẽ 100%, sẵn sàng check-in.',
                'amenities' => ['wifi', 'tv', 'ac', 'fridge', 'minibar', 'balcony', 'smart_lock'],
                'version' => 1,
            ]
        );

        // KỊCH BẢN 7: P.301 - Tạm dừng phục vụ / Bảo trì (Out of Service)
        $room301 = Room::updateOrCreate(
            ['tenant_id' => $tenantId, 'building_id' => $buildingId, 'room_number' => '301'],
            [
                'floor' => 3,
                'room_type' => 'deluxe',
                'rental_type' => 'day',
                'price' => 9000000,
                'price_per_day' => 480000,
                'price_per_hour' => 130000,
                'price_extra_hour' => 40000,
                'status' => 'maintenance',
                'housekeeping_status' => 'out_of_service',
                'cleaning_status' => 'out_of_service',
                'assigned_staff_id' => null,
                'priority' => 'low',
                'inspection_notes' => 'Bảo trì điều hòa và thay công tắc cảm ứng thông minh SmartRoom IoT.',
                'area' => 30,
                'description' => 'Phòng Deluxe đang trong thời gian bảo trì nâng cấp trang thiết bị.',
                'amenities' => ['wifi', 'ac'],
                'version' => 1,
            ]
        );

        // KỊCH BẢN 8: CÁC PHÒNG CÒN LẠI (P.101, P.302, P.401, P.402, P.501, P.502, P.601, P.602)
        $otherRooms = [
            ['room_number' => '101', 'floor' => 1, 'status' => 'empty', 'h_status' => 'inspected', 'day' => 450000, 'hour' => 120000],
            ['room_number' => '302', 'floor' => 3, 'status' => 'empty', 'h_status' => 'inspected', 'day' => 500000, 'hour' => 130000],
            ['room_number' => '401', 'floor' => 4, 'status' => 'empty', 'h_status' => 'inspected', 'day' => 480000, 'hour' => 120000],
            ['room_number' => '402', 'floor' => 4, 'status' => 'empty', 'h_status' => 'dirty', 'h_priority' => 'normal', 'day' => 480000, 'hour' => 120000],
            ['room_number' => '501', 'floor' => 5, 'status' => 'empty', 'h_status' => 'inspected', 'day' => 550000, 'hour' => 140000],
            ['room_number' => '502', 'floor' => 5, 'status' => 'empty', 'h_status' => 'clean', 'day' => 550000, 'hour' => 140000],
            ['room_number' => '601', 'floor' => 6, 'status' => 'empty', 'h_status' => 'inspected', 'day' => 600000, 'hour' => 150000],
            ['room_number' => '602', 'floor' => 6, 'status' => 'empty', 'h_status' => 'inspected', 'day' => 600000, 'hour' => 150000],
        ];

        foreach ($otherRooms as $item) {
            Room::updateOrCreate(
                ['tenant_id' => $tenantId, 'building_id' => $buildingId, 'room_number' => $item['room_number']],
                [
                    'floor' => $item['floor'],
                    'room_type' => 'standard',
                    'rental_type' => 'day',
                    'price' => 7500000,
                    'price_per_day' => $item['day'],
                    'price_per_hour' => $item['hour'],
                    'price_extra_hour' => 35000,
                    'status' => $item['status'],
                    'housekeeping_status' => $item['h_status'],
                    'cleaning_status' => $item['h_status'] === 'inspected' ? 'inspected' : ($item['h_status'] === 'clean' ? 'clean' : 'dirty'),
                    'assigned_staff_id' => $item['h_status'] === 'clean' ? $housekeeper2->id : null,
                    'inspected_by' => $item['h_status'] === 'inspected' ? $receptionist1->id : null,
                    'inspected_at' => $item['h_status'] === 'inspected' ? Carbon::now()->subHours(2) : null,
                    'priority' => $item['h_priority'] ?? 'normal',
                    'area' => 26,
                    'description' => "Phòng {$item['room_number']} Tòa A SmartRoom Cầu Giấy.",
                    'amenities' => ['wifi', 'ac', 'water_heater'],
                    'version' => 1,
                ]
            );
        }

        // 6. GHI NHẬN LỊCH SỬ HOUSEKEEPING LOGS MẪU
        HousekeepingLog::updateOrCreate(
            ['tenant_id' => $tenantId, 'room_id' => $room204->id, 'action' => 'clean_done'],
            [
                'user_id' => $housekeeper1->id,
                'assigned_staff_id' => $housekeeper1->id,
                'from_status' => 'cleaning',
                'to_status' => 'clean',
                'priority' => 'normal',
                'notes' => 'Nhân viên Buồng Phòng 1 đã hoàn tất vệ sinh và khử khuẩn phòng 204.',
                'created_at' => Carbon::now()->subMinutes(60),
            ]
        );

        HousekeepingLog::updateOrCreate(
            ['tenant_id' => $tenantId, 'room_id' => $room205->id, 'action' => 'inspect_passed'],
            [
                'user_id' => $receptionist1->id,
                'assigned_staff_id' => $housekeeper2->id,
                'from_status' => 'clean',
                'to_status' => 'inspected',
                'priority' => 'normal',
                'notes' => 'Lễ Tân 1 đã kiểm tra nghiệm thu phòng 205 đạt chuẩn đón khách.',
                'created_at' => Carbon::now()->subMinutes(45),
            ]
        );

        if (isset($this->command)) {
            $this->command->info("\n=======================================================================");
            $this->command->info("  HOUSEKEEPING & FRONTDESK DEMO SEEDER ĐÃ TẠO THÀNH CÔNG (FEAT_18)");
            $this->command->info("=======================================================================");
            $this->command->info("TÀI KHOẢN ĐĂNG NHẬP TEST:");
            $this->command->info("1. LỄ TÂN (Có quyền Phân công, Nghiệm thu, Check-in, Check-out):");
            $this->command->info("   - Username: letan1 (hoặc letan1_1) | Password: password (hoặc 123456)");
            $this->command->info("2. BUỒNG PHÒNG (Chỉ tiếp nhận phân công, dọn phòng, xác nhận đã dọn xong):");
            $this->command->info("   - Username: buongphong1 | Password: password (hoặc 123456)");
            $this->command->info("   - Username: buongphong2 | Password: password (hoặc 123456)");
            $this->command->info("3. QUẢN LÝ / CHỦ TRỌ:");
            $this->command->info("   - Username: chutro1 | Password: password (hoặc 123456)");
            $this->command->info("-----------------------------------------------------------------------");
            $this->command->info("DANH SÁCH PHÒNG MẪU ĐỂ TEST KỊCH BẢN:");
            $this->command->info("• P.201: Đang ở (Theo ngày, Đơn giá 500k/ngày) -> Test Check-out tính tiền + Minibar & Mua hộ + VietQR");
            $this->command->info("• P.102: Đang ở (Theo giờ, 100k/2h + 30k phụ trội) -> Test Check-out tính theo giờ");
            $this->command->info("• P.202: Cần dọn (Dirty, Ưu tiên khẩn cấp) -> Test Lễ tân Phân công hoặc Buồng phòng Bắt đầu dọn");
            $this->command->info("• P.203: Đang dọn (Cleaning, Phân công cho Buồng Phòng 1) -> Test Buồng phòng bấm Báo dọn xong (Clean)");
            $this->command->info("• P.204: Đã dọn xong (Clean) -> Test Lễ tân Nghiệm thu đạt chuẩn (Inspected) với ghi chú");
            $this->command->info("• P.205: Đã nghiệm thu (Inspected) -> Test Lễ tân bấm Check-in đón khách mới");
            $this->command->info("• P.301: Tạm dừng bảo trì (Out of service)");
            $this->command->info("=======================================================================\n");
        }
    }
}
