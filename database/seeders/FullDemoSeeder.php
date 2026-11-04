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
use App\Models\IotDevice;
use App\Models\IotMeterTelemetry;
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
use Illuminate\Support\Str;

class FullDemoSeeder extends Seeder
{
    private array $roles = [];
    private ?int $adminUserId = null;
    private string $passwordHash = '';

    public function run(): void
    {
        @ini_set('memory_limit', '512M');
        @set_time_limit(600);
        DB::disableQueryLog();

        // 0. Băm mật khẩu chung 1 lần duy nhất để tối ưu tốc độ seeding siêu tốc
        $this->passwordHash = Hash::make('123456');

        // 1. Dọn dẹp dữ liệu cũ an toàn
        $this->cleanOldData();

        // 2. Khởi tạo danh sách vai trò
        $this->seedRoles();

        // 3. Khởi tạo tài khoản Quản trị viên (Superadmin / Admin)
        $this->seedAdminUsers();

        // 4. Lấy danh sách 40 chủ trọ chia đều 4 thành phố (Hà Nội, Hồ Chí Minh, Đà Nẵng, Đà Lạt)
        $blueprints = $this->tenantBlueprints();

        $totalTenants = count($blueprints);
        $totalBuildings = 0;
        $totalRooms = 0;

        foreach ($blueprints as $tenantIndex => $blueprint) {
            $tenant = $this->seedTenant($blueprint);
            $landlord = $this->seedLandlord($tenant, $blueprint, $tenantIndex);
            $this->seedManager($tenant, $tenantIndex, $blueprint);
            $this->seedHospitalityStaff($tenant, $tenantIndex, $blueprint);

            $rooms = $this->seedBuildingsAndRooms($tenant, $blueprint);
            $totalBuildings += count($blueprint['buildings']);
            $totalRooms += count($rooms);

            $residents = $this->seedResidents($tenant, $rooms, $tenantIndex);
            $this->seedContracts($tenant, $rooms, $residents, $tenantIndex);
            $this->seedUtilitiesAndBills($tenant, $rooms, $residents);
            $equipment = $this->seedEquipment($tenant, $rooms);
            $this->seedIotDevicesAndTelemetries($tenant, $rooms);
            $this->seedTickets($tenant, $rooms, $residents);
            $this->seedReviewsAndContactRequests($rooms, $tenantIndex);
            $this->seedNotifications($tenant, $rooms, $residents);
            $this->seedActivityLogs($tenant, $landlord, $rooms, $residents, $equipment);

            if (($blueprint['has_hotel'] ?? false) || $tenantIndex < 5) {
                $this->seedHotelBookingsAndLogs($tenant, $rooms, $tenantIndex);
            }
        }

        // 5. Khởi tạo tài khoản khách vãng lai và cư dân dùng chung
        $this->seedGuestUsers();

        // 6. In hướng dẫn tài khoản kiểm thử
        $this->printInstructions($totalTenants, $totalBuildings, $totalRooms);
    }

