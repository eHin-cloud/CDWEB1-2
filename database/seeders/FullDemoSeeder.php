<?php

namespace Database\Seeders;

use App\Models\AdminActivityLog;
use App\Models\Bill;
use App\Models\Building;
use App\Models\ContactRequest;
use App\Models\Contract;
use App\Models\ElectricWaterLog;
use App\Models\Equipment;
use App\Models\HotelBooking;
use App\Models\HotelFolioItem;
use App\Models\HousekeepingLog;
use App\Models\LandlordProfile;
use App\Models\LandlordVerificationDocument;
use App\Models\LandlordVerificationRequest;
use App\Models\NotificationLog;
use App\Models\Resident;
use App\Models\ResidentRelative;
use App\Models\Review;
use App\Models\Role;
use App\Models\Room;
use App\Models\RoomEquipment;
use App\Models\Tenant;
use App\Models\Ticket;
use App\Models\User;
use App\Models\UtilityRecord;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FullDemoSeeder extends Seeder
{
    private array $roles = [];
    private ?int $adminUserId = null;

    public function run(): void
    {
        // 0. Xóa hết dữ liệu phòng trọ và dữ liệu cũ để tránh sót rác
        $this->cleanOldData();

        $this->seedRoles();

        // 1. Tạo Superadmin hệ thống
        $adminUser = User::updateOrCreate(
            ['username' => 'admin1'],
            [
                'tenant_id' => null,
                'role_id' => $this->roles['admin']->id,
                'name' => 'Admin Hệ Thống 1 (Superadmin)',
                'phone' => '0999000001',
                'email' => 'admin1@smartroom.local',
                'password' => Hash::make('123456'),
                'role' => 'admin',
                'like' => 'Admin 1',
            ]
        );
        $this->adminUserId = $adminUser->id;

        User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'tenant_id' => null,
                'role_id' => $this->roles['admin']->id,
                'name' => 'Admin hệ thống',
                'phone' => '0999999999',
                'email' => 'superadmin@smartroom.local',
                'password' => Hash::make('123456'),
                'role' => 'admin',
                'like' => 'Superadmin',
            ]
        );

        // 2. Tạo đúng 10 chủ trọ (7 đã xác minh KYC, 3 chưa xác minh) cùng các tòa chung cư mini và phòng trọ
        foreach ($this->tenantBlueprints() as $tenantIndex => $blueprint) {
            $tenant = $this->seedTenant($blueprint);
            $landlord = $this->seedLandlord($tenant, $blueprint, $tenantIndex);
            $rooms = $this->seedBuildingsAndRooms($tenant, $blueprint);
            $residents = $this->seedResidents($tenant, $rooms, $tenantIndex);

            $this->seedContracts($tenant, $rooms, $residents, $tenantIndex);
            $this->seedUtilitiesAndBills($tenant, $rooms, $residents);
            $equipment = $this->seedEquipment($tenant, $rooms);
            $this->seedTickets($tenant, $rooms, $residents);
            $this->seedReviewsAndContactRequests($rooms, $tenantIndex);
            $this->seedNotifications($tenant, $rooms, $residents);
            $this->seedActivityLogs($tenant, $landlord, $rooms, $residents, $equipment);

            if ($tenantIndex === 0) {
                $this->seedHotelBookingsAndLogs($tenant, $rooms);
            }

            // Tạo thêm tài khoản Manager cho từng Tenant
            $this->seedManager($tenant, $tenantIndex);
        }

        $this->seedGuestUsers();

        $this->printInstructions();
    }

    private function cleanOldData(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        HotelFolioItem::truncate();
        HotelBooking::truncate();
        HousekeepingLog::truncate();
        RoomEquipment::truncate();
        Equipment::truncate();
        ElectricWaterLog::truncate();
        UtilityRecord::truncate();
        Bill::truncate();
        Contract::truncate();
        ResidentRelative::truncate();
        Resident::truncate();
        Ticket::truncate();
        Review::truncate();
        ContactRequest::truncate();
        NotificationLog::truncate();
        AdminActivityLog::truncate();
        Room::truncate();
        Building::truncate();
        LandlordVerificationDocument::truncate();
        LandlordVerificationRequest::truncate();
        LandlordProfile::truncate();
        Tenant::truncate();

        User::whereNotIn('username', ['superadmin', 'admin', 'admin1', 'admin2'])->delete();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    private function seedRoles(): void
    {
        $roles = [
            'admin' => ['name' => 'Admin hệ thống', 'description' => 'Quản lý toàn bộ hệ thống, xác minh danh tính chủ trọ và kiểm tra báo cáo.'],
            'landlord' => ['name' => 'Chủ trọ / Quản lý', 'description' => 'Quản lý phòng, cư dân, hợp đồng, hóa đơn và thiết bị.'],
            'unverified_landlord' => ['name' => 'Chủ trọ chưa xác minh', 'description' => 'Chủ trọ mới đăng ký, chờ admin xác minh tài khoản.'],
            'manager' => ['name' => 'Nhân viên quản lý', 'description' => 'Nhân viên do chủ trọ bổ nhiệm để quản lý tòa nhà.'],
            'receptionist' => ['name' => 'Nhân viên lễ tân', 'description' => 'Check-in, Check-out, quản lý dịch vụ phát sinh Folio.'],
            'housekeeper' => ['name' => 'Nhân viên buồng phòng', 'description' => 'Cập nhật trạng thái phòng Sạch/Bẩn/Đang dọn.'],
            'resident' => ['name' => 'Cư dân thuê phòng', 'description' => 'Xem hóa đơn, hợp đồng và gửi yêu cầu báo hỏng.'],
            'guest' => ['name' => 'Khách tìm phòng', 'description' => 'Tìm kiếm phòng và gửi yêu cầu tư vấn.'],
        ];

        foreach ($roles as $slug => $payload) {
            $this->roles[$slug] = Role::updateOrCreate(['slug' => $slug], $payload);
        }
    }

    private function seedTenant(array $blueprint): Tenant
    {
        return Tenant::updateOrCreate(
            ['email' => $blueprint['email']],
            [
                'name' => $blueprint['name'],
                'phone' => $blueprint['phone'],
                'bank_name' => $blueprint['bank_name'],
                'bank_account_no' => $blueprint['bank_account_no'],
                'bank_account_name' => $blueprint['bank_account_name'],
                'verification_status' => $blueprint['verification_status'] ?? 'kyc_verified',
                'listing_badge' => $blueprint['listing_badge'] ?? 'kyc_verified',
                'boost_score' => $blueprint['boost_score'] ?? 0,
                'onboarding_step' => ($blueprint['verification_status'] ?? '') === 'kyc_verified' ? 4 : 2,
            ]
        );
    }

    private function seedLandlord(Tenant $tenant, array $blueprint, int $tenantIndex): User
    {
        $username = 'chutro' . ($tenantIndex + 1);
        $email = 'chutro' . ($tenantIndex + 1) . '@smartroom.local';
        $verificationStatus = $blueprint['verification_status'] ?? 'unverified';
        $isVerified = ($verificationStatus === 'kyc_verified');
        $isPending = ($verificationStatus === 'pending');

        $roleSlug = $isVerified ? 'landlord' : 'unverified_landlord';

        // 1. Tạo tài khoản chủ trọ chính (chutro1 .. chutro10)
        $landlord1 = User::updateOrCreate(
            ['username' => $username],
            [
                'tenant_id' => $tenant->id,
                'role_id' => $this->roles[$roleSlug]->id,
                'name' => $blueprint['owner_name'],
                'phone' => $blueprint['phone'] ?? ('0888000' . str_pad((string) ($tenantIndex + 1), 3, '0', STR_PAD_LEFT)),
                'email' => $email,
                'password' => Hash::make('123456'),
                'role' => $roleSlug,
                'like' => $blueprint['owner_name'],
            ]
        );

        // 2. Tạo LandlordProfile tương ứng
        $profileStatus = $isVerified ? 'verified' : ($isPending ? 'pending' : 'unverified');
        $firstBuilding = $blueprint['buildings'][0] ?? null;
        $demoCccd = $isVerified ? ('00109' . str_pad((string) ($tenantIndex + 1), 7, '0', STR_PAD_LEFT)) : null;
        $demoMst = $isVerified ? ('010' . str_pad((string) ($tenantIndex + 1), 7, '0', STR_PAD_LEFT)) : null;

        LandlordProfile::updateOrCreate(
            ['user_id' => $landlord1->id],
            [
                'tenant_id' => $tenant->id,
                'full_name' => $landlord1->name,
                'phone' => $landlord1->phone,
                'property_name' => $tenant->name,
                'property_address' => $firstBuilding['address'] ?? 'Hà Nội',
                'national_id' => $demoCccd,
                'business_license' => $demoMst,
                'status' => $profileStatus,
                'verification_status' => $verificationStatus,
            ]
        );

        // 3. Tạo yêu cầu KYC nếu đã xác minh (approved) hoặc đang chờ duyệt (pending)
        if ($isVerified || $isPending) {
            $reqStatus = $isVerified ? 'approved' : 'pending';
            $verificationRequest = LandlordVerificationRequest::updateOrCreate(
                ['tenant_id' => $tenant->id, 'user_id' => $landlord1->id],
                [
                    'type' => 'kyc',
                    'cccd_number' => '00109' . str_pad((string) ($tenantIndex + 1), 7, '0', STR_PAD_LEFT),
                    'admin_review_consent_given' => true,
                    'admin_review_consent_at' => Carbon::now()->subDays(5),
                    'admin_review_consent_ip' => '127.0.0.1',
                    'status' => $reqStatus,
                    'reviewed_by' => $isVerified ? ($this->adminUserId ?? User::where('role', 'admin')->value('id')) : null,
                    'reviewed_at' => $isVerified ? Carbon::now()->subDays(2) : null,
                    'reject_reason' => null,
                ]
            );

            foreach (['cccd_front' => 'Căn cước mặt trước.jpg', 'cccd_back' => 'Căn cước mặt sau.jpg'] as $docType => $filename) {
                LandlordVerificationDocument::updateOrCreate(
                    ['verification_request_id' => $verificationRequest->id, 'document_type' => $docType],
                    [
                        'disk' => 'local',
                        'file_path' => 'kyc/' . $docType . '_' . ($tenantIndex + 1) . '.jpg',
                        'original_filename' => $filename,
                        'mime_type' => 'image/jpeg',
                        'size_bytes' => 102400,
                        'sha256_checksum' => hash('sha256', $filename . ($tenantIndex + 1)),
                        'status' => $reqStatus,
                    ]
                );
            }
        }

        // Tạo sẵn nhân viên Lễ tân & Buồng phòng cho Tenant 1 (SmartRoom Cầu Giấy)
        if ($tenantIndex === 0) {
            $recRole = $this->roles['receptionist'] ?? Role::where('slug', 'receptionist')->first();
            $hkRole = $this->roles['housekeeper'] ?? Role::where('slug', 'housekeeper')->first();

            User::updateOrCreate(
                ['username' => 'letan1_1'],
                [
                    'tenant_id' => $tenant->id,
                    'role_id' => $recRole?->id,
                    'name' => 'Lê Thị Thu Thảo (Lễ Tân)',
                    'phone' => '0961000001',
                    'email' => 'letan1@demo.smartroom.local',
                    'password' => Hash::make('123456'),
                    'role' => 'receptionist',
                    'status' => 'active',
                    'like' => 'Lễ Tân 1 Cầu Giấy',
                ]
            );

            User::updateOrCreate(
                ['username' => 'buongphong1_1'],
                [
                    'tenant_id' => $tenant->id,
                    'role_id' => $hkRole?->id,
                    'name' => 'Nguyễn Văn Dũng (Buồng Phòng 1)',
                    'phone' => '0951000001',
                    'email' => 'buongphong1@demo.smartroom.local',
                    'password' => Hash::make('123456'),
                    'role' => 'housekeeper',
                    'status' => 'active',
                    'like' => 'Buồng Phòng 1 Cầu Giấy',
                ]
            );

            User::updateOrCreate(
                ['username' => 'buongphong1_2'],
                [
                    'tenant_id' => $tenant->id,
                    'role_id' => $hkRole?->id,
                    'name' => 'Trần Thị Lan (Buồng Phòng 2)',
                    'phone' => '0951000002',
                    'email' => 'buongphong2@demo.smartroom.local',
                    'password' => Hash::make('123456'),
                    'role' => 'housekeeper',
                    'status' => 'active',
                    'like' => 'Buồng Phòng 2 Cầu Giấy',
                ]
            );
        }

        return $landlord1;
    }

    private function seedManager(Tenant $tenant, int $tenantIndex): void
    {
        // Quan ly duoc tao chuan trong SystemUsersSeeder
    }

    private function seedBuildingsAndRooms(Tenant $tenant, array $blueprint): array
    {
        $rooms = [];

        $bp1 = User::where(fn ($q) => $q->where('tenant_id', $tenant->id)->orWhereNull('tenant_id'))
            ->whereIn('username', ['buongphong1_1', 'buongphong1'])
            ->first();
        $bp2 = User::where(fn ($q) => $q->where('tenant_id', $tenant->id)->orWhereNull('tenant_id'))
            ->whereIn('username', ['buongphong1_2', 'buongphong2'])
            ->first();
        $lt1 = User::where(fn ($q) => $q->where('tenant_id', $tenant->id)->orWhereNull('tenant_id'))
            ->whereIn('username', ['letan1_1', 'letan1'])
            ->first();

        foreach ($blueprint['buildings'] as $buildingData) {
            $building = Building::updateOrCreate(
                ['tenant_id' => $tenant->id, 'name' => $buildingData['name']],
                [
                    'address' => $buildingData['address'],
                    'description' => $buildingData['description'],
                    'property_type' => 'hotel',
                    'checkin_time' => '14:00:00',
                    'checkout_time' => '12:00:00',
                ]
            );

            foreach ($buildingData['rooms'] as $roomData) {
                $media = $this->mediaForRoom($buildingData['name'], (string) $roomData['room_number']);

                $staffId = null;
                if (!empty($roomData['staff_slot'])) {
                    $staffId = $roomData['staff_slot'] === 1 ? ($bp1?->id) : ($bp2?->id);
                }

                $inspectedBy = null;
                $inspectedAt = null;
                if (($roomData['housekeeping_status'] ?? '') === 'inspected') {
                    $inspectedBy = $lt1?->id;
                    $inspectedAt = Carbon::now()->subMinutes(rand(30, 240));
                }

                $room = Room::updateOrCreate(
                    ['building_id' => $building->id, 'room_number' => $roomData['room_number']],
                    [
                        'tenant_id' => $tenant->id,
                        'floor' => $roomData['floor'],
                        'status' => $roomData['status'],
                        'housekeeping_status' => $roomData['housekeeping_status'] ?? 'clean',
                        'cleaning_status' => $roomData['cleaning_status'] ?? 'clean',
                        'priority' => $roomData['priority'] ?? 'normal',
                        'inspection_notes' => $roomData['inspection_notes'] ?? null,
                        'rental_type' => $roomData['rental_type'] ?? 'month',
                        'price_per_day' => $roomData['price_per_day'] ?? null,
                        'price_per_hour' => $roomData['price_per_hour'] ?? null,
                        'price_extra_hour' => $roomData['price_extra_hour'] ?? null,
                        'assigned_staff_id' => $staffId,
                        'inspected_by' => $inspectedBy,
                        'inspected_at' => $inspectedAt,
                        'room_type' => $roomData['room_type'],
                        'price' => $roomData['price'],
                        'area' => $roomData['area'],
                        'amenities' => $roomData['amenities'],
                        'description' => $buildingData['name'] . ' - phòng ' . $roomData['room_number'] . ' phục vụ test đầy đủ trạng thái.',
                        'image' => $media['image'],
                        'images' => $media['images'],
                        'video' => $media['video'],
                        'version' => 1,
                    ]
                );

                $rooms[$buildingData['code'] . '-' . $roomData['room_number']] = $room;
            }
        }

        return $rooms;
    }

    private function seedHotelBookingsAndLogs(Tenant $tenant, array $rooms): void
    {
        // 1. Booking P.102 thuê theo giờ
        $room102A = collect($rooms)->first(fn ($r) => $r->room_number === '102' && str_contains($r->building?->name ?? '', 'Tòa A'));
        if ($room102A) {
            $booking102 = HotelBooking::updateOrCreate(
                ['booking_code' => 'HB-CG102'],
                [
                    'tenant_id' => $tenant->id,
                    'room_id' => $room102A->id,
                    'guest_name' => 'Lê Hoàng Long',
                    'guest_phone' => '0933888999',
                    'guest_cccd' => '001098054321',
                    'rental_type' => 'hour',
                    'check_in_at' => Carbon::now()->subMinutes(210),
                    'unit_rate' => 100000,
                    'deposit_amount' => 50000,
                    'status' => 'checked_in',
                    'payment_status' => 'unpaid',
                    'note' => 'Khách thuê theo giờ, yêu cầu phòng yên tĩnh.',
                ]
            );

            HotelFolioItem::updateOrCreate(
                ['booking_id' => $booking102->id, 'item_name' => 'Nước tăng lực RedBull lon'],
                ['item_type' => 'minibar', 'quantity' => 2, 'unit_price' => 25000, 'subtotal' => 50000]
            );
        }

        // 2. Booking P.201 Tòa B thuê theo ngày
        $room201B = collect($rooms)->first(fn ($r) => $r->room_number === '201' && str_contains($r->building?->name ?? '', 'Tòa B'));
        if ($room201B) {
            $booking201 = HotelBooking::updateOrCreate(
                ['booking_code' => 'HB-CG201'],
                [
                    'tenant_id' => $tenant->id,
                    'room_id' => $room201B->id,
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
        }

        // 3. Lịch sử dọn phòng mẫu
        $room204A = collect($rooms)->first(fn ($r) => $r->room_number === '204' && str_contains($r->building?->name ?? '', 'Tòa A'));
        $bp1 = User::whereIn('username', ['buongphong1_1', 'buongphong1'])->first();
        $lt1 = User::whereIn('username', ['letan1_1', 'letan1'])->first();

        if ($room204A && $bp1) {
            HousekeepingLog::updateOrCreate(
                ['tenant_id' => $tenant->id, 'room_id' => $room204A->id, 'action' => 'clean_done'],
                [
                    'user_id' => $bp1->id,
                    'assigned_staff_id' => $bp1->id,
                    'from_status' => 'cleaning',
                    'to_status' => 'clean',
                    'priority' => 'normal',
                    'notes' => 'Nhân viên Buồng Phòng đã hoàn tất vệ sinh và khử khuẩn phòng 204.',
                    'created_at' => Carbon::now()->subMinutes(60),
                ]
            );
        }

        $room205A = collect($rooms)->first(fn ($r) => $r->room_number === '205' && str_contains($r->building?->name ?? '', 'Tòa A'));
        if ($room205A && $lt1 && $bp1) {
            HousekeepingLog::updateOrCreate(
                ['tenant_id' => $tenant->id, 'room_id' => $room205A->id, 'action' => 'inspect_passed'],
                [
                    'user_id' => $lt1->id,
                    'assigned_staff_id' => $bp1->id,
                    'from_status' => 'clean',
                    'to_status' => 'inspected',
                    'priority' => 'normal',
                    'notes' => 'Lễ Tân đã kiểm tra nghiệm thu phòng 205 đạt chuẩn đón khách.',
                    'created_at' => Carbon::now()->subMinutes(30),
                ]
            );
        }
    }

    private function seedResidents(Tenant $tenant, array $rooms, int $tenantIndex): array
    {
        $primaryResidents = [];
        $occupiedRooms = collect($rooms)
            ->filter(fn (Room $room) => in_array($room->status, ['occupied', 'overdue'], true))
            ->values();

        foreach ($occupiedRooms as $index => $room) {
            $seedNo = (($tenantIndex + 1) * 100) + $index + 1;

            // 1. Tạo cư dân chính (đứng tên hợp đồng, nhận hóa đơn)
            $name1 = $this->residentNames()[$index % count($this->residentNames())];
            $email1 = 'resident' . $seedNo . '-1@demo.smartroom.local';

            $user1 = User::updateOrCreate(
                ['username' => 'demo-resident-' . $seedNo . '-1'],
                [
                    'tenant_id' => $tenant->id,
                    'role_id' => $this->roles['resident']->id,
                    'name' => $name1,
                    'phone' => '0777' . str_pad((string) ($seedNo * 10 + 1), 6, '0', STR_PAD_LEFT),
                    'email' => $email1,
                    'password' => Hash::make('password'),
                    'role' => 'user',
                    'like' => 'Khách thuê chính demo',
                ]
            );

            $resident1 = Resident::updateOrCreate(
                ['email' => $email1],
                [
                    'tenant_id' => $tenant->id,
                    'room_id' => $room->id,
                    'user_id' => $user1->id,
                    'name' => $name1,
                    'dob' => Carbon::create(1992 + ($index % 10), ($index % 12) + 1, 10)->toDateString(),
                    'phone' => $user1->phone,
                    'cccd' => '001' . str_pad((string) ($seedNo * 10 + 1), 9, '0', STR_PAD_LEFT),
                    'hometown' => ['Hà Nội', 'Nam Định', 'Thái Bình', 'Bắc Ninh', 'Đà Nẵng'][$index % 5],
                    'start_date' => Carbon::today()->subMonths(12 - ($index % 6))->toDateString(),
                    'status' => 'active',
                    'temporary_residence_status' => ['registered', 'none', 'absent'][$index % 3],
                    'version' => 1,
                ]
            );

            // 2. Tạo cư dân ở ghép (bạn chung phòng, có tài khoản đăng nhập riêng)
            $name2 = $this->residentNames()[($index + 1) % count($this->residentNames())] . ' (Ở Ghép)';
            $email2 = 'resident' . $seedNo . '-2@demo.smartroom.local';

            $user2 = User::updateOrCreate(
                ['username' => 'demo-resident-' . $seedNo . '-2'],
                [
                    'tenant_id' => $tenant->id,
                    'role_id' => $this->roles['resident']->id,
                    'name' => $name2,
                    'phone' => '0777' . str_pad((string) ($seedNo * 10 + 2), 6, '0', STR_PAD_LEFT),
                    'email' => $email2,
                    'password' => Hash::make('password'),
                    'role' => 'user',
                    'like' => 'Khách ở ghép demo',
                ]
            );

            Resident::updateOrCreate(
                ['email' => $email2],
                [
                    'tenant_id' => $tenant->id,
                    'room_id' => $room->id,
                    'user_id' => $user2->id,
                    'name' => $name2,
                    'dob' => Carbon::create(1994 + ($index % 8), (($index + 4) % 12) + 1, 15)->toDateString(),
                    'phone' => $user2->phone,
                    'cccd' => '001' . str_pad((string) ($seedNo * 10 + 2), 9, '0', STR_PAD_LEFT),
                    'hometown' => ['Thanh Hóa', 'Nghệ An', 'Hải Phòng', 'Quảng Ninh', 'Lạng Sơn'][$index % 5],
                    'start_date' => Carbon::today()->subMonths(6 - ($index % 3))->toDateString(),
                    'status' => 'active',
                    'temporary_residence_status' => ['registered', 'none', 'absent'][($index + 1) % 3],
                    'version' => 1,
                ]
            );

            if ($index % 3 === 0) {
                ResidentRelative::updateOrCreate(
                    ['resident_id' => $resident1->id, 'name' => 'Người thân ' . $room->room_number],
                    [
                        'dob' => Carbon::create(1998, 6, 15)->toDateString(),
                        'cccd' => 'REL' . str_pad((string) $seedNo, 9, '0', STR_PAD_LEFT),
                        'phone' => '0666' . str_pad((string) $seedNo, 6, '0', STR_PAD_LEFT),
                        'hometown' => 'Hà Nội',
                        'relationship' => 'Anh/Chị/Em',
                        'temporary_residence_status' => 'registered',
                        'start_date' => Carbon::today()->subDays(20)->toDateString(),
                        'end_date' => Carbon::today()->addMonths(2)->toDateString(),
                        'version' => 1,
                    ]
                );
            }

            $primaryResidents[$room->id] = $resident1;
        }

        return $primaryResidents;
    }

    private function seedContracts(Tenant $tenant, array $rooms, array $residents, int $tenantIndex): void
    {
        foreach ($residents as $roomId => $resident) {
            $room = collect($rooms)->firstWhere('id', $roomId);
            if (!$room) {
                continue;
            }

            $endingSoon = ((int) substr($room->room_number, -1)) % 4 === 0;
            Contract::updateOrCreate(
                ['contract_code' => 'DEMO-HD-T' . ($tenantIndex + 1) . '-' . $room->id],
                [
                    'tenant_id' => $tenant->id,
                    'room_id' => $room->id,
                    'resident_id' => $resident->id,
                    'start_date' => $resident->start_date,
                    'end_date' => $endingSoon
                        ? Carbon::today()->addDays(18)->toDateString()
                        : Carbon::parse($resident->start_date)->addYear()->toDateString(),
                    'deposit' => $room->price,
                    'status' => 'active',
                    'terms' => 'Hợp đồng demo cho phòng ' . $room->room_number . '. Thanh toán trước ngày 10 hằng tháng.',
                    'signature' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=',
                ]
            );
        }
    }

    private function seedUtilitiesAndBills(Tenant $tenant, array $rooms, array $residents): void
    {
        $months = [
            Carbon::today()->subMonths(2)->format('Y-m'),
            Carbon::today()->subMonth()->format('Y-m'),
            Carbon::today()->format('Y-m'),
        ];

        foreach ($rooms as $room) {
            if (!isset($residents[$room->id])) {
                continue;
            }

            foreach ($months as $monthIndex => $month) {
                $electricUsage = 70 + (($room->id + $monthIndex) % 65);
                $waterUsage = 4 + (($room->id + $monthIndex) % 8);
                $oldElectric = 800 + ($room->id * 7) + ($monthIndex * 130);
                $oldWater = 100 + ($room->id % 20) + ($monthIndex * 10);
                $electricPrice = $tenant->bank_name === 'VCB' ? 3800 : 3500;
                $waterPrice = $tenant->bank_name === 'VCB' ? 18000 : 15000;
                $serviceCost = 150000;
                $total = $room->price + ($electricUsage * $electricPrice) + ($waterUsage * $waterPrice) + $serviceCost;
                $isCurrent = $month === Carbon::today()->format('Y-m');
                $billStatus = $isCurrent ? ($room->status === 'overdue' ? 'overdue' : 'pending') : 'paid';
                $paymentDate = $billStatus === 'paid' ? Carbon::parse($month . '-10')->addDays($room->id % 5) : null;

                $log = ElectricWaterLog::updateOrCreate(
                    ['room_id' => $room->id, 'billing_month' => $month],
                    [
                        'tenant_id' => $tenant->id,
                        'old_electricity' => $oldElectric,
                        'new_electricity' => $oldElectric + $electricUsage,
                        'old_water' => $oldWater,
                        'new_water' => $oldWater + $waterUsage,
                        'electricity_price' => $electricPrice,
                        'water_price' => $waterPrice,
                    ]
                );

                Bill::updateOrCreate(
                    ['room_id' => $room->id, 'billing_month' => $month],
                    [
                        'tenant_id' => $tenant->id,
                        'electric_water_log_id' => $log->id,
                        'room_price' => $room->price,
                        'electricity_usage' => $electricUsage,
                        'electricity_cost' => $electricUsage * $electricPrice,
                        'water_usage' => $waterUsage,
                        'water_cost' => $waterUsage * $waterPrice,
                        'service_cost' => $serviceCost,
                        'total_amount' => $total,
                        'status' => $billStatus,
                        'payment_date' => $paymentDate,
                        'vietqr_url' => $this->vietQrUrl($tenant, $room, $month, $total),
                    ]
                );

                UtilityRecord::updateOrCreate(
                    ['room_id' => $room->id, 'billing_month' => $month],
                    [
                        'tenant_id' => $tenant->id,
                        'old_electricity' => $oldElectric,
                        'new_electricity' => $oldElectric + $electricUsage,
                        'old_water' => $oldWater,
                        'new_water' => $oldWater + $waterUsage,
                        'electricity_price' => $electricPrice,
                        'water_price' => $waterPrice,
                        'status' => $billStatus === 'pending' ? 'sent' : $billStatus,
                        'payment_date' => $paymentDate,
                        'payment_method' => $paymentDate ? ['cash', 'bank_transfer', 'vietqr'][$room->id % 3] : null,
                    ]
                );
            }
        }
    }

    private function seedEquipment(Tenant $tenant, array $rooms): array
    {
        $equipmentItems = [
            ['code' => 'AC', 'name' => 'Điều hòa 9000 BTU', 'unit' => 'cái', 'quantity' => 40],
            ['code' => 'WM', 'name' => 'Máy giặt mini', 'unit' => 'cái', 'quantity' => 20],
            ['code' => 'FR', 'name' => 'Tủ lạnh 90L', 'unit' => 'cái', 'quantity' => 30],
            ['code' => 'BED', 'name' => 'Giường sắt 1m2', 'unit' => 'cái', 'quantity' => 50],
            ['code' => 'LOCK', 'name' => 'Khóa vân tay', 'unit' => 'cái', 'quantity' => 40],
            ['code' => 'CAM', 'name' => 'Camera hành lang', 'unit' => 'cái', 'quantity' => 20],
        ];

        $equipment = [];
        foreach ($equipmentItems as $item) {
            $equipment[$item['code']] = Equipment::updateOrCreate(
                ['tenant_id' => $tenant->id, 'code' => $item['code']],
                [
                    'name' => $item['name'],
                    'unit' => $item['unit'],
                    'total_quantity' => $item['quantity'],
                    'allocated_quantity' => 0,
                    'description' => 'Demo equipment for full test data.',
                    'version' => 1,
                ]
            );
        }

        RoomEquipment::where('tenant_id', $tenant->id)->delete();

        foreach (array_values($rooms) as $index => $room) {
            if ($room->status === 'empty') {
                continue;
            }

            foreach (['AC' => 1, 'BED' => 1, 'LOCK' => 1] as $code => $quantity) {
                $this->allocateEquipment($tenant, $room, $equipment[$code], $quantity);
            }
            if ($index % 2 === 0) {
                $this->allocateEquipment($tenant, $room, $equipment['FR'], 1);
            }
            if ($index % 3 === 0) {
                $this->allocateEquipment($tenant, $room, $equipment['WM'], 1);
            }
        }

        foreach ($equipment as $item) {
            $item->update([
                'allocated_quantity' => RoomEquipment::where('equipment_id', $item->id)->sum('quantity'),
                'version' => $item->version + 1,
            ]);
        }

        return $equipment;
    }

    private function allocateEquipment(Tenant $tenant, Room $room, Equipment $equipment, int $quantity): void
    {
        RoomEquipment::updateOrCreate(
            ['room_id' => $room->id, 'equipment_id' => $equipment->id],
            [
                'tenant_id' => $tenant->id,
                'quantity' => $quantity,
                'last_allocated_at' => Carbon::today()->subDays($room->id % 20),
            ]
        );
    }

    private function seedTickets(Tenant $tenant, array $rooms, array $residents): void
    {
        foreach (array_values($rooms) as $index => $room) {
            if (!isset($residents[$room->id]) || $index % 3 !== 0) {
                continue;
            }

            Ticket::updateOrCreate(
                ['room_id' => $room->id, 'title' => 'Báo hỏng phòng ' . $room->room_number],
                [
                    'tenant_id' => $tenant->id,
                    'resident_id' => $residents[$room->id]->id,
                    'description' => ['Điều hòa không mát.', 'Vòi sen bị rò nước.', 'Khóa cửa bị kẹt.'][$index % 3],
                    'category' => ['điện', 'nước', 'nội thất'][$index % 3],
                    'status' => ['pending', 'processing', 'resolved'][$index % 3],
                    'assigned_to' => $index % 3 === 0 ? null : 'Tổ bảo trì',
                ]
            );
        }
    }

    private function seedReviewsAndContactRequests(array $rooms, int $tenantIndex): void
    {
        foreach (array_values($rooms) as $index => $room) {
            if ($index < 5) {
                Review::updateOrCreate(
                    ['room_id' => $room->id, 'author_name' => 'Khách tham quan ' . ($tenantIndex + 1) . '-' . ($index + 1)],
                    [
                        'rating' => 3 + ($index % 3),
                        'comment' => 'Phòng sạch, vị trí thuận tiện, phù hợp để test đánh giá công khai.',
                    ]
                );
            }

            if ($room->status === 'empty') {
                ContactRequest::updateOrCreate(
                    ['room_id' => $room->id, 'phone' => '0555' . str_pad((string) ($tenantIndex * 100 + $index), 6, '0', STR_PAD_LEFT)],
                    [
                        'name' => 'Khách cần tư vấn ' . $room->room_number,
                        'message' => 'Tôi muốn xem phòng và hỏi về chi phí cọc.',
                        'status' => $index % 2 === 0 ? 'pending' : 'processed',
                    ]
                );
            }
        }
    }

    private function seedNotifications(Tenant $tenant, array $rooms, array $residents): void
    {
        NotificationLog::where('tenant_id', $tenant->id)->where('meta->seeded', true)->delete();

        foreach (array_values($residents) as $index => $resident) {
            $room = collect($rooms)->firstWhere('id', $resident->room_id);
            NotificationLog::create([
                'tenant_id' => $tenant->id,
                'type' => $index % 2 === 0 ? 'payment_reminder' : 'contract_notice',
                'channel' => ['zalo', 'email', 'sms'][$index % 3],
                'recipient_name' => $resident->name,
                'recipient_contact' => $resident->phone,
                'subject' => 'Thông báo phòng ' . ($room->room_number ?? 'N/A'),
                'message' => 'Dữ liệu demo thông báo cho cư dân ' . $resident->name,
                'status' => ['sent', 'failed', 'queued'][$index % 3],
                'target_type' => Resident::class,
                'target_id' => $resident->id,
                'meta' => ['seeded' => true, 'room_number' => $room->room_number ?? null],
                'sent_at' => Carbon::now()->subDays($index),
            ]);
        }
    }

    private function seedActivityLogs(Tenant $tenant, User $landlord, array $rooms, array $residents, array $equipment): void
    {
        AdminActivityLog::where('tenant_id', $tenant->id)->where('metadata->seeded', true)->delete();

        $subjects = [
            ['create', 'rooms', 'Tạo phòng demo đầu tiên', reset($rooms)],
            ['create', 'residents', 'Thêm cư dân vào phòng demo', reset($residents) ?: null],
            ['create', 'utilities', 'Chốt điện nước tháng hiện tại', UtilityRecord::where('tenant_id', $tenant->id)->latest()->first()],
            ['payment', 'payments', 'Ghi nhận thanh toán demo', UtilityRecord::where('tenant_id', $tenant->id)->where('status', 'paid')->latest()->first()],
            ['allocate', 'equipment', 'Bàn giao thiết bị demo', reset($equipment) ?: null],
            ['notify', 'notifications', 'Gửi thông báo nhắc nợ demo', NotificationLog::where('tenant_id', $tenant->id)->latest()->first()],
        ];

        foreach ($subjects as $index => [$action, $module, $description, $subject]) {
            AdminActivityLog::create([
                'tenant_id' => $tenant->id,
                'user_id' => $landlord->id,
                'user_name' => $landlord->name,
                'action' => $action,
                'module' => $module,
                'description' => $description,
                'subject_type' => $subject ? $subject::class : null,
                'subject_id' => $subject?->getKey(),
                'ip_address' => '127.0.0.1',
                'method' => 'SEED',
                'url' => '/full-seeder',
                'user_agent' => 'FullDemoSeeder',
                'before_values' => null,
                'after_values' => $subject ? ['id' => $subject->getKey()] : null,
                'metadata' => ['seeded' => true, 'index' => $index],
                'created_at' => Carbon::now()->subHours(24 - $index),
                'updated_at' => Carbon::now()->subHours(24 - $index),
            ]);
        }
    }

    private function seedGuestUsers(): void
    {
        // Khach vang lai duoc tao chuan trong SystemUsersSeeder (khach1..khach10)
    }

    private function vietQrUrl(Tenant $tenant, Room $room, string $month, int $amount): string
    {
        $info = 'Thanh toán phòng ' . $room->room_number . ' tháng ' . substr($month, 5, 2);

        return 'https://img.vietqr.io/image/' . $tenant->bank_name . '-' . $tenant->bank_account_no
            . '-compact.png?amount=' . $amount
            . '&addInfo=' . rawurlencode($info)
            . '&accountName=' . rawurlencode($tenant->bank_account_name ?? $tenant->name);
    }

    private function tenantBlueprints(): array
    {
        return [
            // ==========================================
            // 7 CHỦ TRỌ ĐÃ XÁC MINH (KYC VERIFIED)
            // ==========================================
            [
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
                'buildings' => $this->buildingBlueprints(
                    'CG',
                    'SmartRoom Cầu Giấy',
                    'Số 12 Ngõ 105 Xuân Thủy, Cầu Giấy, Hà Nội',
                    'Số 36 Ngõ 86 Chùa Hà, Cầu Giấy, Hà Nội',
                    3500000
                ),
            ],
            [
                'name' => 'Hệ thống Chung cư mini Rentry Home Thanh Xuân',
                'email' => 'demo-thanhxuan@smartroom.local',
                'phone' => '0988000002',
                'owner_name' => 'Lê Quản Lý Thanh Xuân',
                'bank_name' => 'VCB',
                'bank_account_no' => '999988880002',
                'bank_account_name' => 'LE QUAN LY THANH XUAN',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 90,
                'buildings' => $this->buildingBlueprints(
                    'TX',
                    'Rentry Home Thanh Xuân',
                    'Số 85 Vũ Tông Phan, Thanh Xuân, Hà Nội',
                    'Số 120 Khương Đình, Thanh Xuân, Hà Nội',
                    4000000
                ),
            ],
            [
                'name' => 'Hệ thống Chung cư mini Sen Vàng Đống Đa',
                'email' => 'demo-dongda@smartroom.local',
                'phone' => '0988000003',
                'owner_name' => 'Phạm Văn Đống Đa',
                'bank_name' => 'MB',
                'bank_account_no' => '999988880003',
                'bank_account_name' => 'PHAM VAN DONG DA',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 88,
                'buildings' => $this->buildingBlueprints(
                    'DD',
                    'Sen Vàng Đống Đa',
                    'Số 18 Ngõ 198 Xã Đàn, Đống Đa, Hà Nội',
                    'Số 45 Chùa Bộc, Đống Đa, Hà Nội',
                    3800000
                ),
            ],
            [
                'name' => 'Hệ thống Chung cư mini Hồ Tây Panorama Tây Hồ',
                'email' => 'demo-tayho@smartroom.local',
                'phone' => '0988000004',
                'owner_name' => 'Hoàng Văn Tây Hồ',
                'bank_name' => 'Agribank',
                'bank_account_no' => '999988880004',
                'bank_account_name' => 'HOANG VAN TAY HO',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 92,
                'buildings' => $this->buildingBlueprints(
                    'TH',
                    'Hồ Tây Panorama',
                    'Số 210 Lạc Long Quân, Tây Hồ, Hà Nội',
                    'Số 99 Trích Sài, Tây Hồ, Hà Nội',
                    5500000
                ),
            ],
            [
                'name' => 'Hệ thống Chung cư mini Times Light Hai Bà Trưng',
                'email' => 'demo-hbt@smartroom.local',
                'phone' => '0988000005',
                'owner_name' => 'Vũ Thị Hai Bà Trưng',
                'bank_name' => 'VCB',
                'bank_account_no' => '999988880005',
                'bank_account_name' => 'VU THI HAI BA TRUNG',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 86,
                'buildings' => $this->buildingBlueprints(
                    'HBT',
                    'Times Light Hai Bà Trưng',
                    'Số 250 Bạch Mai, Hai Bà Trưng, Hà Nội',
                    'Số 158 Minh Khai, Hai Bà Trưng, Hà Nội',
                    4500000
                ),
            ],
            [
                'name' => 'Hệ thống Chung cư mini Liễu Giai Riverside Ba Đình',
                'email' => 'demo-badinh@smartroom.local',
                'phone' => '0988000006',
                'owner_name' => 'Trần Văn Ba Đình',
                'bank_name' => 'BIDV',
                'bank_account_no' => '999988880006',
                'bank_account_name' => 'TRAN VAN BA DINH',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 89,
                'buildings' => $this->buildingBlueprints(
                    'BD',
                    'Liễu Giai Ba Đình',
                    'Số 68 Liễu Giai, Ba Đình, Hà Nội',
                    'Số 142 Đội Cấn, Ba Đình, Hà Nội',
                    4800000
                ),
            ],
            [
                'name' => 'Hệ thống Chung cư mini Mỹ Đình Star Nam Từ Liêm',
                'email' => 'demo-mydinh@smartroom.local',
                'phone' => '0988000007',
                'owner_name' => 'Bùi Tiến Đạt',
                'bank_name' => 'Techcombank',
                'bank_account_no' => '999988880007',
                'bank_account_name' => 'BUI TIEN DAT',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 87,
                'buildings' => $this->buildingBlueprints(
                    'NTL',
                    'Mỹ Đình Star',
                    'Số 56 Đình Thôn, Nam Từ Liêm, Hà Nội',
                    'Số 89 Lê Đức Thọ, Nam Từ Liêm, Hà Nội',
                    4200000
                ),
            ],

            // ==========================================
            // 3 CHỦ TRỌ CHƯA XÁC MINH (PENDING & UNVERIFIED)
            // ==========================================
            [
                'name' => 'Hệ thống Chung cư mini Linh Đàm Green Hoàng Mai',
                'email' => 'demo-hoangmai@smartroom.local',
                'phone' => '0988000008',
                'owner_name' => 'Hoàng Văn Hùng',
                'bank_name' => 'VietinBank',
                'bank_account_no' => '999988880008',
                'bank_account_name' => 'HOANG VAN HUNG',
                'verification_status' => 'pending',
                'listing_badge' => 'unverified',
                'boost_score' => 0,
                'buildings' => $this->buildingBlueprints(
                    'HM',
                    'Linh Đàm Green',
                    'Bán đảo Linh Đàm, Hoàng Mai, Hà Nội',
                    'Số 72 Đại Từ, Hoàng Mai, Hà Nội',
                    3600000
                ),
            ],
            [
                'name' => 'Hệ thống Chung cư mini Cổ Nhuế House Bắc Từ Liêm',
                'email' => 'demo-bactuliem@smartroom.local',
                'phone' => '0988000009',
                'owner_name' => 'Nguyễn Thị Mai',
                'bank_name' => 'ACB',
                'bank_account_no' => '999988880009',
                'bank_account_name' => 'NGUYEN THI MAI',
                'verification_status' => 'pending',
                'listing_badge' => 'unverified',
                'boost_score' => 0,
                'buildings' => $this->buildingBlueprints(
                    'BTL',
                    'Cổ Nhuế House',
                    'Số 18 Ngõ 136 Cổ Nhuế, Bắc Từ Liêm, Hà Nội',
                    'Số 64 Trần Cung, Bắc Từ Liêm, Hà Nội',
                    3400000
                ),
            ],
            [
                'name' => 'Hệ thống Chung cư mini Hà Đông Central Hà Đông',
                'email' => 'demo-hadong@smartroom.local',
                'phone' => '0988000010',
                'owner_name' => 'Trịnh Văn Lâm',
                'bank_name' => 'Sacombank',
                'bank_account_no' => '999988880010',
                'bank_account_name' => 'TRINH VAN LAM',
                'verification_status' => 'unverified',
                'listing_badge' => 'unverified',
                'boost_score' => 0,
                'buildings' => $this->buildingBlueprints(
                    'HD',
                    'Hà Đông Central',
                    'Số 112 Quang Trung, Hà Đông, Hà Nội',
                    'Số 35 Nguyễn Khuyến, Hà Đông, Hà Nội',
                    3300000
                ),
            ],
        ];
    }

    private function buildingBlueprints(string $code, string $buildingName, string $address1, string $address2, int $basePrice): array
    {
        return [
            [
                'code' => $code . 'A',
                'name' => 'Chung cư mini ' . $buildingName . ' - Tòa A',
                'address' => $address1,
                'description' => 'Chung cư mini ' . $buildingName . ' - Tòa A hiện đại, thang máy, an ninh 24/7, khóa vân tay, giờ giấc tự do.',
                'rooms' => $this->roomBlueprints($basePrice, 'Chung cư mini ' . $buildingName . ' - Tòa A', 'A'),
            ],
            [
                'code' => $code . 'B',
                'name' => 'Chung cư mini ' . $buildingName . ' - Tòa B',
                'address' => $address2,
                'description' => 'Chung cư mini ' . $buildingName . ' - Tòa B cao cấp, ban công thoáng mát, đầy đủ nội thất tiện nghi khép kín.',
                'rooms' => $this->roomBlueprints($basePrice + 300000, 'Chung cư mini ' . $buildingName . ' - Tòa B', 'B'),
            ],
        ];
    }

    private function roomBlueprints(int $basePrice, string $buildingTitle = '', string $buildingCode = 'A'): array
    {
        $amenitiesPool = [
            ['điều hòa', 'nóng lạnh', 'wifi', 'cho nuôi thú cưng', 'gác lửng', 'wc khép kín', 'tủ lạnh'],
            ['điều hòa', 'nóng lạnh', 'ban công', 'wc khép kín', 'tủ quần áo', 'máy giặt'],
            ['điều hòa', 'gác lửng', 'wc khép kín', 'wifi', 'bếp từ'],
            ['điều hòa', 'nóng lạnh', 'ban công', 'cho nuôi thú cưng', 'wc khép kín', 'máy giặt'],
            ['nóng lạnh', 'wifi', 'tủ lạnh', 'gác lửng', 'điều hòa'],
            ['điều hòa', 'ban công', 'gác lửng', 'wc khép kín', 'cho nuôi thú cưng', 'wifi', 'khóa thông minh'],
            ['điều hòa', 'nóng lạnh', 'wifi', 'ban công', 'tủ lạnh'],
            ['nóng lạnh', 'wifi', 'tủ lạnh', 'máy giặt', 'điều hòa'],
            ['điều hòa', 'gác lửng', 'wc khép kín', 'ban công'],
            ['điều hòa', 'nóng lạnh', 'cho nuôi thú cưng', 'wifi', 'tủ quần áo'],
            ['nóng lạnh', 'wifi', 'gác lửng', 'điều hòa', 'wc khép kín'],
            ['điều hòa', 'ban công', 'wc khép kín', 'nóng lạnh', 'máy giặt'],
            ['điều hòa', 'nóng lạnh', 'wifi', 'tủ lạnh', 'ban công'],
            ['điều hòa', 'nóng lạnh', 'gác lửng', 'wc khép kín', 'tủ quần áo'],
            ['điều hòa', 'nóng lạnh', 'ban công', 'máy giặt', 'khóa thông minh'],
        ];

        // 15 phòng trải dài từ Tầng 1 đến Tầng 6
        // [room_number, floor, status, hk_status, priority, rental_type, day_price, hr_price, room_type, notes, staff_slot]
        $specsA = [
            ['101', 1, 'empty', 'inspected', 'normal', 'day', 450000, 120000, 'standard', 'Lễ tân đã nghiệm thu: phòng thơm mát, sẵn sàng đón khách.', 1],
            ['102', 1, 'occupied', 'inspected', 'normal', 'hour', 450000, 100000, 'deluxe', 'Khách thuê theo giờ, phòng sạch sẽ tiện nghi.', 2],
            ['201', 2, 'overdue', 'inspected', 'normal', 'month', null, null, 'vip', 'Cư dân đang nợ phí tháng hiện tại.', 1],
            ['202', 2, 'cleaning', 'dirty', 'urgent', 'day', 420000, 110000, 'standard', 'Khách vừa check-out lúc 12:00, cần dọn gấp đón khách mới.', null],
            ['203', 2, 'cleaning', 'cleaning', 'high', 'day', 420000, 110000, 'standard', 'Nhân viên đang thay drap giường, hút bụi và khử khuẩn phòng.', 1],
            ['204', 2, 'empty', 'clean', 'normal', 'day', 450000, 120000, 'deluxe', 'Buồng phòng đã dọn xong, chờ Lễ tân nghiệm thu.', 1],
            ['205', 2, 'empty', 'inspected', 'normal', 'day', 520000, 150000, 'vip', 'Đã nghiệm thu đạt chuẩn đón khách, minibar nguyên tem.', 2],
            ['301', 3, 'maintenance', 'out_of_service', 'low', 'day', 480000, 130000, 'deluxe', 'Bảo trì hệ thống điều hòa và kiểm tra thiết bị.', null],
            ['302', 3, 'occupied', 'inspected', 'normal', 'month', null, null, 'standard', 'Khách đang ở ổn định, phòng sạch sẽ.', 2],
            ['401', 4, 'occupied', 'inspected', 'normal', 'month', null, null, 'studio', 'Khách đang ở ổn định, phòng sạch sẽ.', 1],
            ['402', 4, 'empty', 'dirty', 'normal', 'day', 480000, 120000, 'standard', 'Cần vệ sinh định kỳ và lau cửa kính ban công.', 1],
            ['403', 4, 'empty', 'clean', 'normal', 'day', 500000, 130000, 'vip', 'Đã dọn dẹp vệ sinh xong, chờ Lễ tân kiểm tra.', 1],
            ['501', 5, 'occupied', 'inspected', 'normal', 'month', null, null, 'studio', 'Khách đang ở ổn định, phòng sạch sẽ.', 2],
            ['502', 5, 'occupied', 'cleaning', 'normal', 'day', 550000, 140000, 'deluxe', 'Khách đang ở yêu cầu dọn phòng giữa kỳ.', 2],
            ['601', 6, 'occupied', 'inspected', 'normal', 'month', null, null, 'vip', 'Khách VIP đang ở, phòng sạch sẽ.', 1],
        ];

        $specsB = [
            ['101', 1, 'occupied', 'inspected', 'normal', 'month', null, null, 'standard', 'Khách đang ở ổn định, phòng sạch sẽ.', 1],
            ['102', 1, 'occupied', 'inspected', 'normal', 'month', null, null, 'deluxe', 'Khách đang ở ổn định, phòng sạch sẽ.', 2],
            ['201', 2, 'overdue', 'inspected', 'normal', 'month', null, null, 'vip', 'Cư dân đang nợ phí dịch vụ điện nước.', 2],
            ['202', 2, 'empty', 'dirty', 'high', 'day', 420000, 110000, 'standard', 'Khách vừa trả phòng, cần dọn dẹp khử khuẩn đón khách.', null],
            ['203', 2, 'occupied', 'cleaning', 'normal', 'day', 450000, 120000, 'standard', 'Nhân viên buồng phòng đang dọn dẹp theo yêu cầu.', 2],
            ['204', 2, 'empty', 'clean', 'normal', 'day', 480000, 120000, 'deluxe', 'Đã dọn xong lúc 09:30, chờ kiểm tra nghiệm thu.', 2],
            ['205', 2, 'empty', 'inspected', 'normal', 'day', 520000, 140000, 'vip', 'Đã nghiệm thu đạt chuẩn, sẵn sàng nhận phòng.', 1],
            ['301', 3, 'maintenance', 'out_of_service', 'low', 'day', 480000, 130000, 'deluxe', 'Bảo trì hệ thống khóa thông minh SmartLock.', null],
            ['302', 3, 'occupied', 'inspected', 'normal', 'month', null, null, 'standard', 'Khách đang ở ổn định, phòng sạch sẽ.', 1],
            ['401', 4, 'occupied', 'inspected', 'normal', 'month', null, null, 'studio', 'Khách đang ở ổn định, phòng sạch sẽ.', 2],
            ['402', 4, 'empty', 'dirty', 'normal', 'day', 460000, 120000, 'standard', 'Phòng trống cần vệ sinh định kỳ.', 2],
            ['403', 4, 'empty', 'clean', 'normal', 'day', 490000, 130000, 'vip', 'Đã dọn xong, chờ nghiệm thu đạt chuẩn.', 2],
            ['501', 5, 'occupied', 'inspected', 'normal', 'month', null, null, 'studio', 'Khách đang ở ổn định, phòng sạch sẽ.', 1],
            ['502', 5, 'occupied', 'inspected', 'normal', 'month', null, null, 'deluxe', 'Khách đang ở ổn định, phòng sạch sẽ.', 2],
            ['601', 6, 'occupied', 'inspected', 'normal', 'month', null, null, 'vip', 'Khách đang ở ổn định, phòng sạch sẽ.', 1],
        ];

        $specs = $buildingCode === 'B' ? $specsB : $specsA;

        return collect($specs)->map(function ($spec, $index) use ($basePrice, $amenitiesPool, $buildingTitle) {
            [$roomNumber, $floor, $status, $hkStatus, $priority, $rentalType, $dayPrice, $hrPrice, $roomType, $notes, $staffSlot] = $spec;
            return [
                'room_number' => $roomNumber,
                'floor' => (int) $floor,
                'status' => $status,
                'housekeeping_status' => $hkStatus,
                'cleaning_status' => $hkStatus === 'out_of_service' ? 'out_of_service' : ($hkStatus === 'dirty' ? 'dirty' : ($hkStatus === 'cleaning' ? 'cleaning' : 'clean')),
                'priority' => $priority,
                'rental_type' => $rentalType,
                'price_per_day' => $dayPrice,
                'price_per_hour' => $hrPrice,
                'price_extra_hour' => $hrPrice ? 30000 : null,
                'inspection_notes' => $notes,
                'staff_slot' => $staffSlot,
                'room_type' => $roomType,
                'price' => $basePrice + ($index * 120000),
                'area' => 24 + (($index % 6) * 3),
                'amenities' => $amenitiesPool[$index % count($amenitiesPool)],
                'description' => ($buildingTitle ?: 'Chung cư mini') . ' - Phòng ' . $roomNumber . ' khép kín, tiện nghi, an ninh 24/7.',
            ];
        })->all();
    }

    private function residentNames(): array
    {
        return [
            'Trần Minh Anh',
            'Nguyễn Hoàng Nam',
            'Lê Thu Trang',
            'Phạm Quốc Việt',
            'Đỗ Khánh Linh',
            'Bùi Tiến Đạt',
            'Hoàng Gia Bảo',
            'Vũ Phương Thảo',
            'Đặng Anh Đức',
            'Ngô Bảo Châu',
        ];
    }

    private function printInstructions(): void
    {
        if (isset($this->command)) {
            $this->command->info("\n=======================================================================");
            $this->command->info("   HỆ THỐNG SMARTROOM & RENTRY - DỮ LIỆU ĐÃ TẠO MỚI HOÀN TOÀN");
            $this->command->info("=======================================================================");
            $this->command->info("1. ADMIN HỆ THỐNG (Superadmin):");
            $this->command->info("   - Username: superadmin / admin (password: password / admin123)");
            $this->command->info("   - Vai trò: Toàn quyền quản trị, duyệt hồ sơ KYC chủ trọ.");
            $this->command->info("-----------------------------------------------------------------------");
            $this->command->info("2. 10 CHỦ TRỌ CHUNG CƯ MINI (7 ĐÃ XÁC MINH - 3 CHƯA XÁC MINH):");
            $this->command->info("   [ĐÃ XÁC MINH - 7/10]:");
            $this->command->info("   - Chủ trọ 1 (Cầu Giấy): demo-landlord-1 / admin_hanoi (contact@smartroom-caugiay.vn)");
            $this->command->info("   - Chủ trọ 2 (Thanh Xuân): demo-landlord-2 (demo-thanhxuan@smartroom.local)");
            $this->command->info("   - Chủ trọ 3 (Đống Đa): demo-landlord-3 (demo-dongda@smartroom.local)");
            $this->command->info("   - Chủ trọ 4 (Tây Hồ): demo-landlord-4 (demo-tayho@smartroom.local)");
            $this->command->info("   - Chủ trọ 5 (Hai Bà Trưng): demo-landlord-5 (demo-hbt@smartroom.local)");
            $this->command->info("   - Chủ trọ 6 (Ba Đình): demo-landlord-6 (demo-badinh@smartroom.local)");
            $this->command->info("   - Chủ trọ 7 (Nam Từ Liêm): demo-landlord-7 (demo-mydinh@smartroom.local)");
            $this->command->info("   [CHƯA XÁC MINH - 3/10]:");
            $this->command->info("   - Chủ trọ 8 (Hoàng Mai - Chờ duyệt): demo-landlord-8 (demo-hoangmai@smartroom.local)");
            $this->command->info("   - Chủ trọ 9 (Bắc Từ Liêm - Chờ duyệt): demo-landlord-9 (demo-bactuliem@smartroom.local)");
            $this->command->info("   - Chủ trọ 10 (Hà Đông - Chưa nộp): demo-landlord-10 / unverified-landlord (demo-hadong@smartroom.local)");
            $this->command->info("   * Mật khẩu tất cả tài khoản chủ trọ: password");
            $this->command->info("-----------------------------------------------------------------------");
            $this->command->info("3. CÁC TÒA NHÀ & PHÒNG TRỌ:");
            $this->command->info("   - Đều là các tòa 'Chung cư mini ...' (Tòa A & Tòa B), đầy đủ 12 phòng/tòa");
            $this->command->info("   - Đầy đủ trạng thái: Phòng trống, có khách, quá hạn, bảo trì.");
            $this->command->info("=======================================================================\n");
        }
    }

    private function mediaForRoom(string $buildingName, string $roomNumber): array
    {
        $sets = [
            [
                'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1484154218962-a197022b5858?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1552321554-5fefe8c9ef14?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1556911220-bff31c812dba?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1560448075-bb485b067938?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'https://images.unsplash.com/photo-1554995207-c18c203602cb?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1620626011761-996317b8d101?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1560185127-6ed189bf02f4?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1536376072261-38c75010e6c9?auto=format&fit=crop&w=1200&q=80',
            ],
        ];
        $videos = [
            'https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.mp4',
            'https://www.w3schools.com/html/mov_bbb.mp4',
            'https://media.w3.org/2010/05/sintel/trailer.mp4',
        ];

        $index = abs(crc32($buildingName . $roomNumber)) % count($sets);
        $images = $sets[$index];

        return [
            'image' => $images[0],
            'images' => $images,
            'video' => $videos[$index % count($videos)],
        ];
    }
}

