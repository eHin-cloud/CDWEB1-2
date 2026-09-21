<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\Building;
use App\Models\Room;
use App\Models\HotelBooking;
use App\Models\HotelFolioItem;
use App\Models\Contract;
use App\Models\Resident;
use App\Models\UtilityRecord;
use App\Models\User;
use Carbon\Carbon;

class HospitalityDummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Lấy hoặc tạo Tenant Demo chính
        $tenant = Tenant::first() ?: Tenant::create([
            'name' => 'Tập đoàn Khách Sạn & Nhà Trọ SmartRoom Cầu Giấy',
            'email' => 'contact@smartroom-caugiay.vn',
            'phone' => '0988123456',
            'bank_name' => 'MB',
            'bank_account_no' => '9999888889999',
            'bank_account_name' => 'NGUYEN ANH QUY',
        ]);
        $tenantId = $tenant->id;

        // Đảm bảo các tài khoản demo thuộc về Tenant này
        User::whereIn('username', ['demo-landlord-1', 'demo-receptionist', 'demo-housekeeper', 'demo-manager-1-1'])
            ->update(['tenant_id' => $tenantId]);

        // =========================================================================
        // MÔ HÌNH 1: KHÁCH SẠN / HOMESTAY NGHỈ DƯỠNG (property_type = 'hotel')
        // =========================================================================
        $hotel = Building::updateOrCreate(
            ['tenant_id' => $tenantId, 'name' => 'Khách Sạn Renty Star Boutique & Suites'],
            [
                'address' => 'Số 68 Đường Cầu Giấy, P. Quan Hoa, Q. Cầu Giấy, Hà Nội',
                'property_type' => 'hotel',
                'description' => 'Khách sạn phong cách Boutique 4 sao, hỗ trợ đón khách theo giờ, theo ngày/đêm và minibar tự động.',
                'checkin_time' => '14:00:00',
                'checkout_time' => '12:00:00',
            ]
        );

        // Danh sách phòng khách sạn mẫu với đầy đủ các trạng thái lưu trú & dọn dẹp
        $hotelRoomsData = [
            [
                'room_number' => 'KS-201',
                'floor' => 2,
                'room_type' => 'vip',
                'rental_type' => 'day',
                'price' => 12000000,
                'price_per_day' => 650000,
                'price_per_hour' => 180000,
                'price_extra_hour' => 50000,
                'status' => 'occupied',
                'cleaning_status' => 'clean',
                'area' => 38,
                'description' => 'Phòng VIP Suite giường King-size, bồn tắm nằm massage, ban công view thành phố, minibar đầy đủ đồ uống.',
                'amenities' => ['wifi', 'tv', 'ac', 'fridge', 'water_heater', 'minibar', 'balcony', 'smart_lock'],
            ],
            [
                'room_number' => 'KS-202',
                'floor' => 2,
                'room_type' => 'deluxe',
                'rental_type' => 'hour',
                'price' => 8500000,
                'price_per_day' => 480000,
                'price_per_hour' => 140000,
                'price_extra_hour' => 45000,
                'status' => 'occupied',
                'cleaning_status' => 'clean',
                'area' => 28,
                'description' => 'Phòng Deluxe sang trọng thích hợp thuê theo giờ hoặc ngắn ngày, cách âm cao cấp, đèn LED đổi màu.',
                'amenities' => ['wifi', 'tv', 'ac', 'fridge', 'minibar', 'smart_lock'],
            ],
            [
                'room_number' => 'KS-203',
                'floor' => 2,
                'room_type' => 'normal',
                'rental_type' => 'day',
                'price' => 7000000,
                'price_per_day' => 380000,
                'price_per_hour' => 110000,
                'price_extra_hour' => 35000,
                'status' => 'cleaning',
                'cleaning_status' => 'dirty',
                'area' => 24,
                'description' => 'Phòng Standard Queen ấm cúng. (Khách vừa trả phòng lúc 11:30 - Chờ buồng phòng vào vệ sinh).',
                'amenities' => ['wifi', 'ac', 'fridge', 'minibar'],
            ],
            [
                'room_number' => 'KS-204',
                'floor' => 2,
                'room_type' => 'normal',
                'rental_type' => 'hour',
                'price' => 7000000,
                'price_per_day' => 380000,
                'price_per_hour' => 110000,
                'price_extra_hour' => 35000,
                'status' => 'cleaning',
                'cleaning_status' => 'cleaning',
                'area' => 24,
                'description' => 'Phòng Standard 2 giường đơn. (Nhân viên buồng phòng đang tiến hành thay drap giường và khử khuẩn).',
                'amenities' => ['wifi', 'ac', 'fridge'],
            ],
            [
                'room_number' => 'KS-205',
                'floor' => 2,
                'room_type' => 'deluxe',
                'rental_type' => 'day',
                'price' => 9000000,
                'price_per_day' => 520000,
                'price_per_hour' => 150000,
                'price_extra_hour' => 45000,
                'status' => 'empty',
                'cleaning_status' => 'clean',
                'area' => 30,
                'description' => 'Phòng Studio Balcony thoáng đãng. Đã dọn sạch sẽ 100%, sẵn sàng đón khách nhận phòng ngay!',
                'amenities' => ['wifi', 'tv', 'ac', 'fridge', 'minibar', 'balcony'],
            ],
            [
                'room_number' => 'KS-301',
                'floor' => 3,
                'room_type' => 'vip',
                'rental_type' => 'day',
                'price' => 16000000,
                'price_per_day' => 880000,
                'price_per_hour' => 220000,
                'price_extra_hour' => 60000,
                'status' => 'empty',
                'cleaning_status' => 'clean',
                'area' => 50,
                'description' => 'Căn Penthouse Suite sang trọng, tầm nhìn toàn cảnh Hà Nội, quầy bar riêng và máy pha cafe Ý.',
                'amenities' => ['wifi', 'tv', 'ac', 'fridge', 'minibar', 'balcony', 'smart_lock'],
            ],
        ];

        foreach ($hotelRoomsData as $data) {
            $room = Room::updateOrCreate(
                ['tenant_id' => $tenantId, 'building_id' => $hotel->id, 'room_number' => $data['room_number']],
                $data
            );

            // Tạo Booking mẫu cho KS-201 (Đang ở theo ngày)
            if ($data['room_number'] === 'KS-201') {
                $b1 = HotelBooking::updateOrCreate(
                    ['booking_code' => 'HB-RENTY01'],
                    [
                        'tenant_id' => $tenantId,
                        'room_id' => $room->id,
                        'guest_name' => 'Đặng Quốc Tuấn',
                        'guest_phone' => '0912345678',
                        'guest_cccd' => '001095012345',
                        'rental_type' => 'day',
                        'check_in_at' => Carbon::now()->subHours(20),
                        'expected_check_out_at' => Carbon::now()->addHours(4),
                        'unit_rate' => 650000,
                        'deposit_amount' => 200000,
                        'status' => 'checked_in',
                        'payment_status' => 'unpaid',
                        'note' => 'Khách yêu cầu thêm 1 gối lông vũ mềm.',
                    ]
                );

                // Minibar tiêu thụ mẫu
                HotelFolioItem::updateOrCreate(
                    ['booking_id' => $b1->id, 'item_name' => 'Bia Heineken lon 330ml'],
                    ['item_type' => 'minibar', 'quantity' => 2, 'unit_price' => 35000, 'subtotal' => 70000]
                );
                HotelFolioItem::updateOrCreate(
                    ['booking_id' => $b1->id, 'item_name' => 'Nước khoáng Lavie 500ml'],
                    ['item_type' => 'minibar', 'quantity' => 2, 'unit_price' => 15000, 'subtotal' => 30000]
                );
                HotelFolioItem::updateOrCreate(
                    ['booking_id' => $b1->id, 'item_name' => 'Snack khoai tây Pringles'],
                    ['item_type' => 'minibar', 'quantity' => 1, 'unit_price' => 45000, 'subtotal' => 45000]
                );
            }

            // Tạo Booking mẫu cho KS-202 (Đang ở theo giờ)
            if ($data['room_number'] === 'KS-202') {
                $b2 = HotelBooking::updateOrCreate(
                    ['booking_code' => 'HB-RENTY02'],
                    [
                        'tenant_id' => $tenantId,
                        'room_id' => $room->id,
                        'guest_name' => 'Lê Hoàng Long',
                        'guest_phone' => '0933888999',
                        'guest_cccd' => '001098054321',
                        'rental_type' => 'hour',
                        'check_in_at' => Carbon::now()->subMinutes(110), // ở được gần 2 tiếng
                        'unit_rate' => 140000,
                        'deposit_amount' => 50000,
                        'status' => 'checked_in',
                        'payment_status' => 'unpaid',
                    ]
                );

                HotelFolioItem::updateOrCreate(
                    ['booking_id' => $b2->id, 'item_name' => 'Nước tăng lực RedBull'],
                    ['item_type' => 'minibar', 'quantity' => 2, 'unit_price' => 25000, 'subtotal' => 50000]
                );
            }

            // Tạo Booking mẫu đã trả phòng (Checked-out) cho KS-203 để xem lịch sử Folio
            if ($data['room_number'] === 'KS-203') {
                $b3 = HotelBooking::updateOrCreate(
                    ['booking_code' => 'HB-RENTY03'],
                    [
                        'tenant_id' => $tenantId,
                        'room_id' => $room->id,
                        'guest_name' => 'Trần Thị Mai Phương',
                        'guest_phone' => '0988777666',
                        'rental_type' => 'day',
                        'check_in_at' => Carbon::now()->subDays(2),
                        'actual_check_out_at' => Carbon::now()->subHours(4),
                        'unit_rate' => 380000,
                        'room_amount' => 760000, // 2 ngày
                        'service_amount' => 50000,
                        'total_amount' => 810000,
                        'deposit_amount' => 200000,
                        'status' => 'checked_out',
                        'payment_status' => 'paid',
                        'payment_method' => 'vietqr',
                    ]
                );

                HotelFolioItem::updateOrCreate(
                    ['booking_id' => $b3->id, 'item_name' => 'Nước ngọt Coca-Cola'],
                    ['item_type' => 'minibar', 'quantity' => 2, 'unit_price' => 20000, 'subtotal' => 40000]
                );
                HotelFolioItem::updateOrCreate(
                    ['booking_id' => $b3->id, 'item_name' => 'Khăn ướt lạnh cao cấp'],
                    ['item_type' => 'service', 'quantity' => 2, 'unit_price' => 5000, 'subtotal' => 10000]
                );
            }
        }

        // =========================================================================
        // MÔ HÌNH 2: CHUNG CƯ MINI / CĂN HỘ DỊCH VỤ (property_type = 'apartment')
        // =========================================================================
        $condo = Building::updateOrCreate(
            ['tenant_id' => $tenantId, 'name' => 'Chung Cư Mini Renty Skyview Residence'],
            [
                'address' => 'Số 12 Ngõ 86 Duy Tân, P. Dịch Vọng Hậu, Q. Cầu Giấy, Hà Nội',
                'property_type' => 'apartment',
                'description' => 'Tòa căn hộ chung cư mini cao cấp 7 tầng, có thang máy, khóa vân tay thẻ từ, hầm xe và bảo vệ 24/7.',
            ]
        );

        $condoRoomsData = [
            [
                'room_number' => 'CH-401',
                'floor' => 4,
                'room_type' => 'vip',
                'rental_type' => 'month',
                'price' => 6500000,
                'status' => 'occupied',
                'cleaning_status' => 'clean',
                'area' => 45,
                'description' => 'Căn hộ 2 phòng ngủ 1 phòng khách, bếp riêng biệt, ban công phơi đồ thoáng mát.',
                'amenities' => ['wifi', 'tv', 'ac', 'fridge', 'water_heater', 'kitchen', 'balcony', 'elevator', 'smart_lock'],
            ],
            [
                'room_number' => 'CH-402',
                'floor' => 4,
                'room_type' => 'deluxe',
                'rental_type' => 'month',
                'price' => 4800000,
                'status' => 'occupied',
                'cleaning_status' => 'clean',
                'area' => 32,
                'description' => 'Căn hộ 1 phòng ngủ Studio full nội thất hiện đại, sofa giường, bàn làm việc.',
                'amenities' => ['wifi', 'ac', 'fridge', 'kitchen', 'elevator', 'smart_lock'],
            ],
            [
                'room_number' => 'CH-403',
                'floor' => 4,
                'room_type' => 'normal',
                'rental_type' => 'month',
                'price' => 4200000,
                'status' => 'empty',
                'cleaning_status' => 'clean',
                'area' => 28,
                'description' => 'Căn hộ Studio có gác lửng đúc cao cấp, vệ sinh khép kín, đang còn trống sẵn sàng vào ở.',
                'amenities' => ['wifi', 'ac', 'water_heater', 'loft', 'elevator'],
            ],
        ];

        foreach ($condoRoomsData as $data) {
            $room = Room::updateOrCreate(
                ['tenant_id' => $tenantId, 'building_id' => $condo->id, 'room_number' => $data['room_number']],
                $data
            );

            // Gán Cư dân thuê dài hạn và Hợp đồng cho CH-401
            if ($data['room_number'] === 'CH-401') {
                $resident = Resident::updateOrCreate(
                    ['room_id' => $room->id],
                    [
                        'tenant_id' => $tenantId,
                        'name' => 'Nguyễn Minh Quân',
                        'phone' => '0977112233',
                        'cccd' => '001094008899',
                        'dob' => '1994-05-18',
                        'hometown' => 'Nam Định',
                        'start_date' => Carbon::now()->subMonths(3)->toDateString(),
                        'status' => 'active',
                    ]
                );

                Contract::updateOrCreate(
                    ['room_id' => $room->id, 'resident_id' => $resident->id],
                    [
                        'tenant_id' => $tenantId,
                        'contract_code' => 'HD-CH401-2026',
                        'start_date' => Carbon::now()->subMonths(3)->toDateString(),
                        'end_date' => Carbon::now()->addMonths(9)->toDateString(),
                        'deposit' => 6500000,
                        'terms' => 'Hợp đồng thuê căn hộ chung cư mini dài hạn 12 tháng theo quy định pháp luật.',
                        'status' => 'active',
                        'is_signed' => true,
                        'signed_at' => Carbon::now()->subMonths(3),
                    ]
                );

                // Bản ghi điện nước tháng hiện tại
                UtilityRecord::updateOrCreate(
                    ['room_id' => $room->id, 'billing_month' => Carbon::now()->format('Y-m')],
                    [
                        'tenant_id' => $tenantId,
                        'old_electricity' => 1250,
                        'new_electricity' => 1410, // 160 số
                        'electricity_price' => 3800,
                        'old_water' => 85,
                        'new_water' => 97, // 12 khối
                        'water_price' => 28000,
                        'status' => 'sent',
                    ]
                );
            }
        }

        // =========================================================================
        // MÔ HÌNH 3: NHÀ TRỌ TRUYỀN THỐNG (property_type = 'boarding')
        // =========================================================================
        $boarding = Building::updateOrCreate(
            ['tenant_id' => $tenantId, 'name' => 'Dãy Nhà Trọ Sinh Viên Renty Xanh'],
            [
                'address' => 'Số 25 Ngõ 199 Hồ Tùng Mậu, P. Mai Dịch, Q. Cầu Giấy, Hà Nội',
                'property_type' => 'boarding',
                'description' => 'Dãy nhà trọ truyền thống giá sinh viên, an ninh tốt, gần các trường đại học lớn.',
            ]
        );

        $boardingRooms = [
            [
                'room_number' => 'TR-101',
                'floor' => 1,
                'room_type' => 'normal',
                'rental_type' => 'month',
                'price' => 2600000,
                'status' => 'occupied',
                'cleaning_status' => 'clean',
                'area' => 18,
                'description' => 'Phòng trọ có gác lửng, vệ sinh khép kín, đồng hồ điện nước riêng.',
                'amenities' => ['wifi', 'loft', 'water_heater'],
            ],
            [
                'room_number' => 'TR-102',
                'floor' => 1,
                'room_type' => 'normal',
                'rental_type' => 'month',
                'price' => 2600000,
                'status' => 'empty',
                'cleaning_status' => 'clean',
                'area' => 18,
                'description' => 'Phòng trọ tầng 1 thoáng mát, giờ giấc tự do không chung chủ.',
                'amenities' => ['wifi', 'loft'],
            ],
            [
                'room_number' => 'TR-103',
                'floor' => 1,
                'room_type' => 'normal',
                'rental_type' => 'month',
                'price' => 3200000,
                'status' => 'overdue',
                'cleaning_status' => 'clean',
                'area' => 22,
                'description' => 'Phòng trọ rộng có thêm điều hòa. (Đang nợ tiền điện nước tháng này).',
                'amenities' => ['wifi', 'ac', 'loft', 'water_heater'],
            ],
        ];

        foreach ($boardingRooms as $bData) {
            Room::updateOrCreate(
                ['tenant_id' => $tenantId, 'building_id' => $boarding->id, 'room_number' => $bData['room_number']],
                $bData
            );
        }
    }
}