    private function cleanOldData(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        IotMeterTelemetry::truncate();
        IotDevice::truncate();
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

    private function seedAdminUsers(): void
    {
        $adminUser = User::updateOrCreate(
            ['username' => 'admin1'],
            [
                'tenant_id' => null,
                'role_id' => $this->roles['admin']->id,
                'name' => 'Admin Hệ Thống 1 (Superadmin)',
                'phone' => '0999000001',
                'email' => 'admin1@smartroom.local',
                'password' => $this->passwordHash,
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
                'name' => 'Superadmin Hệ Thống',
                'phone' => '0999999999',
                'email' => 'superadmin@smartroom.local',
                'password' => $this->passwordHash,
                'role' => 'admin',
                'like' => 'Superadmin',
            ]
        );

        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'tenant_id' => null,
                'role_id' => $this->roles['admin']->id,
                'name' => 'Admin Mặc Định',
                'phone' => '0987654321',
                'email' => 'admin@smartroom.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'like' => 'Admin Mặc Định',
            ]
        );
    }

    private function seedTenant(array $blueprint): Tenant
    {
        return Tenant::create([
            'name' => $blueprint['name'],
            'email' => $blueprint['email'],
            'phone' => $blueprint['phone'],
            'bank_name' => $blueprint['bank_name'],
            'bank_account_no' => $blueprint['bank_account_no'],
            'bank_account_name' => $blueprint['bank_account_name'],
            'verification_status' => $blueprint['verification_status'] ?? 'kyc_verified',
            'listing_badge' => $blueprint['listing_badge'] ?? 'kyc_verified',
            'boost_score' => $blueprint['boost_score'] ?? 80,
            'onboarding_step' => ($blueprint['verification_status'] ?? '') === 'kyc_verified' ? 4 : 2,
            'verified_at' => ($blueprint['verification_status'] ?? '') === 'kyc_verified' ? Carbon::now()->subDays(10) : null,
            'kyc_verified_at' => ($blueprint['verification_status'] ?? '') === 'kyc_verified' ? Carbon::now()->subDays(10) : null,
        ]);
    }

    private function seedLandlord(Tenant $tenant, array $blueprint, int $tenantIndex): User
    {
        $idNumber = $tenantIndex + 1;
        $username = 'chutro' . $idNumber;
        $verificationStatus = $blueprint['verification_status'] ?? 'unverified';
        $isVerified = ($verificationStatus === 'kyc_verified');
        $isPending = ($verificationStatus === 'pending');
        $roleSlug = $isVerified ? 'landlord' : 'unverified_landlord';

        $landlord = User::create([
            'username' => $username,
            'tenant_id' => $tenant->id,
            'role_id' => $this->roles[$roleSlug]->id,
            'name' => $blueprint['owner_name'],
            'phone' => $blueprint['phone'],
            'email' => $blueprint['email'],
            'password' => $this->passwordHash,
            'role' => $roleSlug,
            'status' => 'active',
            'like' => $blueprint['owner_name'],
        ]);

        // Tạo bí danh gợi nhớ theo vùng miền (vd: chutro_hn_1, chutro_hcm_1, chutro_dn_1, chutro_dl_1)
        if (!empty($blueprint['alias_username'])) {
            User::create([
                'username' => $blueprint['alias_username'],
                'tenant_id' => $tenant->id,
                'role_id' => $this->roles[$roleSlug]->id,
                'name' => $blueprint['owner_name'] . ' (' . $blueprint['city'] . ')',
                'phone' => '088' . str_pad((string) $idNumber, 7, '0', STR_PAD_LEFT),
                'email' => $blueprint['alias_username'] . '@smartroom.local',
                'password' => $this->passwordHash,
                'role' => $roleSlug,
                'status' => 'active',
                'like' => $blueprint['owner_name'],
            ]);
        }

        $profileStatus = $isVerified ? 'verified' : ($isPending ? 'pending' : 'unverified');
        $demoCccd = '0' . str_pad((string) (10000000000 + $idNumber), 11, '0', STR_PAD_LEFT);
        $demoMst = '0' . str_pad((string) (300000000 + $idNumber), 9, '0', STR_PAD_LEFT);

        LandlordProfile::create([
            'user_id' => $landlord->id,
            'tenant_id' => $tenant->id,
            'full_name' => $landlord->name,
            'phone' => $landlord->phone,
            'property_name' => $tenant->name,
            'property_address' => $blueprint['buildings'][0]['address'] ?? $blueprint['city'],
            'national_id' => $demoCccd,
            'business_license' => $demoMst,
            'status' => $profileStatus,
            'verification_status' => $verificationStatus,
        ]);

        if ($isVerified || $isPending) {
            $reqStatus = $isVerified ? 'approved' : 'pending';
            $verificationRequest = LandlordVerificationRequest::create([
                'tenant_id' => $tenant->id,
                'user_id' => $landlord->id,
                'type' => 'kyc',
                'cccd_number' => $demoCccd,
                'admin_review_consent_given' => true,
                'admin_review_consent_at' => Carbon::now()->subDays(5),
                'admin_review_consent_ip' => '127.0.0.1',
                'status' => $reqStatus,
                'reviewed_by' => $isVerified ? ($this->adminUserId ?? 1) : null,
                'reviewed_at' => $isVerified ? Carbon::now()->subDays(2) : null,
                'reject_reason' => null,
            ]);

            foreach (['cccd_front' => 'Căn cước mặt trước.jpg', 'cccd_back' => 'Căn cước mặt sau.jpg'] as $docType => $filename) {
                LandlordVerificationDocument::create([
                    'verification_request_id' => $verificationRequest->id,
                    'document_type' => $docType,
                    'disk' => 'local',
                    'file_path' => 'kyc/' . $docType . '_' . $idNumber . '.jpg',
                    'original_filename' => $filename,
                    'mime_type' => 'image/jpeg',
                    'size_bytes' => 102400,
                    'sha256_checksum' => hash('sha256', $filename . $idNumber),
                    'status' => $reqStatus,
                ]);
            }
        }

        return $landlord;
    }

    private function seedManager(Tenant $tenant, int $tenantIndex, array $blueprint): void
    {
        $idNumber = $tenantIndex + 1;
        $username = 'quanly' . $idNumber;

        User::create([
            'username' => $username,
            'tenant_id' => $tenant->id,
            'role_id' => $this->roles['manager']->id,
            'name' => 'Quản Lý ' . $blueprint['city'] . ' ' . $idNumber,
            'phone' => '097' . str_pad((string) $idNumber, 7, '0', STR_PAD_LEFT),
            'email' => 'quanly' . $idNumber . '@smartroom.local',
            'password' => $this->passwordHash,
            'role' => 'manager',
            'status' => 'active',
            'like' => 'Quản Lý Cơ Sở ' . $tenant->name,
        ]);
    }

    private function seedHospitalityStaff(Tenant $tenant, int $tenantIndex, array $blueprint): void
    {
        $idNumber = $tenantIndex + 1;

        // Tạo nhân viên Lễ tân cho cơ sở
        User::create([
            'username' => 'letan' . $idNumber,
            'tenant_id' => $tenant->id,
            'role_id' => $this->roles['receptionist']->id,
            'name' => 'Lễ Tân ' . $blueprint['city'] . ' ' . $idNumber,
            'phone' => '096' . str_pad((string) $idNumber, 7, '0', STR_PAD_LEFT),
            'email' => 'letan' . $idNumber . '@smartroom.local',
            'password' => $this->passwordHash,
            'role' => 'receptionist',
            'status' => 'active',
            'like' => 'Lễ Tân ' . $blueprint['city'],
        ]);

        // Tạo nhân viên Buồng phòng cho cơ sở
        User::create([
            'username' => 'buongphong' . $idNumber,
            'tenant_id' => $tenant->id,
            'role_id' => $this->roles['housekeeper']->id,
            'name' => 'Buồng Phòng ' . $blueprint['city'] . ' ' . $idNumber,
            'phone' => '095' . str_pad((string) $idNumber, 7, '0', STR_PAD_LEFT),
            'email' => 'buongphong' . $idNumber . '@smartroom.local',
            'password' => $this->passwordHash,
            'role' => 'housekeeper',
            'status' => 'active',
            'like' => 'Buồng Phòng ' . $blueprint['city'],
        ]);
    }

    private function seedBuildingsAndRooms(Tenant $tenant, array $blueprint): array
    {
        $rooms = [];
        $receptionist = User::where('tenant_id', $tenant->id)->where('role', 'receptionist')->first();
        $housekeeper = User::where('tenant_id', $tenant->id)->where('role', 'housekeeper')->first();

        foreach ($blueprint['buildings'] as $buildingData) {
            $building = Building::create([
                'tenant_id' => $tenant->id,
                'name' => $buildingData['name'],
                'address' => $buildingData['address'],
                'phone' => $tenant->phone,
                'total_floors' => 5,
                'status' => 'active',
                'description' => $buildingData['description'],
                'property_type' => $buildingData['property_type'] ?? 'apartment',
                'checkin_time' => $buildingData['checkin_time'] ?? '14:00:00',
                'checkout_time' => $buildingData['checkout_time'] ?? '12:00:00',
                'image' => $buildingData['image'] ?? 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=1200&q=80',
                'amenities' => ['Thang máy', 'Khóa vân tay 24/7', 'Camera an ninh', 'Nhà để xe', 'Hệ thống PCCC tự động', 'Wifi tốc độ cao'],
            ]);

            // Mỗi tòa tạo tầm 20 phòng trải dài từ Tầng 1 đến Tầng 5 (mỗi tầng 4 phòng)
            $roomList = $this->generate20RoomsForBuilding($buildingData['property_type'] ?? 'apartment', $buildingData['base_price'] ?? 3500000, $buildingData['code'] ?? 'R');

            foreach ($roomList as $roomData) {
                $media = $this->mediaForRoom($buildingData['name'], (string) $roomData['room_number']);

                $assignedStaff = null;
                if (in_array($roomData['housekeeping_status'], ['dirty', 'cleaning'])) {
                    $assignedStaff = $housekeeper?->id;
                }

                $inspectedBy = null;
                $inspectedAt = null;
                if ($roomData['housekeeping_status'] === 'inspected') {
                    $inspectedBy = $receptionist?->id;
                    $inspectedAt = Carbon::now()->subMinutes(rand(20, 180));
                }

                $meterElectric = 'EM-' . $buildingData['code'] . '-' . $roomData['room_number'];
                $meterWater = 'WM-' . $buildingData['code'] . '-' . $roomData['room_number'];

                $room = Room::create([
                    'building_id' => $building->id,
                    'tenant_id' => $tenant->id,
                    'room_number' => $roomData['room_number'],
                    'floor' => $roomData['floor'],
                    'status' => $roomData['status'],
                    'housekeeping_status' => $roomData['housekeeping_status'],
                    'cleaning_status' => $roomData['cleaning_status'],
                    'priority' => $roomData['priority'],
                    'inspection_notes' => $roomData['notes'],
                    'rental_type' => $roomData['rental_type'],
                    'price' => $roomData['price'],
                    'price_per_day' => $roomData['price_per_day'],
                    'price_per_hour' => $roomData['price_per_hour'],
                    'price_extra_hour' => $roomData['price_extra_hour'],
                    'deposit' => $roomData['deposit'],
                    'area' => $roomData['area'],
                    'electric_meter_serial' => $meterElectric,
                    'water_meter_serial' => $meterWater,
                    'room_type' => $roomData['room_type'],
                    'amenities' => $roomData['amenities'],
                    'description' => $buildingData['name'] . ' - Phòng ' . $roomData['room_number'] . ' đầy đủ tiện nghi cao cấp, an ninh tuyệt đối.',
                    'image' => $media['image'],
                    'images' => $media['images'],
                    'video' => $media['video'],
                    'rating_security' => round(mt_rand(45, 50) / 10, 1),
                    'rating_landlord' => round(mt_rand(46, 50) / 10, 1),
                    'rating_pricing' => round(mt_rand(44, 49) / 10, 1),
                    'assigned_staff_id' => $assignedStaff,
                    'inspected_by' => $inspectedBy,
                    'inspected_at' => $inspectedAt,
                    'version' => 1,
                ]);

                $rooms[$buildingData['code'] . '-' . $roomData['room_number']] = $room;
            }
        }

        return $rooms;
    }

    private function generate20RoomsForBuilding(string $propertyType, int $basePrice, string $prefix): array
    {
        $amenitiesPool = [
            ['Máy lạnh', 'Nóng lạnh', 'Wifi', 'WC khép kín', 'Gác lửng', 'Tủ lạnh'],
            ['Máy lạnh', 'Nóng lạnh', 'Ban công', 'WC khép kín', 'Tủ quần áo', 'Máy giặt'],
            ['Máy lạnh', 'Nóng lạnh', 'Bếp từ', 'WC khép kín', 'Wifi', 'Khóa vân tay'],
            ['Máy lạnh', 'Nóng lạnh', 'Ban công', 'Cho nuôi thú cưng', 'WC khép kín', 'Tủ lạnh'],
        ];

        $rooms = [];
        // 5 tầng, mỗi tầng 4 phòng = 20 phòng chuẩn
        for ($floor = 1; $floor <= 5; $floor++) {
            for ($num = 1; $num <= 4; $num++) {
                $roomNumber = (string) ($floor * 100 + $num);
                $index = (($floor - 1) * 4) + $num;

                // Phân bổ trạng thái: 12 occupied (đang thuê), 4 empty (trống), 2 cleaning (buồng phòng), 1 overdue (quá hạn), 1 maintenance (bảo trì)
                if ($index === 4) {
                    $status = 'empty';
                    $hkStatus = 'inspected';
                    $cleanStatus = 'clean';
                } elseif ($index === 8) {
                    $status = 'empty';
                    $hkStatus = 'clean';
                    $cleanStatus = 'clean';
                } elseif ($index === 12) {
                    $status = 'empty';
                    $hkStatus = 'dirty';
                    $cleanStatus = 'dirty';
                } elseif ($index === 16) {
                    $status = 'empty';
                    $hkStatus = 'inspected';
                    $cleanStatus = 'clean';
                } elseif ($index === 5) {
                    $status = 'cleaning';
                    $hkStatus = 'cleaning';
                    $cleanStatus = 'cleaning';
                } elseif ($index === 10) {
                    $status = 'cleaning';
                    $hkStatus = 'dirty';
                    $cleanStatus = 'dirty';
                } elseif ($index === 14) {
                    $status = 'overdue';
                    $hkStatus = 'inspected';
                    $cleanStatus = 'clean';
                } elseif ($index === 18) {
                    $status = 'maintenance';
                    $hkStatus = 'out_of_service';
                    $cleanStatus = 'out_of_service';
                } else {
                    $status = 'occupied';
                    $hkStatus = 'inspected';
                    $cleanStatus = 'clean';
                }

                $roomType = ['standard', 'deluxe', 'vip', 'studio'][($num - 1) % 4];
                $rentalType = $propertyType === 'hotel' ? ($num % 2 === 0 ? 'hour' : 'day') : 'month';
                $pricePerMonth = $basePrice + (($floor - 1) * 100000) + (($num - 1) * 50000);
                $pricePerDay = (int) round($pricePerMonth / 8);
                $pricePerHour = (int) round($pricePerDay / 4);

                $rooms[] = [
                    'room_number' => $roomNumber,
                    'floor' => $floor,
                    'status' => $status,
                    'housekeeping_status' => $hkStatus,
                    'cleaning_status' => $cleanStatus,
                    'priority' => ($hkStatus === 'dirty' && $status === 'cleaning') ? 'high' : 'normal',
                    'notes' => $hkStatus === 'inspected' ? 'Lễ tân đã nghiệm thu đạt chuẩn đón khách.' : null,
                    'room_type' => $roomType,
                    'rental_type' => $rentalType,
                    'price' => $pricePerMonth,
                    'price_per_day' => $propertyType === 'hotel' ? $pricePerDay : null,
                    'price_per_hour' => $propertyType === 'hotel' ? $pricePerHour : null,
                    'price_extra_hour' => $propertyType === 'hotel' ? 30000 : null,
                    'deposit' => $rentalType === 'month' ? $pricePerMonth : (int) round($pricePerDay / 2),
                    'area' => 22 + (($num % 4) * 4) + ($floor * 2),
                    'amenities' => $amenitiesPool[($floor + $num) % count($amenitiesPool)],
                ];
            }
        }

        return $rooms;
    }

    private function seedResidents(Tenant $tenant, array $rooms, int $tenantIndex): array
    {
        $primaryResidents = [];
        $occupiedRooms = collect($rooms)
            ->filter(fn (Room $room) => in_array($room->status, ['occupied', 'overdue'], true))
            ->values();

        foreach ($occupiedRooms as $index => $room) {
            $uniqueKey = (($tenantIndex + 1) * 100) + $index + 1;
            $name1 = $this->residentNames()[$index % count($this->residentNames())];
            $email1 = 'cudan_' . $uniqueKey . '@smartroom.local';
            $phone1 = '094' . str_pad((string) $uniqueKey, 7, '0', STR_PAD_LEFT);
            $cccd1 = '0' . str_pad((string) (79000000000 + $uniqueKey), 11, '0', STR_PAD_LEFT);

            $user1 = User::create([
                'username' => 'cudan_' . $uniqueKey,
                'tenant_id' => $tenant->id,
                'role_id' => $this->roles['resident']->id,
                'name' => $name1 . ' (P.' . $room->room_number . ')',
                'phone' => $phone1,
                'email' => $email1,
                'password' => $this->passwordHash,
                'role' => 'user',
                'status' => 'active',
                'like' => $name1,
            ]);

            $resident1 = Resident::create([
                'tenant_id' => $tenant->id,
                'room_id' => $room->id,
                'user_id' => $user1->id,
                'name' => $name1,
                'dob' => Carbon::create(1995 + ($index % 7), ($index % 12) + 1, 15)->toDateString(),
                'phone' => $phone1,
                'email' => $email1,
                'cccd' => $cccd1,
                'hometown' => ['Hà Nội', 'TP. Hồ Chí Minh', 'Đà Nẵng', 'Lâm Đồng', 'Huế', 'Cần Thơ', 'Hải Phòng'][$index % 7],
                'start_date' => Carbon::today()->subMonths(10 - ($index % 6))->toDateString(),
                'status' => 'active',
                'temporary_residence_status' => ['registered', 'none', 'absent'][$index % 3],
                'version' => 1,
            ]);

            if ($index % 4 === 0) {
                ResidentRelative::create([
                    'resident_id' => $resident1->id,
                    'name' => 'Người thân P.' . $room->room_number,
                    'dob' => Carbon::create(1998, 5, 20)->toDateString(),
                    'cccd' => 'REL' . str_pad((string) $uniqueKey, 9, '0', STR_PAD_LEFT),
                    'phone' => '0888' . str_pad((string) $uniqueKey, 6, '0', STR_PAD_LEFT),
                    'hometown' => 'Việt Nam',
                    'relationship' => 'Anh/Chị/Em',
                    'temporary_residence_status' => 'registered',
                    'start_date' => Carbon::today()->subDays(30)->toDateString(),
                    'end_date' => Carbon::today()->addMonths(6)->toDateString(),
                    'version' => 1,
                ]);
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
            $startDate = $resident->start_date;
            $endDate = $endingSoon
                ? Carbon::today()->addDays(20)->toDateString()
                : Carbon::parse($resident->start_date)->addYear()->toDateString();

            Contract::create([
                'tenant_id' => $tenant->id,
                'room_id' => $room->id,
                'resident_id' => $resident->id,
                'contract_code' => 'HD-' . ($tenantIndex + 1) . '-' . $room->room_number . '-' . $resident->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'deposit' => $room->price,
                'status' => 'active',
                'terms' => 'Hợp đồng thuê phòng ' . $room->room_number . ' tại ' . ($tenant->name ?? 'Tòa nhà') . '. Thanh toán định kỳ hàng tháng từ ngày 01 đến ngày 05.',
                'signature' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=',
                'lessor_signature' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=',
            ]);
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
                $electricUsage = 85 + (($room->id + $monthIndex) % 70);
                $waterUsage = 5 + (($room->id + $monthIndex) % 7);
                $oldElectric = 500 + ($room->id * 5) + ($monthIndex * 120);
                $oldWater = 80 + ($room->id % 15) + ($monthIndex * 8);
                $electricPrice = 3500;
                $waterPrice = 15000;
                $serviceCost = 150000;
                $total = $room->price + ($electricUsage * $electricPrice) + ($waterUsage * $waterPrice) + $serviceCost;
                $isCurrent = ($month === Carbon::today()->format('Y-m'));
                $billStatus = $isCurrent ? ($room->status === 'overdue' ? 'overdue' : 'pending') : 'paid';
                $paymentDate = $billStatus === 'paid' ? Carbon::parse($month . '-05')->addDays($room->id % 4) : null;

                $log = ElectricWaterLog::create([
                    'tenant_id' => $tenant->id,
                    'room_id' => $room->id,
                    'billing_month' => $month,
                    'old_electricity' => $oldElectric,
                    'new_electricity' => $oldElectric + $electricUsage,
                    'old_water' => $oldWater,
                    'new_water' => $oldWater + $waterUsage,
                    'electricity_price' => $electricPrice,
                    'water_price' => $waterPrice,
                ]);

                Bill::create([
                    'tenant_id' => $tenant->id,
                    'room_id' => $room->id,
                    'billing_month' => $month,
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
                ]);

                UtilityRecord::create([
                    'tenant_id' => $tenant->id,
                    'room_id' => $room->id,
                    'billing_month' => $month,
                    'old_electricity' => $oldElectric,
                    'new_electricity' => $oldElectric + $electricUsage,
                    'old_water' => $oldWater,
                    'new_water' => $oldWater + $waterUsage,
                    'electricity_price' => $electricPrice,
                    'water_price' => $waterPrice,
                    'status' => $billStatus === 'pending' ? 'sent' : $billStatus,
                    'payment_date' => $paymentDate,
                    'payment_method' => $paymentDate ? ['vietqr', 'bank_transfer', 'cash'][$room->id % 3] : null,
                ]);
            }
        }
    }

    private function seedEquipment(Tenant $tenant, array $rooms): array
    {
        $equipmentItems = [
            ['code' => 'AC', 'name' => 'Điều hòa Inverter 9000 BTU', 'unit' => 'cái', 'quantity' => 50],
            ['code' => 'WM', 'name' => 'Máy giặt cửa trước 8kg', 'unit' => 'cái', 'quantity' => 25],
            ['code' => 'FR', 'name' => 'Tủ lạnh 2 cánh 120L', 'unit' => 'cái', 'quantity' => 40],
            ['code' => 'BED', 'name' => 'Giường nệm cao su 1m6', 'unit' => 'cái', 'quantity' => 50],
            ['code' => 'LOCK', 'name' => 'Khóa vân tay thông minh SmartLock', 'unit' => 'cái', 'quantity' => 50],
            ['code' => 'WIFI', 'name' => 'Router Wifi băng tần kép 5G', 'unit' => 'cái', 'quantity' => 30],
        ];

        $equipment = [];
        foreach ($equipmentItems as $item) {
            $equipment[$item['code']] = Equipment::create([
                'tenant_id' => $tenant->id,
                'code' => $item['code'],
                'name' => $item['name'],
                'unit' => $item['unit'],
                'total_quantity' => $item['quantity'],
                'allocated_quantity' => 0,
                'description' => 'Trang thiết bị chuẩn cho phòng trọ / căn hộ / khách sạn.',
                'version' => 1,
            ]);
        }

        foreach (array_values($rooms) as $index => $room) {
            if ($room->status === 'empty') {
                continue;
            }

            foreach (['AC' => 1, 'BED' => 1, 'LOCK' => 1, 'WIFI' => 1] as $code => $qty) {
                RoomEquipment::create([
                    'tenant_id' => $tenant->id,
                    'room_id' => $room->id,
                    'equipment_id' => $equipment[$code]->id,
                    'quantity' => $qty,
                    'last_allocated_at' => Carbon::today()->subDays($room->id % 15),
                ]);
            }
            if ($index % 2 === 0) {
                RoomEquipment::create([
                    'tenant_id' => $tenant->id,
                    'room_id' => $room->id,
                    'equipment_id' => $equipment['FR']->id,
                    'quantity' => 1,
                    'last_allocated_at' => Carbon::today()->subDays($room->id % 10),
                ]);
            }
            if ($index % 3 === 0) {
                RoomEquipment::create([
                    'tenant_id' => $tenant->id,
                    'room_id' => $room->id,
                    'equipment_id' => $equipment['WM']->id,
                    'quantity' => 1,
                    'last_allocated_at' => Carbon::today()->subDays($room->id % 12),
                ]);
            }
        }

        foreach ($equipment as $item) {
            $item->update([
                'allocated_quantity' => RoomEquipment::where('equipment_id', $item->id)->sum('quantity'),
            ]);
        }

        return $equipment;
    }

    private function seedIotDevicesAndTelemetries(Tenant $tenant, array $rooms): void
    {
        $protocols = ['esp32_wifi', 'lorawan', 'modbus_rs485', 'zigbee', 'mqtt'];

        foreach ($rooms as $room) {
            $protoElectric = $protocols[($room->id % count($protocols))];
            $protoWater = $protocols[(($room->id + 2) % count($protocols))];
            $isOverdue = ($room->status === 'overdue');
            $deviceStatus = $isOverdue ? 'warning' : (($room->id % 8 === 0) ? 'offline' : 'online');

            $electricReading = 580 + ($room->id * 4.5);
            $waterReading = 85 + ($room->id * 0.8);

            // 1. Công tơ điện thông minh (Electricity Meter)
            $electricDevice = IotDevice::create([
                'tenant_id' => $tenant->id,
                'room_id' => $room->id,
                'device_code' => $room->electric_meter_serial ?? ('EM-' . $room->id),
                'meter_serial' => $room->electric_meter_serial ?? ('EM-' . $room->id),
                'meter_type' => 'electricity',
                'protocol' => $protoElectric,
                'api_key' => Str::random(32),
                'status' => $deviceStatus,
                'last_reading' => $electricReading,
                'last_seen_at' => $deviceStatus === 'offline' ? Carbon::now()->subHours(6) : Carbon::now()->subMinutes(rand(1, 5)),
                'config' => [
                    'max_current_ampere' => 25.0,
                    'max_power_watt' => 5500.0,
                    'overload_alert_enabled' => true,
                ],
                'firmware_version' => 'v2.4.1-esp32',
            ]);

            // 2. Đồng hồ nước thông minh (Water Meter)
            $waterDevice = IotDevice::create([
                'tenant_id' => $tenant->id,
                'room_id' => $room->id,
                'device_code' => $room->water_meter_serial ?? ('WM-' . $room->id),
                'meter_serial' => $room->water_meter_serial ?? ('WM-' . $room->id),
                'meter_type' => 'water',
                'protocol' => $protoWater,
                'api_key' => Str::random(32),
                'status' => $deviceStatus,
                'last_reading' => $waterReading,
                'last_seen_at' => $deviceStatus === 'offline' ? Carbon::now()->subHours(8) : Carbon::now()->subMinutes(rand(2, 6)),
                'config' => [
                    'max_flow_rate_lpm' => 35.0,
                    'leak_alert_enabled' => true,
                ],
                'firmware_version' => 'v1.8.0-lora',
            ]);

            // 3. Tạo dữ liệu viễn trắc đo kiểm (Telemetry) gần nhất cho cả điện và nước
            IotMeterTelemetry::create([
                'iot_device_id' => $electricDevice->id,
                'room_id' => $room->id,
                'meter_type' => 'electricity',
                'meter_serial' => $electricDevice->meter_serial,
                'reading' => $electricReading,
                'voltage' => 220.0 + (rand(-40, 40) / 10),
                'current' => $room->status === 'occupied' ? (2.1 + (rand(1, 45) / 10)) : 0.05,
                'power' => $room->status === 'occupied' ? (460 + rand(10, 800)) : 11,
                'flow_rate' => null,
                'signal_quality' => -60 - rand(1, 25),
                'battery_level' => 100,
                'recorded_at' => Carbon::now()->subMinutes(rand(2, 10)),
            ]);

            IotMeterTelemetry::create([
                'iot_device_id' => $waterDevice->id,
                'room_id' => $room->id,
                'meter_type' => 'water',
                'meter_serial' => $waterDevice->meter_serial,
                'reading' => $waterReading,
                'voltage' => null,
                'current' => null,
                'power' => null,
                'flow_rate' => $room->status === 'occupied' ? (0.025 + (rand(1, 15) / 100)) : 0.0,
                'signal_quality' => -68 - rand(1, 20),
                'battery_level' => 96,
                'recorded_at' => Carbon::now()->subMinutes(rand(3, 15)),
            ]);
        }
    }

    private function seedTickets(Tenant $tenant, array $rooms, array $residents): void
    {
        foreach (array_values($rooms) as $index => $room) {
            if (!isset($residents[$room->id]) || $index % 4 !== 0) {
                continue;
            }

            Ticket::create([
                'tenant_id' => $tenant->id,
                'room_id' => $room->id,
                'resident_id' => $residents[$room->id]->id,
                'title' => 'Báo hỏng phòng ' . $room->room_number,
                'description' => ['Điều hòa không đủ mát giữa trưa.', 'Vòi sen nhà tắm bị rò rỉ nhẹ.', 'Khóa thông minh cần thay pin.'][$index % 3],
                'category' => ['điện', 'nước', 'nội thất'][$index % 3],
                'status' => ['pending', 'processing', 'resolved'][$index % 3],
                'assigned_to' => $index % 3 === 0 ? null : 'Đội kỹ thuật vận hành',
            ]);
        }
    }

    private function seedReviewsAndContactRequests(array $rooms, int $tenantIndex): void
    {
        $reviewComments = [
            'Phòng sạch sẽ, ban công đón nắng thoáng đãng, chủ nhà hỗ trợ cực kỳ nhiệt tình!',
            'An ninh tòa nhà rất tốt, có khóa vân tay ra vào tự do, wifi mạnh học tập và làm việc mượt mà.',
            'Vị trí đắc địa gần trung tâm và các tuyến giao thông chính, giá phòng hợp lý.',
            'Đồng hồ điện nước điện tử thông minh đo rất minh bạch rõ ràng trên ứng dụng.',
            'Nội thất mới đẹp, máy lạnh Inverter chạy êm ru, rất đáng tiền!',
        ];

        foreach (array_values($rooms) as $index => $room) {
            if ($index < 6) {
                Review::create([
                    'room_id' => $room->id,
                    'author_name' => 'Khách thuê ' . ($tenantIndex + 1) . '-' . ($index + 1),
                    'rating' => 4 + ($index % 2),
                    'comment' => $reviewComments[$index % count($reviewComments)],
                ]);
            }

            if ($room->status === 'empty' && $index % 2 === 0) {
                ContactRequest::create([
                    'room_id' => $room->id,
                    'name' => 'Khách hẹn xem phòng ' . $room->room_number,
                    'phone' => '093' . str_pad((string) ($tenantIndex * 100 + $index), 7, '0', STR_PAD_LEFT),
                    'email' => 'khachxemphong_' . $room->id . '@gmail.com',
                    'message' => 'Chào bạn, tôi muốn đặt lịch xem phòng trực tiếp vào cuối tuần này.',
                    'status' => $index % 4 === 0 ? 'pending' : 'contacted',
                    'appointment_time' => Carbon::now()->addDays(2)->setHour(14)->setMinute(0),
                ]);
            }
        }
    }

    private function seedNotifications(Tenant $tenant, array $rooms, array $residents): void
    {
        foreach (array_values($residents) as $index => $resident) {
            $room = collect($rooms)->firstWhere('id', $resident->room_id);
            NotificationLog::create([
                'tenant_id' => $tenant->id,
                'type' => $index % 2 === 0 ? 'payment_reminder' : 'contract_notice',
                'channel' => ['zalo', 'email', 'sms'][$index % 3],
                'recipient_name' => $resident->name,
                'recipient_contact' => $resident->phone,
                'subject' => 'Thông báo hóa đơn phòng ' . ($room->room_number ?? 'N/A'),
                'message' => 'Hệ thống SmartRoom xin thông báo hóa đơn tháng của bạn đã sẵn sàng thanh toán.',
                'status' => 'sent',
                'target_type' => Resident::class,
                'target_id' => $resident->id,
                'meta' => ['seeded' => true, 'room_number' => $room->room_number ?? null],
                'sent_at' => Carbon::now()->subDays($index % 5),
            ]);
        }
    }

    private function seedActivityLogs(Tenant $tenant, User $landlord, array $rooms, array $residents, array $equipment): void
    {
        $subjects = [
            ['create', 'rooms', 'Khởi tạo phòng mới', reset($rooms)],
            ['create', 'residents', 'Thêm cư dân vào phòng', reset($residents) ?: null],
            ['create', 'utilities', 'Chốt số điện nước tự động qua IoT', UtilityRecord::where('tenant_id', $tenant->id)->latest()->first()],
            ['payment', 'payments', 'Ghi nhận thanh toán hóa đơn VietQR', UtilityRecord::where('tenant_id', $tenant->id)->where('status', 'paid')->latest()->first()],
            ['allocate', 'equipment', 'Cấp phát thiết bị cho phòng', reset($equipment) ?: null],
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
                'url' => '/seed-full',
                'user_agent' => 'SmartRoomFullSeeder',
                'before_values' => null,
                'after_values' => $subject ? ['id' => $subject->getKey()] : null,
                'metadata' => ['seeded' => true, 'index' => $index],
                'created_at' => Carbon::now()->subHours(24 - $index),
                'updated_at' => Carbon::now()->subHours(24 - $index),
            ]);
        }
    }

    private function seedHotelBookingsAndLogs(Tenant $tenant, array $rooms, int $tenantIndex): void
    {
        $hotelRooms = collect($rooms)->filter(fn ($r) => in_array($r->rental_type, ['day', 'hour']))->values();
        if ($hotelRooms->isEmpty()) {
            $hotelRooms = collect($rooms)->values();
        }

        $room1 = $hotelRooms->get(0);
        $room2 = $hotelRooms->get(1);

        if ($room1) {
            $booking1 = HotelBooking::create([
                'tenant_id' => $tenant->id,
                'room_id' => $room1->id,
                'booking_code' => 'HB-' . ($tenantIndex + 1) . '-101',
                'guest_name' => 'Nguyễn Minh Tuấn (Khách Đặt Ngày)',
                'guest_phone' => '0933888777',
                'guest_cccd' => '001098800111',
                'rental_type' => 'day',
                'check_in_at' => Carbon::now()->subHours(18),
                'expected_check_out_at' => Carbon::now()->addHours(6),
                'unit_rate' => $room1->price_per_day ?? 450000,
                'deposit_amount' => 200000,
                'status' => 'checked_in',
                'payment_status' => 'unpaid',
                'note' => 'Khách du lịch đặt phòng view đẹp.',
            ]);

            HotelFolioItem::create([
                'booking_id' => $booking1->id,
                'item_name' => 'Nước khoáng Lavie 500ml',
                'item_type' => 'minibar',
                'quantity' => 2,
                'unit_price' => 15000,
                'subtotal' => 30000,
            ]);
            HotelFolioItem::create([
                'booking_id' => $booking1->id,
                'item_name' => 'Bia Heineken lon 330ml',
                'item_type' => 'minibar',
                'quantity' => 2,
                'unit_price' => 30000,
                'subtotal' => 60000,
            ]);
        }

        if ($room2) {
            $booking2 = HotelBooking::create([
                'tenant_id' => $tenant->id,
                'room_id' => $room2->id,
                'booking_code' => 'HB-' . ($tenantIndex + 1) . '-102',
                'guest_name' => 'Lê Thanh Mai (Khách Thuê Giờ)',
                'guest_phone' => '0933999888',
                'guest_cccd' => '001098800222',
                'rental_type' => 'hour',
                'check_in_at' => Carbon::now()->subMinutes(90),
                'expected_check_out_at' => Carbon::now()->addMinutes(30),
                'unit_rate' => $room2->price_per_hour ?? 120000,
                'deposit_amount' => 50000,
                'status' => 'checked_in',
                'payment_status' => 'unpaid',
                'note' => 'Khách nghỉ ngơi theo giờ.',
            ]);

            HotelFolioItem::create([
                'booking_id' => $booking2->id,
                'item_name' => 'Snack khoai tây Ostar',
                'item_type' => 'minibar',
                'quantity' => 1,
                'unit_price' => 25000,
                'subtotal' => 25000,
            ]);
        }
    }

    private function seedGuestUsers(): void
    {
        // 1. Tạo tài khoản Khách thuê demo chung (tenant / password)
        User::updateOrCreate(
            ['username' => 'tenant'],
            [
                'name' => 'Khách thuê demo chung',
                'email' => 'tenant@smartroom.local',
                'phone' => '0940000000',
                'password' => Hash::make('password'),
                'role' => 'user',
                'role_id' => $this->roles['resident']->id,
                'tenant_id' => 1,
                'status' => 'active',
                'like' => 'Khách thuê demo chung',
            ]
        );

        // 2. Tạo các tài khoản khách theo 4 thành phố
        $cityGuests = [
            ['username' => 'khachhanoi', 'name' => 'Khách Tìm Phòng Hà Nội', 'email' => 'khach.hn@smartroom.local', 'phone' => '0931000001'],
            ['username' => 'khachhcm', 'name' => 'Khách Tìm Phòng TP.HCM', 'email' => 'khach.hcm@smartroom.local', 'phone' => '0931000002'],
            ['username' => 'khachdanang', 'name' => 'Khách Tìm Phòng Đà Nẵng', 'email' => 'khach.dn@smartroom.local', 'phone' => '0931000003'],
            ['username' => 'khachdalat', 'name' => 'Khách Tìm Phòng Đà Lạt', 'email' => 'khach.dl@smartroom.local', 'phone' => '0931000004'],
        ];

        foreach ($cityGuests as $cg) {
            User::updateOrCreate(
                ['username' => $cg['username']],
                [
                    'name' => $cg['name'],
                    'email' => $cg['email'],
                    'phone' => $cg['phone'],
                    'password' => $this->passwordHash,
                    'role' => 'guest',
                    'role_id' => $this->roles['guest']->id,
                    'tenant_id' => null,
                    'status' => 'active',
                    'like' => $cg['name'],
                ]
            );
        }

        // 3. Tạo thêm 10 tài khoản khách vãng lai thông thường (khach1 .. khach10)
        for ($g = 1; $g <= 10; $g++) {
            User::updateOrCreate(
                ['username' => 'khach' . $g],
                [
                    'name' => 'Khách Vãng Lai ' . $g,
                    'email' => 'khach' . $g . '@smartroom.local',
                    'phone' => '0938' . str_pad((string) $g, 6, '0', STR_PAD_LEFT),
                    'password' => $this->passwordHash,
                    'role' => 'guest',
                    'role_id' => $this->roles['guest']->id,
                    'tenant_id' => null,
                    'status' => 'active',
                    'like' => 'Khách Vãng Lai ' . $g,
                ]
            );
        }
    }

    private function vietQrUrl(Tenant $tenant, Room $room, string $month, int $amount): string
    {
        $info = 'Thanh toan phong ' . $room->room_number . ' T' . substr($month, 5, 2);

        return 'https://img.vietqr.io/image/' . ($tenant->bank_name ?? 'MB') . '-' . ($tenant->bank_account_no ?? '999988880001')
            . '-compact.png?amount=' . $amount
            . '&addInfo=' . rawurlencode($info)
            . '&accountName=' . rawurlencode($tenant->bank_account_name ?? 'SMARTROOM');
    }

    private function residentNames(): array
    {
        return [
            'Trần Minh Anh', 'Nguyễn Hoàng Nam', 'Lê Thu Trang', 'Phạm Quốc Việt',
            'Đỗ Khánh Linh', 'Bùi Tiến Đạt', 'Hoàng Gia Bảo', 'Vũ Phương Thảo',
            'Đặng Anh Đức', 'Ngô Bảo Châu', 'Lâm Hoài An', 'Dương Thúy Nga',
            'Trương Gia Huy', 'Hồ Diệu My', 'Lý Tuấn Kiệt', 'Mai Phương Thúy',
        ];
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

    private function printInstructions(int $tenants, int $buildings, int $rooms): void
    {
        if (isset($this->command)) {
            $this->command->info("\n=======================================================================");
            $this->command->info("   HỆ THỐNG SMARTROOM & RENTRY - SEEDER 4 THÀNH PHỐ THÀNH CÔNG");
            $this->command->info("=======================================================================");
            $this->command->info("TỔNG QUAN:");
            $this->command->info("- 4 Thành phố lớn: Hà Nội (10), TP. Hồ Chí Minh (10), Đà Nẵng (10), Đà Lạt (10)");
            $this->command->info("- Tổng số chủ trọ (Tenants): {$tenants} chủ trọ");
            $this->command->info("- Tổng số tòa nhà (Buildings): {$buildings} tòa (Trọ, Chung cư mini, Khách sạn)");
            $this->command->info("- Tổng số phòng (Rooms): {$rooms} phòng (Đúng chuẩn ~20 phòng/tòa)");
            $this->command->info("- Đã tích hợp đầy đủ: Thiết bị phòng, Đồng hồ điện nước IoT thông minh, viễn trắc!");
            $this->command->info("-----------------------------------------------------------------------");
            $this->command->info("TÀI KHOẢN ĐĂNG NHẬP (Mật khẩu: 123456):");
            $this->command->info("1. ADMIN: superadmin / admin1 / admin (admin123)");
            $this->command->info("2. CHỦ TRỌ (40 tài khoản): chutro1 .. chutro40");
            $this->command->info("   - Hoặc alias gợi nhớ: chutro_hn_1..chutro_hn_10, chutro_hcm_1..10, chutro_dn_1..10, chutro_dl_1..10");
            $this->command->info("3. QUẢN LÝ (40 tài khoản): quanly1 .. quanly40");
            $this->command->info("4. LỄ TÂN (40 tài khoản): letan1 .. letan40");
            $this->command->info("5. BUỒNG PHÒNG (40 tài khoản): buongphong1 .. buongphong40");
            $this->command->info("6. KHÁCH TÌM PHÒNG: khachhanoi, khachhcm, khachdanang, khachdalat, khach1..khach10");
            $this->command->info("7. KHÁCH THUÊ DEMO: tenant (password: password)");
            $this->command->info("=======================================================================\n");
        }
    }

    private function tenantBlueprints(): array
    {
        return [
            // =========================================================================
            // I. HÀ NỘI (10 CHỦ TRỌ - CHIA ĐỀU CÁC QUẬN CẦU GIẤY, THANH XUÂN, ĐỐNG ĐA,
            // TÂY HỒ, HAI BÀ TRƯNG, BA ĐÌNH, NAM TỪ LIÊM, HOÀNG MAI, BẮC TỪ LIÊM, HÀ ĐÔNG)
            // =========================================================================
            [
                'city' => 'Hà Nội',
                'alias_username' => 'chutro_hn_1',
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
                'has_hotel' => true,
                'buildings' => [
                    [
                        'code' => 'HN01A',
                        'name' => 'Chung cư mini SmartRoom Cầu Giấy - Tòa A',
                        'address' => 'Số 12 Ngõ 105 Xuân Thủy, Phường Dịch Vọng Hậu, Quận Cầu Giấy, Hà Nội',
                        'description' => 'Gần ĐH Quốc Gia, ĐH Sư Phạm, ga Metro Cầu Giấy. Thang máy, bảo vệ 24/7, phòng mới đẹp.',
                        'property_type' => 'hotel',
                        'base_price' => 3800000,
                    ],
                    [
                        'code' => 'HN01B',
                        'name' => 'Chung cư mini SmartRoom Cầu Giấy - Tòa B',
                        'address' => 'Số 36 Ngõ 86 Chùa Hà, Phường Quan Hoa, Quận Cầu Giấy, Hà Nội',
                        'description' => 'Khu dân trí cao gần công viên Nghĩa Đô, tiện nghi khép kín, ban công thoáng đãng.',
                        'property_type' => 'apartment',
                        'base_price' => 4000000,
                    ],
                ],
            ],
            [
                'city' => 'Hà Nội',
                'alias_username' => 'chutro_hn_2',
                'name' => 'Chuỗi Trọ & Chung cư mini Rentry Home Thanh Xuân',
                'email' => 'chutro.thanhxuan@smartroom.local',
                'phone' => '0988000002',
                'owner_name' => 'Lê Quản Lý Thanh Xuân',
                'bank_name' => 'VCB',
                'bank_account_no' => '999988880002',
                'bank_account_name' => 'LE QUAN LY THANH XUAN',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 92,
                'buildings' => [
                    [
                        'code' => 'HN02A',
                        'name' => 'Khu Trọ Cao Cấp Rentry Vũ Tông Phan',
                        'address' => 'Số 85 Vũ Tông Phan, Phường Khương Trung, Quận Thanh Xuân, Hà Nội',
                        'description' => 'Gần Ngã Tư Sở và Royal City, giờ giấc tự do, có gác lửng và vệ sinh riêng.',
                        'property_type' => 'boarding',
                        'base_price' => 3200000,
                    ],
                    [
                        'code' => 'HN02B',
                        'name' => 'Chung cư mini Rentry Khương Đình',
                        'address' => 'Số 120 Khương Đình, Phường Hạ Đình, Quận Thanh Xuân, Hà Nội',
                        'description' => 'Căn hộ dịch vụ cao cấp, full combo máy lạnh, máy giặt riêng, khóa thẻ từ.',
                        'property_type' => 'apartment',
                        'base_price' => 4200000,
                    ],
                ],
            ],
            [
                'city' => 'Hà Nội',
                'alias_username' => 'chutro_hn_3',
                'name' => 'Căn hộ Dịch vụ Sen Vàng Đống Đa',
                'email' => 'chutro.dongda@smartroom.local',
                'phone' => '0988000003',
                'owner_name' => 'Phạm Văn Đống Đa',
                'bank_name' => 'MB',
                'bank_account_no' => '999988880003',
                'bank_account_name' => 'PHAM VAN DONG DA',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 90,
                'buildings' => [
                    [
                        'code' => 'HN03A',
                        'name' => 'Chung cư mini Sen Vàng Xã Đàn',
                        'address' => 'Số 18 Ngõ 198 Xã Đàn, Phường Phương Liên, Quận Đống Đa, Hà Nội',
                        'description' => 'Trung tâm Đống Đa, giao thông thuận tiện đi Kim Liên, Ô Chợ Dừa, phòng cực yên tĩnh.',
                        'property_type' => 'apartment',
                        'base_price' => 4500000,
                    ],
                    [
                        'code' => 'HN03B',
                        'name' => 'Ký túc xá & Trọ Sinh viên Chùa Bộc',
                        'address' => 'Số 45 Chùa Bộc, Phường Quang Trung, Quận Đống Đa, Hà Nội',
                        'description' => 'Sát vách ĐH Thủy Lợi, Học viện Ngân Hàng, Đại học Y Hà Nội, giá sinh viên.',
                        'property_type' => 'boarding',
                        'base_price' => 2800000,
                    ],
                ],
            ],
            [
                'city' => 'Hà Nội',
                'alias_username' => 'chutro_hn_4',
                'name' => 'Hồ Tây Panorama Hotel & Luxury Suites',
                'email' => 'chutro.tayho@smartroom.local',
                'phone' => '0988000004',
                'owner_name' => 'Hoàng Văn Tây Hồ',
                'bank_name' => 'Agribank',
                'bank_account_no' => '999988880004',
                'bank_account_name' => 'HOANG VAN TAY HO',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 96,
                'has_hotel' => true,
                'buildings' => [
                    [
                        'code' => 'HN04A',
                        'name' => 'Panorama Boutique Hotel Tây Hồ',
                        'address' => 'Số 210 Lạc Long Quân, Phường Bưởi, Quận Tây Hồ, Hà Nội',
                        'description' => 'Khách sạn boutique chuẩn 3 sao ngắm toàn cảnh Hồ Tây lộng gió, đón khách theo ngày và giờ.',
                        'property_type' => 'hotel',
                        'base_price' => 5500000,
                    ],
                    [
                        'code' => 'HN04B',
                        'name' => 'Hồ Tây Studio View Lake',
                        'address' => 'Số 99 Trích Sài, Phường Thụy Khuê, Quận Tây Hồ, Hà Nội',
                        'description' => 'Căn hộ Studio ven hồ, ban công ngắm hoàng hôn siêu đẹp, không khí trong lành.',
                        'property_type' => 'apartment',
                        'base_price' => 6000000,
                    ],
                ],
            ],
            [
                'city' => 'Hà Nội',
                'alias_username' => 'chutro_hn_5',
                'name' => 'Hệ thống Phòng trọ & Studio Times Light Hai Bà Trưng',
                'email' => 'chutro.hbt@smartroom.local',
                'phone' => '0988000005',
                'owner_name' => 'Vũ Thị Hai Bà Trưng',
                'bank_name' => 'VCB',
                'bank_account_no' => '999988880005',
                'bank_account_name' => 'VU THI HAI BA TRUNG',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 88,
                'buildings' => [
                    [
                        'code' => 'HN05A',
                        'name' => 'Chung cư mini Times Light Bạch Mai',
                        'address' => 'Số 250 Bạch Mai, Phường Cầu Dền, Quận Hai Bà Trưng, Hà Nội',
                        'description' => 'Gần Bách - Kinh - Xây, chợ Mơ, phòng rộng rãi, trang bị đầy đủ điều hòa nóng lạnh.',
                        'property_type' => 'apartment',
                        'base_price' => 4200000,
                    ],
                ],
            ],
            [
                'city' => 'Hà Nội',
                'alias_username' => 'chutro_hn_6',
                'name' => 'Khách sạn Mini & Căn hộ Liễu Giai Riverside Ba Đình',
                'email' => 'chutro.badinh@smartroom.local',
                'phone' => '0988000006',
                'owner_name' => 'Trần Văn Ba Đình',
                'bank_name' => 'BIDV',
                'bank_account_no' => '999988880006',
                'bank_account_name' => 'TRAN VAN BA DINH',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 91,
                'has_hotel' => true,
                'buildings' => [
                    [
                        'code' => 'HN06A',
                        'name' => 'Liễu Giai Riverside Suites & Hotel',
                        'address' => 'Số 35 Ngõ 12 Liễu Giai, Phường Cống Vị, Quận Ba Đình, Hà Nội',
                        'description' => 'Đối diện Lotte Center Liễu Giai, trung tâm ngoại giao, an ninh quân đội cực tốt.',
                        'property_type' => 'hotel',
                        'base_price' => 5200000,
                    ],
                ],
            ],
            [
                'city' => 'Hà Nội',
                'alias_username' => 'chutro_hn_7',
                'name' => 'Tổ hợp Căn hộ & KTX Mỹ Đình Star Nam Từ Liêm',
                'email' => 'chutro.namtuliem@smartroom.local',
                'phone' => '0988000007',
                'owner_name' => 'Bùi Tiến Đạt',
                'bank_name' => 'Techcombank',
                'bank_account_no' => '999988880007',
                'bank_account_name' => 'BUI TIEN DAT',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 93,
                'buildings' => [
                    [
                        'code' => 'HN07A',
                        'name' => 'Chung cư mini Mỹ Đình Star Đình Thôn',
                        'address' => 'Số 16 Ngõ 39 Đình Thôn, Phường Mỹ Đình 1, Quận Nam Từ Liêm, Hà Nội',
                        'description' => 'Khu phố Hàn Quốc sầm uất The Manor, Keangnam Landmark 72, nội thất Châu Âu.',
                        'property_type' => 'apartment',
                        'base_price' => 4300000,
                    ],
                    [
                        'code' => 'HN07B',
                        'name' => 'Nhà trọ Sinh viên & Người đi làm Lê Đức Thọ',
                        'address' => 'Số 48 Ngõ 63 Lê Đức Thọ, Phường Mỹ Đình 2, Quận Nam Từ Liêm, Hà Nội',
                        'description' => 'Gần Sân vận động Mỹ Đình, ĐH Thương Mại, đường thông thoáng, camera 24/24.',
                        'property_type' => 'boarding',
                        'base_price' => 3100000,
                    ],
                ],
            ],
            [
                'city' => 'Hà Nội',
                'alias_username' => 'chutro_hn_8',
                'name' => 'Chung cư mini & Khách sạn Eco Linh Đàm Hoàng Mai',
                'email' => 'chutro.hoangmai@smartroom.local',
                'phone' => '0988000008',
                'owner_name' => 'Hoàng Văn Hùng',
                'bank_name' => 'MB',
                'bank_account_no' => '999988880008',
                'bank_account_name' => 'HOANG VAN HUNG',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 89,
                'has_hotel' => true,
                'buildings' => [
                    [
                        'code' => 'HN08A',
                        'name' => 'Eco Linh Đàm Hotel & Suites',
                        'address' => 'Số 15 BT2 Bán đảo Linh Đàm, Phường Hoàng Liệt, Quận Hoàng Mai, Hà Nội',
                        'description' => 'Khu đô thị sinh thái bán đảo Linh Đàm mát mẻ, phòng rộng có bồn tắm, minibar.',
                        'property_type' => 'hotel',
                        'base_price' => 3900000,
                    ],
                ],
            ],
            [
                'city' => 'Hà Nội',
                'alias_username' => 'chutro_hn_9',
                'name' => 'Cổ Nhuế Green House & KTX Học Viện Bắc Từ Liêm',
                'email' => 'chutro.bactuliem@smartroom.local',
                'phone' => '0988000009',
                'owner_name' => 'Nguyễn Thị Mai',
                'bank_name' => 'VietinBank',
                'bank_account_no' => '999988880009',
                'bank_account_name' => 'NGUYEN THI MAI',
                'verification_status' => 'pending',
                'listing_badge' => 'unverified',
                'boost_score' => 60,
                'buildings' => [
                    [
                        'code' => 'HN09A',
                        'name' => 'Nhà trọ Sinh viên Cổ Nhuế Green House',
                        'address' => 'Số 72 Ngõ 145 Cổ Nhuế, Phường Cổ Nhuế 2, Quận Bắc Từ Liêm, Hà Nội',
                        'description' => 'Gần ĐH Mỏ Địa Chất, Học viện Cảnh Sát, Học viện Tài Chính. Giá rẻ, an ninh tự quản.',
                        'property_type' => 'boarding',
                        'base_price' => 2500000,
                    ],
                ],
            ],
            [
                'city' => 'Hà Nội',
                'alias_username' => 'chutro_hn_10',
                'name' => 'Tòa nhà Căn hộ Dịch vụ Hà Đông Central',
                'email' => 'chutro.hadong@smartroom.local',
                'phone' => '0988000010',
                'owner_name' => 'Trịnh Văn Lâm',
                'bank_name' => 'VPBank',
                'bank_account_no' => '999988880010',
                'bank_account_name' => 'TRINH VAN LAM',
                'verification_status' => 'unverified',
                'listing_badge' => 'unverified',
                'boost_score' => 40,
                'buildings' => [
                    [
                        'code' => 'HN10A',
                        'name' => 'Chung cư mini Hà Đông Central',
                        'address' => 'Số 68 Đường Chiến Thắng, Phường Văn Quán, Quận Hà Đông, Hà Nội',
                        'description' => 'Gần ĐH Kiến Trúc, Học viện An Ninh, Bưu Chính Viễn Thông. Phòng studio có bếp riêng.',
                        'property_type' => 'apartment',
                        'base_price' => 3400000,
                    ],
                ],
            ],

            // =========================================================================
            // II. HỒ CHÍ MINH (10 CHỦ TRỌ - CHIA ĐỀU QUẬN 1, BÌNH THẠNH, THỦ ĐỨC,
            // QUẬN 3, QUẬN 10, QUẬN 7, PHÚ NHUẬN, TÂN BÌNH, GÒ VẤP, QUẬN 5)
            // =========================================================================
            [
                'city' => 'Hồ Chí Minh',
                'alias_username' => 'chutro_hcm_1',
                'name' => 'Saigon Central Boutique Hotel & Studio Q1',
                'email' => 'chutro.saigonq1@smartroom.local',
                'phone' => '0938000011',
                'owner_name' => 'Huỳnh Minh Trí',
                'bank_name' => 'VCB',
                'bank_account_no' => '999988880011',
                'bank_account_name' => 'HUYNH MINH TRI',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 98,
                'has_hotel' => true,
                'buildings' => [
                    [
                        'code' => 'HCM01A',
                        'name' => 'Saigon Central Boutique Hotel Q1',
                        'address' => 'Số 128 Lê Lai, Phường Bến Thành, Quận 1, TP. Hồ Chí Minh',
                        'description' => 'Cách Chợ Bến Thành 300m, phố đi bộ Bùi Viện 2 phút đi bộ. Chuẩn nghỉ dưỡng sang trọng.',
                        'property_type' => 'hotel',
                        'base_price' => 7500000,
                    ],
                    [
                        'code' => 'HCM01B',
                        'name' => 'Căn hộ Studio Vintage Cống Quỳnh',
                        'address' => 'Số 84 Cống Quỳnh, Phường Phạm Ngũ Lão, Quận 1, TP. Hồ Chí Minh',
                        'description' => 'Studio phong cách Indochine hoài niệm giữa lòng Sài Gòn hoa lệ, đầy đủ tiện nghi bếp.',
                        'property_type' => 'apartment',
                        'base_price' => 6800000,
                    ],
                ],
            ],
            [
                'city' => 'Hồ Chí Minh',
                'alias_username' => 'chutro_hcm_2',
                'name' => 'Hệ thống Căn hộ & KTX Sinh viên Renty Bình Thạnh',
                'email' => 'chutro.binhthanh@smartroom.local',
                'phone' => '0938000012',
                'owner_name' => 'Nguyễn Hoàng Phúc',
                'bank_name' => 'ACB',
                'bank_account_no' => '999988880012',
                'bank_account_name' => 'NGUYEN HOANG PHUC',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 96,
                'buildings' => [
                    [
                        'code' => 'HCM02A',
                        'name' => 'Chung cư mini Renty HUTECH Điện Biên Phủ',
                        'address' => 'Số 475A Điện Biên Phủ, Phường 25, Quận Bình Thạnh, TP. Hồ Chí Minh',
                        'description' => 'Cách ĐH HUTECH và Ngoại Thương 100m, view Landmark 81 cực phẩm ngắm pháo hoa.',
                        'property_type' => 'apartment',
                        'base_price' => 4500000,
                    ],
                    [
                        'code' => 'HCM02B',
                        'name' => 'Ký túc xá Cao cấp Xô Viết Nghệ Tĩnh',
                        'address' => 'Số 280 Xô Viết Nghệ Tĩnh, Phường 21, Quận Bình Thạnh, TP. Hồ Chí Minh',
                        'description' => 'Gần cầu Thị Nghè sang Quận 1 chỉ 3 phút, phòng máy lạnh gác lửng rộng rãi.',
                        'property_type' => 'boarding',
                        'base_price' => 3300000,
                    ],
                ],
            ],
            [
                'city' => 'Hồ Chí Minh',
                'alias_username' => 'chutro_hcm_3',
                'name' => 'Làng Đại học Mega Dorm & Căn hộ Công nghệ cao Thủ Đức',
                'email' => 'chutro.thuduc@smartroom.local',
                'phone' => '0938000013',
                'owner_name' => 'Đặng Thanh Tùng',
                'bank_name' => 'MB',
                'bank_account_no' => '999988880013',
                'bank_account_name' => 'DANG THANH TUNG',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 95,
                'buildings' => [
                    [
                        'code' => 'HCM03A',
                        'name' => 'Thủ Đức University Dorm Võ Văn Ngân',
                        'address' => 'Số 18 Võ Văn Ngân, Phường Linh Chiểu, TP. Thủ Đức, TP. Hồ Chí Minh',
                        'description' => 'Cạnh ĐH Sư Phạm Kỹ Thuật (HCMUTE), chợ Bắc Ninh, ga Metro số 1 Bến Thành - Suối Tiên.',
                        'property_type' => 'boarding',
                        'base_price' => 2900000,
                    ],
                    [
                        'code' => 'HCM03B',
                        'name' => 'Căn hộ Studio Grand Park Thủ Đức',
                        'address' => 'Đường Nguyễn Xiển, Phường Long Thạnh Mỹ, TP. Thủ Đức, TP. Hồ Chí Minh',
                        'description' => 'Khu đô thị thông minh công viên ánh sáng, hồ bơi, xe bus điện VinBus đưa đón tận cửa.',
                        'property_type' => 'apartment',
                        'base_price' => 4800000,
                    ],
                ],
            ],
            [
                'city' => 'Hồ Chí Minh',
                'alias_username' => 'chutro_hcm_4',
                'name' => 'The Garden Suites Hotel & Studio Quận 3',
                'email' => 'chutro.quan3@smartroom.local',
                'phone' => '0938000014',
                'owner_name' => 'Phan Thúy Hằng',
                'bank_name' => 'Techcombank',
                'bank_account_no' => '999988880014',
                'bank_account_name' => 'PHAN THUY HANG',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 93,
                'has_hotel' => true,
                'buildings' => [
                    [
                        'code' => 'HCM04A',
                        'name' => 'The Garden Suites Hotel Quận 3',
                        'address' => 'Số 214 Nam Kỳ Khởi Nghĩa, Phường Võ Thị Sáu, Quận 3, TP. Hồ Chí Minh',
                        'description' => 'Tuyến đường huyết mạch sân bay vào trung tâm, cây xanh rợp bóng mát, dịch vụ phòng 24/7.',
                        'property_type' => 'hotel',
                        'base_price' => 6200000,
                    ],
                ],
            ],
            [
                'city' => 'Hồ Chí Minh',
                'alias_username' => 'chutro_hcm_5',
                'name' => 'Căn hộ Bách Khoa & Trọ Sinh viên Thành Thái Q10',
                'email' => 'chutro.quan10@smartroom.local',
                'phone' => '0938000015',
                'owner_name' => 'Lê Quốc Anh',
                'bank_name' => 'VCB',
                'bank_account_no' => '999988880015',
                'bank_account_name' => 'LE QUOC ANH',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 94,
                'buildings' => [
                    [
                        'code' => 'HCM05A',
                        'name' => 'Bách Khoa Studio Home Thành Thái',
                        'address' => 'Số 42 Thành Thái, Phường 12, Quận 10, TP. Hồ Chí Minh',
                        'description' => 'Khu dân cư sầm uất gần Vạn Hạnh Mall, ĐH Y Khoa Phạm Ngọc Thạch, Bách Khoa CS1.',
                        'property_type' => 'apartment',
                        'base_price' => 4600000,
                    ],
                    [
                        'code' => 'HCM05B',
                        'name' => 'KTX Sinh viên Lý Thường Kiệt',
                        'address' => 'Số 268 Lý Thường Kiệt, Phường 14, Quận 10, TP. Hồ Chí Minh',
                        'description' => 'Đối diện cổng chính ĐH Bách Khoa TP.HCM, an ninh nghiêm ngặt, có phòng tự học chung.',
                        'property_type' => 'boarding',
                        'base_price' => 3100000,
                    ],
                ],
            ],
            [
                'city' => 'Hồ Chí Minh',
                'alias_username' => 'chutro_hcm_6',
                'name' => 'Phú Mỹ Hưng Serviced Apartments & Hotel Q7',
                'email' => 'chutro.quan7@smartroom.local',
                'phone' => '0938000016',
                'owner_name' => 'Võ Minh Hải',
                'bank_name' => 'BIDV',
                'bank_account_no' => '999988880016',
                'bank_account_name' => 'VO MINH HAI',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 95,
                'has_hotel' => true,
                'buildings' => [
                    [
                        'code' => 'HCM06A',
                        'name' => 'Phú Mỹ Hưng Green Suites Q7',
                        'address' => 'Số 55 Đường số 7, Phường Tân Phong, Quận 7, TP. Hồ Chí Minh',
                        'description' => 'Khu đô thị kiểu mẫu Phú Mỹ Hưng, gần Crescent Mall, ĐH RMIT, ĐH Tôn Đức Thắng.',
                        'property_type' => 'apartment',
                        'base_price' => 5800000,
                    ],
                    [
                        'code' => 'HCM06B',
                        'name' => 'Sunrise View Hotel Nguyễn Thị Thập Q7',
                        'address' => 'Số 18 Nguyễn Thị Thập, Phường Tân Hưng, Quận 7, TP. Hồ Chí Minh',
                        'description' => 'Gần Lotte Mart Quận 7, phòng ốc hiện đại, dịch vụ đón tiếp khách quốc tế chuyên nghiệp.',
                        'property_type' => 'hotel',
                        'base_price' => 5200000,
                    ],
                ],
            ],
            [
                'city' => 'Hồ Chí Minh',
                'alias_username' => 'chutro_hcm_7',
                'name' => 'Phan Xích Long Smart Living & Studio Phú Nhuận',
                'email' => 'chutro.phunhuan@smartroom.local',
                'phone' => '0938000017',
                'owner_name' => 'Trần Bảo Ngọc',
                'bank_name' => 'Sacombank',
                'bank_account_no' => '999988880017',
                'bank_account_name' => 'TRAN BAO NGOC',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 91,
                'buildings' => [
                    [
                        'code' => 'HCM07A',
                        'name' => 'Phan Xích Long Smart Living',
                        'address' => 'Số 88 Phan Xích Long, Phường 2, Quận Phú Nhuận, TP. Hồ Chí Minh',
                        'description' => 'Phố ẩm thực sầm uất bậc nhất Phú Nhuận, đi sân bay hay sang Quận 1 chỉ 5 phút.',
                        'property_type' => 'apartment',
                        'base_price' => 4900000,
                    ],
                ],
            ],
            [
                'city' => 'Hồ Chí Minh',
                'alias_username' => 'chutro_hcm_8',
                'name' => 'Airport Transit Hotel & Nhà trọ Sân Bay Tân Bình',
                'email' => 'chutro.tanbinh@smartroom.local',
                'phone' => '0938000018',
                'owner_name' => 'Đỗ Quang Minh',
                'bank_name' => 'TPBank',
                'bank_account_no' => '999988880018',
                'bank_account_name' => 'DO QUANG MINH',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 92,
                'has_hotel' => true,
                'buildings' => [
                    [
                        'code' => 'HCM08A',
                        'name' => 'Airport Transit Hotel Tân Sơn Nhất',
                        'address' => 'Số 32 Bạch Đằng, Phường 2, Quận Tân Bình, TP. Hồ Chí Minh',
                        'description' => 'Cách cổng ga quốc nội Tân Sơn Nhất chỉ 500m, hỗ trợ xe đưa đón, thuê phòng ngày/giờ.',
                        'property_type' => 'hotel',
                        'base_price' => 4600000,
                    ],
                    [
                        'code' => 'HCM08B',
                        'name' => 'Nhà trọ Sinh viên Hoàng Hoa Thám',
                        'address' => 'Số 154 Hoàng Hoa Thám, Phường 12, Quận Tân Bình, TP. Hồ Chí Minh',
                        'description' => 'Gần chợ Hoàng Hoa Thám, rạp chiếu phim, khu dân cư an ninh, giá thuê mềm.',
                        'property_type' => 'boarding',
                        'base_price' => 3000000,
                    ],
                ],
            ],
            [
                'city' => 'Hồ Chí Minh',
                'alias_username' => 'chutro_hcm_9',
                'name' => 'Nhà trọ & Chung cư mini Quang Trung Gò Vấp',
                'email' => 'chutro.govap@smartroom.local',
                'phone' => '0938000019',
                'owner_name' => 'Nguyễn Thị Kim Ngân',
                'bank_name' => 'MB',
                'bank_account_no' => '999988880019',
                'bank_account_name' => 'NGUYEN THI KIM NGAN',
                'verification_status' => 'pending',
                'listing_badge' => 'unverified',
                'boost_score' => 65,
                'buildings' => [
                    [
                        'code' => 'HCM09A',
                        'name' => 'Quang Trung Modern House Gò Vấp',
                        'address' => 'Số 520 Quang Trung, Phường 11, Quận Gò Vấp, TP. Hồ Chí Minh',
                        'description' => 'Trục đường mua sắm sầm uất Quang Trung, gần ngã năm Chuồng Chó, chợ Hạnh Thông Tây.',
                        'property_type' => 'boarding',
                        'base_price' => 3100000,
                    ],
                ],
            ],
            [
                'city' => 'Hồ Chí Minh',
                'alias_username' => 'chutro_hcm_10',
                'name' => 'Chợ Lớn Heritage House & Trọ Y Dược Quận 5',
                'email' => 'chutro.quan5@smartroom.local',
                'phone' => '0938000020',
                'owner_name' => 'Vương Quốc Hào',
                'bank_name' => 'VCB',
                'bank_account_no' => '999988880020',
                'bank_account_name' => 'VUONG QUOC HAO',
                'verification_status' => 'unverified',
                'listing_badge' => 'unverified',
                'boost_score' => 45,
                'buildings' => [
                    [
                        'code' => 'HCM10A',
                        'name' => 'Chợ Lớn Studio & Hotel Quận 5',
                        'address' => 'Số 86 Nguyễn Trãi, Phường 3, Quận 5, TP. Hồ Chí Minh',
                        'description' => 'Khu phố người Hoa Chợ Lớn đặc sắc, gần ĐH Sư Phạm, ĐH Sài Gòn, ĐH Y Dược TP.HCM.',
                        'property_type' => 'apartment',
                        'base_price' => 4100000,
                    ],
                ],
            ],

            // =========================================================================
            // III. ĐÀ NẴNG (10 CHỦ TRỌ - CHIA ĐỀU HẢI CHÂU, SƠN TRÀ MỸ KHÊ, NGŨ HÀNH SƠN,
            // THANH KHÊ, LIÊN CHIỂU BÁCH KHOA, CẨM LỆ, HÒA VANG, PHỐ TÂY AN THƯỢNG)
            // =========================================================================
            [
                'city' => 'Đà Nẵng',
                'alias_username' => 'chutro_dn_1',
                'name' => 'Sông Hàn Riverside Hotel & Luxury Apartment Đà Nẵng',
                'email' => 'chutro.dananghc@smartroom.local',
                'phone' => '0905000021',
                'owner_name' => 'Trần Đình Nam',
                'bank_name' => 'VCB',
                'bank_account_no' => '999988880021',
                'bank_account_name' => 'TRAN DINH NAM',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 97,
                'has_hotel' => true,
                'buildings' => [
                    [
                        'code' => 'DN01A',
                        'name' => 'Han River Riverside Hotel Bạch Đằng',
                        'address' => 'Số 74 Bạch Đằng, Phường Hải Châu 1, Quận Hải Châu, TP. Đà Nẵng',
                        'description' => 'Trực diện bờ sông Hàn ngắm Cầu Rồng phun lửa và Cầu Quay, trung tâm lễ hội pháo hoa quốc tế.',
                        'property_type' => 'hotel',
                        'base_price' => 6500000,
                    ],
                    [
                        'code' => 'DN01B',
                        'name' => 'Căn hộ Cao cấp Nguyễn Văn Linh',
                        'address' => 'Số 150 Nguyễn Văn Linh, Phường Nam Dương, Quận Hải Châu, TP. Đà Nẵng',
                        'description' => 'Tuyến phố tài chính ngân hàng Đà Nẵng, gần sân bay quốc tế và cầu Rồng.',
                        'property_type' => 'apartment',
                        'base_price' => 5200000,
                    ],
                ],
            ],
            [
                'city' => 'Đà Nẵng',
                'alias_username' => 'chutro_dn_2',
                'name' => 'Biển Xanh Mỹ Khê Beach Hotel & Resort Suites Sơn Trà',
                'email' => 'chutro.danangst@smartroom.local',
                'phone' => '0905000022',
                'owner_name' => 'Lê Thị Thu Hương',
                'bank_name' => 'MB',
                'bank_account_no' => '999988880022',
                'bank_account_name' => 'LE THI THU HUONG',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 98,
                'has_hotel' => true,
                'buildings' => [
                    [
                        'code' => 'DN02A',
                        'name' => 'My Khe Blue Ocean Hotel',
                        'address' => 'Số 258 Võ Nguyên Giáp, Phường Phước Mỹ, Quận Sơn Trà, TP. Đà Nẵng',
                        'description' => 'Đối diện công viên Biển Đông bãi tắm Mỹ Khê lọt top hành tinh, view biển Panorama.',
                        'property_type' => 'hotel',
                        'base_price' => 7000000,
                    ],
                    [
                        'code' => 'DN02B',
                        'name' => 'Biển Đông Studio Apartment',
                        'address' => 'Số 19 Dương Đình Nghệ, Phường An Hải Bắc, Quận Sơn Trà, TP. Đà Nẵng',
                        'description' => 'Khu phố du lịch Hàn Quốc sầm uất, cách bãi tắm Phạm Văn Đồng 200m.',
                        'property_type' => 'apartment',
                        'base_price' => 4800000,
                    ],
                ],
            ],
            [
                'city' => 'Đà Nẵng',
                'alias_username' => 'chutro_dn_3',
                'name' => 'Non Nước Luxury Boutique & Trọ Kinh Tế Ngũ Hành Sơn',
                'email' => 'chutro.danangnhs@smartroom.local',
                'phone' => '0905000023',
                'owner_name' => 'Nguyễn Văn Danh',
                'bank_name' => 'BIDV',
                'bank_account_no' => '999988880023',
                'bank_account_name' => 'NGUYEN VAN DANH',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 93,
                'has_hotel' => true,
                'buildings' => [
                    [
                        'code' => 'DN03A',
                        'name' => 'Non Nước Boutique Suites Trường Sa',
                        'address' => 'Số 36 Trường Sa, Phường Hòa Hải, Quận Ngũ Hành Sơn, TP. Đà Nẵng',
                        'description' => 'Cung đường Resort 5 sao kết nối Đà Nẵng - Hội An, cạnh danh thắng Ngũ Hành Sơn.',
                        'property_type' => 'hotel',
                        'base_price' => 5000000,
                    ],
                    [
                        'code' => 'DN03B',
                        'name' => 'Trọ Sinh viên ĐH Kinh Tế Đà Nẵng',
                        'address' => 'Số 71 Ngũ Hành Sơn, Phường Mỹ An, Quận Ngũ Hành Sơn, TP. Đà Nẵng',
                        'description' => 'Ngay cổng trường ĐH Kinh Tế ĐN, gần cầu Trần Thị Lý, giá hợp túi tiền sinh viên.',
                        'property_type' => 'boarding',
                        'base_price' => 2600000,
                    ],
                ],
            ],
            [
                'city' => 'Đà Nẵng',
                'alias_username' => 'chutro_dn_4',
                'name' => 'Vịnh Đà Nẵng Studio & Nhà trọ Thanh Khê',
                'email' => 'chutro.danangtk@smartroom.local',
                'phone' => '0905000024',
                'owner_name' => 'Mai Văn Thành',
                'bank_name' => 'Techcombank',
                'bank_account_no' => '999988880024',
                'bank_account_name' => 'MAI VAN THANH',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 90,
                'buildings' => [
                    [
                        'code' => 'DN04A',
                        'name' => 'Vịnh Đà Nẵng View Home Nguyễn Tất Thành',
                        'address' => 'Số 312 Nguyễn Tất Thành, Phường Tam Thuận, Quận Thanh Khê, TP. Đà Nẵng',
                        'description' => 'Đường ven biển vịnh Đà Nẵng lộng gió, ngắm trọn cung đường đèo Hải Vân hùng vĩ.',
                        'property_type' => 'apartment',
                        'base_price' => 3800000,
                    ],
                    [
                        'code' => 'DN04B',
                        'name' => 'Trọ Sinh viên Điện Biên Phủ Thanh Khê',
                        'address' => 'Số 420 Điện Biên Phủ, Phường Thanh Khê Đông, Quận Thanh Khê, TP. Đà Nẵng',
                        'description' => 'Trục đường mua sắm trung tâm, gần hầm chui ngã ba Huế, trọ khép kín sạch sẽ.',
                        'property_type' => 'boarding',
                        'base_price' => 2500000,
                    ],
                ],
            ],
            [
                'city' => 'Đà Nẵng',
                'alias_username' => 'chutro_dn_5',
                'name' => 'Ký túc xá & Phòng trọ Sinh viên Bách Khoa Liên Chiểu',
                'email' => 'chutro.dananglc@smartroom.local',
                'phone' => '0905000025',
                'owner_name' => 'Phạm Quốc Trung',
                'bank_name' => 'VCB',
                'bank_account_no' => '999988880025',
                'bank_account_name' => 'PHAM QUOC TRUNG',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 94,
                'buildings' => [
                    [
                        'code' => 'DN05A',
                        'name' => 'Bách Khoa Smart Dorm Nguyễn Lương Bằng',
                        'address' => 'Số 54 Nguyễn Lương Bằng, Phường Hòa Khánh Bắc, Quận Liên Chiểu, TP. Đà Nẵng',
                        'description' => 'Cách cổng ĐH Bách Khoa và ĐH Sư Phạm ĐN chỉ 150m, chợ Hòa Khánh sầm uất.',
                        'property_type' => 'boarding',
                        'base_price' => 2400000,
                    ],
                    [
                        'code' => 'DN05B',
                        'name' => 'Căn hộ Khu Công nghệ Cao Đà Nẵng Tôn Đức Thắng',
                        'address' => 'Số 102 Tôn Đức Thắng, Phường Hòa Minh, Quận Liên Chiểu, TP. Đà Nẵng',
                        'description' => 'Gần Bến xe trung tâm Đà Nẵng, phù hợp kỹ sư, sinh viên và chuyên gia.',
                        'property_type' => 'apartment',
                        'base_price' => 3600000,
                    ],
                ],
            ],
            [
                'city' => 'Đà Nẵng',
                'alias_username' => 'chutro_dn_6',
                'name' => 'Cẩm Lệ Green Living & Chung cư mini Khuê Trung',
                'email' => 'chutro.danangcl@smartroom.local',
                'phone' => '0905000026',
                'owner_name' => 'Đặng Thị Lan',
                'bank_name' => 'Agribank',
                'bank_account_no' => '999988880026',
                'bank_account_name' => 'DANG THI LAN',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 88,
                'buildings' => [
                    [
                        'code' => 'DN06A',
                        'name' => 'Cẩm Lệ Central Apartment Cách Mạng Tháng 8',
                        'address' => 'Số 89 Cách Mạng Tháng 8, Phường Khuê Trung, Quận Cẩm Lệ, TP. Đà Nẵng',
                        'description' => 'Gần Trung tâm Hội chợ Triển lãm, ĐH Ngoại Ngữ Đà Nẵng, không gian thoáng đãng.',
                        'property_type' => 'apartment',
                        'base_price' => 3300000,
                    ],
                ],
            ],
            [
                'city' => 'Đà Nẵng',
                'alias_username' => 'chutro_dn_7',
                'name' => 'Bán đảo Sơn Trà Nature Homestay & Suites',
                'email' => 'chutro.danangst2@smartroom.local',
                'phone' => '0905000027',
                'owner_name' => 'Trương Quang Khải',
                'bank_name' => 'VPBank',
                'bank_account_no' => '999988880027',
                'bank_account_name' => 'TRUONG QUANG KHAI',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 92,
                'has_hotel' => true,
                'buildings' => [
                    [
                        'code' => 'DN07A',
                        'name' => 'Son Tra Nature Lodge Hoàng Sa',
                        'address' => 'Số 15 Hoàng Sa, Phường Thọ Quang, Quận Sơn Trà, TP. Đà Nẵng',
                        'description' => 'Dưới chân núi Sơn Trà và Chùa Linh Ứng Bãi Bụt, không gian sinh thái nghỉ dưỡng tuyệt vời.',
                        'property_type' => 'hotel',
                        'base_price' => 4500000,
                    ],
                ],
            ],
            [
                'city' => 'Đà Nẵng',
                'alias_username' => 'chutro_dn_8',
                'name' => 'Dragon Bridge Studio & Suites 2 Tháng 9 Hải Châu',
                'email' => 'chutro.dananghc2@smartroom.local',
                'phone' => '0905000028',
                'owner_name' => 'Bùi Hồng Sơn',
                'bank_name' => 'MB',
                'bank_account_no' => '999988880028',
                'bank_account_name' => 'BUI HONG SON',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 93,
                'buildings' => [
                    [
                        'code' => 'DN08A',
                        'name' => 'Dragon Bridge Studio & Suites',
                        'address' => 'Số 210 Đường 2 Tháng 9, Phường Hòa Cường Bắc, Quận Hải Châu, TP. Đà Nẵng',
                        'description' => 'Cạnh công viên APEC, Cung Thể thao Tiên Sơn, Lotte Mart, trung tâm ăn uống sầm uất.',
                        'property_type' => 'apartment',
                        'base_price' => 4200000,
                    ],
                ],
            ],
            [
                'city' => 'Đà Nẵng',
                'alias_username' => 'chutro_dn_9',
                'name' => 'Khu nhà trọ Công nhân Hòa Khánh - Hòa Vang',
                'email' => 'chutro.dananghv@smartroom.local',
                'phone' => '0905000029',
                'owner_name' => 'Nguyễn Đức Hạnh',
                'bank_name' => 'Agribank',
                'bank_account_no' => '999988880029',
                'bank_account_name' => 'NGUYEN DUC HANH',
                'verification_status' => 'pending',
                'listing_badge' => 'unverified',
                'boost_score' => 60,
                'buildings' => [
                    [
                        'code' => 'DN09A',
                        'name' => 'Hòa Vang Eco Boarding House',
                        'address' => 'Tuyến QL14B, Xã Hòa Nhơn, Huyện Hòa Vang, TP. Đà Nẵng',
                        'description' => 'Khuôn viên nhà trọ sinh thái cây xanh mát mẻ, an ninh trật tự làng xóm văn minh.',
                        'property_type' => 'boarding',
                        'base_price' => 2000000,
                    ],
                ],
            ],
            [
                'city' => 'Đà Nẵng',
                'alias_username' => 'chutro_dn_10',
                'name' => 'Phố Tây An Thượng Hotel & Apartment Ngũ Hành Sơn',
                'email' => 'chutro.dananganthuong@smartroom.local',
                'phone' => '0905000030',
                'owner_name' => 'Lâm Hoài Nam',
                'bank_name' => 'VCB',
                'bank_account_no' => '999988880030',
                'bank_account_name' => 'LAM HOAI NAM',
                'verification_status' => 'unverified',
                'listing_badge' => 'unverified',
                'boost_score' => 50,
                'has_hotel' => true,
                'buildings' => [
                    [
                        'code' => 'DN10A',
                        'name' => 'An Thuong Western Hotel & Suites',
                        'address' => 'Số 12 An Thượng 2, Phường Mỹ An, Quận Ngũ Hành Sơn, TP. Đà Nẵng',
                        'description' => 'Khu phố Tây An Thượng nhộn nhịp về đêm, bước ra biển 50m, tập trung nhiều du khách nước ngoài.',
                        'property_type' => 'hotel',
                        'base_price' => 4800000,
                    ],
                ],
            ],

            // =========================================================================
            // IV. ĐÀ LẠT - LÂM ĐỒNG (10 CHỦ TRỌ - CHIA ĐỀU CÁC KHU VỰC PHƯỜNG 1 CHỢ ĐÊM,
            // PHƯỜNG 2 BÙI THỊ XUÂN, PHƯỜNG 3 ĐÈO PRENN, PHƯỜNG 4 VẠN THÀNH, PHƯỜNG 8 ĐH ĐÀ LẠT,
            // PHƯỜNG 10 GA ĐÀ LẠT, PHƯỜNG 11 TRẠI MÁT, PHƯỜNG 9 HÙNG VƯƠNG, PHƯỜNG 12, HỒ TUYỀN LÂM)
            // =========================================================================
            [
                'city' => 'Đà Lạt',
                'alias_username' => 'chutro_dl_1',
                'name' => 'Hồ Xuân Hương Panorama Hotel & Căn hộ Trung tâm Đà Lạt',
                'email' => 'chutro.dalatp1@smartroom.local',
                'phone' => '0963000031',
                'owner_name' => 'Đoàn Minh Thông',
                'bank_name' => 'VCB',
                'bank_account_no' => '999988880031',
                'bank_account_name' => 'DOAN MINH THONG',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 99,
                'has_hotel' => true,
                'buildings' => [
                    [
                        'code' => 'DL01A',
                        'name' => 'Xuan Huong Lakeview Hotel',
                        'address' => 'Số 10 Nguyễn Chí Thanh, Phường 1, TP. Đà Lạt, Tỉnh Lâm Đồng',
                        'description' => 'Trực diện Hồ Xuân Hương và Chợ Đêm Đà Lạt, phòng sưởi ấm cúng view hồ lãng mạn.',
                        'property_type' => 'hotel',
                        'base_price' => 6000000,
                    ],
                    [
                        'code' => 'DL01B',
                        'name' => 'Căn hộ Phố Đi Bộ Lê Thị Hồng Gấm',
                        'address' => 'Số 35 Lê Thị Hồng Gấm, Phường 1, TP. Đà Lạt, Tỉnh Lâm Đồng',
                        'description' => 'Ngay chợ Đà Lạt, thuận tiện đi bộ ăn bánh tráng nướng, uống sữa đậu nành nóng hổi.',
                        'property_type' => 'apartment',
                        'base_price' => 5000000,
                    ],
                ],
            ],
            [
                'city' => 'Đà Lạt',
                'alias_username' => 'chutro_dl_2',
                'name' => 'Ngàn Hoa Boutique Hotel & Trọ Sinh viên Đà Lạt Phường 2',
                'email' => 'chutro.dalatp2@smartroom.local',
                'phone' => '0963000032',
                'owner_name' => 'Phan Cẩm Ly',
                'bank_name' => 'MB',
                'bank_account_no' => '999988880032',
                'bank_account_name' => 'PHAN CAM LY',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 95,
                'has_hotel' => true,
                'buildings' => [
                    [
                        'code' => 'DL02A',
                        'name' => 'Ngan Hoa Boutique Hotel Bùi Thị Xuân',
                        'address' => 'Số 72 Bùi Thị Xuân, Phường 2, TP. Đà Lạt, Tỉnh Lâm Đồng',
                        'description' => 'Khu phố ẩm thực cà phê sầm uất Bùi Thị Xuân, phòng decor hoa cẩm tú cầu nhẹ nhàng.',
                        'property_type' => 'hotel',
                        'base_price' => 4800000,
                    ],
                    [
                        'code' => 'DL02B',
                        'name' => 'Nhà trọ Sinh viên Phan Đình Phùng',
                        'address' => 'Số 145 Phan Đình Phùng, Phường 2, TP. Đà Lạt, Tỉnh Lâm Đồng',
                        'description' => 'Đường lớn trung tâm Đà Lạt, gần các trường cao đẳng và cửa hàng tiện lợi 24/7.',
                        'property_type' => 'boarding',
                        'base_price' => 2800000,
                    ],
                ],
            ],
            [
                'city' => 'Đà Lạt',
                'alias_username' => 'chutro_dl_3',
                'name' => 'Đồi Robin Pine View Villa & Suites Phường 3',
                'email' => 'chutro.dalatp3@smartroom.local',
                'phone' => '0963000033',
                'owner_name' => 'Lâm Thanh Tuấn',
                'bank_name' => 'Sacombank',
                'bank_account_no' => '999988880033',
                'bank_account_name' => 'LAM THANH TUAN',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 94,
                'has_hotel' => true,
                'buildings' => [
                    [
                        'code' => 'DL03A',
                        'name' => 'Robin Hill Pine Hotel Đống Đa',
                        'address' => 'Số 01 Đống Đa, Phường 3, TP. Đà Lạt, Tỉnh Lâm Đồng',
                        'description' => 'Trên đồi Robin ngắm toàn cảnh thung lũng sương mù, ngay trạm Cáp treo Đà Lạt.',
                        'property_type' => 'hotel',
                        'base_price' => 5500000,
                    ],
                    [
                        'code' => 'DL03B',
                        'name' => 'Homestay Căn hộ Rừng Thông Ba Tháng Tư',
                        'address' => 'Số 22 Đường Ba Tháng Tư, Phường 3, TP. Đà Lạt, Tỉnh Lâm Đồng',
                        'description' => 'Cửa ngõ Đèo Prenn vào TP. Đà Lạt, gần Bến xe Liên tỉnh, bao quanh bởi rừng thông nguyên sinh.',
                        'property_type' => 'apartment',
                        'base_price' => 4500000,
                    ],
                ],
            ],
            [
                'city' => 'Đà Lạt',
                'alias_username' => 'chutro_dl_4',
                'name' => 'Vạn Thành Valley Homestay & Nhà trọ Phường 4',
                'email' => 'chutro.dalatp4@smartroom.local',
                'phone' => '0963000034',
                'owner_name' => 'Nguyễn Kim Yến',
                'bank_name' => 'VietinBank',
                'bank_account_no' => '999988880034',
                'bank_account_name' => 'NGUYEN KIM YEN',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 92,
                'has_hotel' => true,
                'buildings' => [
                    [
                        'code' => 'DL04A',
                        'name' => 'Vạn Thành Valley Lodge Vạn Hạnh',
                        'address' => 'Số 40 Vạn Hạnh, Phường 4, TP. Đà Lạt, Tỉnh Lâm Đồng',
                        'description' => 'Lọt giữa thung lũng Làng hoa Vạn Thành ngát hương, gần Dinh 3 Bảo Đại.',
                        'property_type' => 'hotel',
                        'base_price' => 4600000,
                    ],
                    [
                        'code' => 'DL04B',
                        'name' => 'Nhà trọ Công nhân Vạn Thành Hoàng Văn Thụ',
                        'address' => 'Số 88 Hoàng Văn Thụ, Phường 4, TP. Đà Lạt, Tỉnh Lâm Đồng',
                        'description' => 'Khu dân cư hiền hòa gần thác Cam Ly, phòng ốc ấm cúng đầy đủ nước nóng lạnh.',
                        'property_type' => 'boarding',
                        'base_price' => 2600000,
                    ],
                ],
            ],
            [
                'city' => 'Đà Lạt',
                'alias_username' => 'chutro_dl_5',
                'name' => 'Ký túc xá & Trọ Sinh viên Đại học Đà Lạt Phường 8',
                'email' => 'chutro.dalatp8@smartroom.local',
                'phone' => '0963000035',
                'owner_name' => 'Vũ Đình Trọng',
                'bank_name' => 'VCB',
                'bank_account_no' => '999988880035',
                'bank_account_name' => 'VU DINH TRONG',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 96,
                'buildings' => [
                    [
                        'code' => 'DL05A',
                        'name' => 'ĐH Đà Lạt Student Dorm Phù Đổng Thiên Vương',
                        'address' => 'Số 1 Phù Đổng Thiên Vương, Phường 8, TP. Đà Lạt, Tỉnh Lâm Đồng',
                        'description' => 'Sát cổng Trường Đại học Đà Lạt thơ mộng, khu sinh viên ăn uống nhộn nhịp giá hạt dẻ.',
                        'property_type' => 'boarding',
                        'base_price' => 2400000,
                    ],
                    [
                        'code' => 'DL05B',
                        'name' => 'Căn hộ Studio Thung Lũng Tình Yêu Mai Anh Đào',
                        'address' => 'Số 24 Mai Anh Đào, Phường 8, TP. Đà Lạt, Tỉnh Lâm Đồng',
                        'description' => 'Cạnh KDL Thung Lũng Tình Yêu và Đồi Mộng Mơ, view đồi thông xanh ngát.',
                        'property_type' => 'apartment',
                        'base_price' => 3800000,
                    ],
                ],
            ],
            [
                'city' => 'Đà Lạt',
                'alias_username' => 'chutro_dl_6',
                'name' => 'Ga Đà Lạt Heritage Hotel & Căn hộ Dịch vụ Phường 10',
                'email' => 'chutro.dalatp10@smartroom.local',
                'phone' => '0963000036',
                'owner_name' => 'Hoàng Gia Long',
                'bank_name' => 'BIDV',
                'bank_account_no' => '999988880036',
                'bank_account_name' => 'HOANG GIA LONG',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 95,
                'has_hotel' => true,
                'buildings' => [
                    [
                        'code' => 'DL06A',
                        'name' => 'Old Railway Heritage Hotel Quang Trung',
                        'address' => 'Số 01 Quang Trung, Phường 10, TP. Đà Lạt, Tỉnh Lâm Đồng',
                        'description' => 'Ngay ga xe lửa cổ nhất Đông Dương, kiến trúc Pháp cổ kính sang trọng.',
                        'property_type' => 'hotel',
                        'base_price' => 5400000,
                    ],
                    [
                        'code' => 'DL06B',
                        'name' => 'Lam Vien Square Studio Yersin',
                        'address' => 'Số 18 Yersin, Phường 10, TP. Đà Lạt, Tỉnh Lâm Đồng',
                        'description' => 'Đi bộ ra Quảng trường Lâm Viên (Bông Atiso & Hoa Dã Quỳ), rạp chiếu phim Cinestar.',
                        'property_type' => 'apartment',
                        'base_price' => 4600000,
                    ],
                ],
            ],
            [
                'city' => 'Đà Lạt',
                'alias_username' => 'chutro_dl_7',
                'name' => 'Trại Mát Cloud Villa & Nhà trọ Phường 11 Đà Lạt',
                'email' => 'chutro.dalatp11@smartroom.local',
                'phone' => '0963000037',
                'owner_name' => 'Lê Trọng Nhân',
                'bank_name' => 'Agribank',
                'bank_account_no' => '999988880037',
                'bank_account_name' => 'LE TRONG NHAN',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 91,
                'has_hotel' => true,
                'buildings' => [
                    [
                        'code' => 'DL07A',
                        'name' => 'Trai Mat Cloud Villa & Homestay',
                        'address' => 'Số 120 Tự Phước, Phường 11, TP. Đà Lạt, Tỉnh Lâm Đồng',
                        'description' => 'Thiên đường săn mây Trại Mát, gần Chùa Linh Phước (Chùa Ve Chai), tuyến tàu hỏa răng cưa.',
                        'property_type' => 'hotel',
                        'base_price' => 4300000,
                    ],
                ],
            ],
            [
                'city' => 'Đà Lạt',
                'alias_username' => 'chutro_dl_8',
                'name' => 'Hùng Vương Royal Garden Villa & Căn hộ Phường 9',
                'email' => 'chutro.dalatp9@smartroom.local',
                'phone' => '0963000038',
                'owner_name' => 'Đỗ Thu Hà',
                'bank_name' => 'Techcombank',
                'bank_account_no' => '999988880038',
                'bank_account_name' => 'DO THU HA',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 93,
                'buildings' => [
                    [
                        'code' => 'DL08A',
                        'name' => 'Royal Garden Suites Da Lat Hùng Vương',
                        'address' => 'Số 42 Hùng Vương, Phường 9, TP. Đà Lạt, Tỉnh Lâm Đồng',
                        'description' => 'Cung đường biệt thự cổ kính ngắm thung lũng ánh sáng về đêm lung linh rực rỡ.',
                        'property_type' => 'apartment',
                        'base_price' => 4700000,
                    ],
                ],
            ],
            [
                'city' => 'Đà Lạt',
                'alias_username' => 'chutro_dl_9',
                'name' => 'Thái Phiên Thung Lũng Ánh Sáng Homestay Phường 12',
                'email' => 'chutro.dalatp12@smartroom.local',
                'phone' => '0963000039',
                'owner_name' => 'Bùi Vĩnh Phát',
                'bank_name' => 'MB',
                'bank_account_no' => '999988880039',
                'bank_account_name' => 'BUI VINH PHAT',
                'verification_status' => 'pending',
                'listing_badge' => 'unverified',
                'boost_score' => 62,
                'has_hotel' => true,
                'buildings' => [
                    [
                        'code' => 'DL09A',
                        'name' => 'Light Valley Farmstay & Suites Ngô Gia Tự',
                        'address' => 'Số 55 Ngô Gia Tự, Phường 12, TP. Đà Lạt, Tỉnh Lâm Đồng',
                        'description' => 'Trực diện làng hoa Thái Phiên, đêm xuống cả thung lũng lên đèn nhà lồng vàng rực kỳ ảo.',
                        'property_type' => 'hotel',
                        'base_price' => 3900000,
                    ],
                ],
            ],
            [
                'city' => 'Đà Lạt',
                'alias_username' => 'chutro_dl_10',
                'name' => 'Tuyền Lâm Sunset Eco Resort & Lakeview Villa',
                'email' => 'chutro.dalattuyenlam@smartroom.local',
                'phone' => '0963000040',
                'owner_name' => 'Nguyễn Thảo Vy',
                'bank_name' => 'VCB',
                'bank_account_no' => '999988880040',
                'bank_account_name' => 'NGUYEN THAO VY',
                'verification_status' => 'unverified',
                'listing_badge' => 'unverified',
                'boost_score' => 50,
                'has_hotel' => true,
                'buildings' => [
                    [
                        'code' => 'DL10A',
                        'name' => 'Tuyen Lam Sunset Eco Resort',
                        'address' => 'Khu du lịch Quốc gia Hồ Tuyền Lâm, Phường 3, TP. Đà Lạt, Tỉnh Lâm Đồng',
                        'description' => 'Ven hồ Tuyền Lâm phẳng lặng và Thiền Viện Trúc Lâm, không gian biệt lập tĩnh lặng bình yên.',
                        'property_type' => 'hotel',
                        'base_price' => 5900000,
                    ],
                ],
            ],
        ];
    }
}
