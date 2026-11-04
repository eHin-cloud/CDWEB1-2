<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\LandlordProfile;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Vietnam34ProvincesRoomsSeeder extends Seeder
{
    /**
     * Seeder khởi tạo 34 Tỉnh Thành tại Việt Nam.
     * Trong đó, 4 Đô thị lớn nhất:
     *  - TP. Hồ Chí Minh: 120 phòng
     *  - Đà Nẵng: 100 phòng
     *  - Hải Phòng: 80 phòng
     *  - Cần Thơ: 80 phòng
     * 30 tỉnh thành còn lại: 50 phòng / tỉnh.
     * Dữ liệu đầy đủ hình ảnh, tiện ích, khoảng giá thực tế và toạ độ/địa chỉ chính xác tại từng địa phương.
     */
    public function run(): void
    {
        $this->command?->info("==========================================================================");
        $this->command?->info("   KHỞI TẠO DỮ LIỆU PHÒNG TRỌ 34 TỈNH THÀNH VIỆT NAM (PHÂN TÁN TIÊU CHUẨN 6 PHÒNG/NƠI)");
        $this->command?->info("   Phân bố rải rác đều 34 tỉnh thành cả nước, tối ưu mượt mà cho Bản đồ Renty");
        $this->command?->info("==========================================================================");

        // 1. Chuẩn bị Tenant & Tài khoản Chủ trọ Toàn quốc
        $tenant = Tenant::updateOrCreate(
            ['email' => 'contact@renty-vietnam.vn'],
            [
                'name' => 'Hệ thống Chuỗi Phòng trọ Toàn quốc Renty Vietnam',
                'phone' => '0901888999',
                'bank_name' => 'Vietcombank',
                'bank_account_no' => '999934343434',
                'bank_account_name' => 'RENTY VIETNAM NETWORK',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 99,
                'onboarding_step' => 4,
            ]
        );

        // Dọn dẹp phòng cũ của tenant này để phân tán lại chuẩn 5-6 phòng/nơi, tránh dồn cục gây lag map
        DB::table('rooms')->where('tenant_id', $tenant->id)->delete();

        $landlordRole = Role::firstOrCreate(['slug' => 'landlord'], ['name' => 'Chủ trọ / Quản lý']);

        $landlord = User::updateOrCreate(
            ['username' => 'chutrovietnam'],
            [
                'tenant_id' => $tenant->id,
                'role_id' => $landlordRole->id,
                'name' => 'Nguyễn Quốc Việt (Quản lý Chuỗi Renty 34 Tỉnh Thành)',
                'phone' => '0901888999',
                'email' => 'chutro.vietnam@smartroom.local',
                'password' => Hash::make('123456'),
                'role' => 'landlord',
                'like' => 'Nguyễn Quốc Việt',
            ]
        );

        LandlordProfile::updateOrCreate(
            ['user_id' => $landlord->id],
            [
                'tenant_id' => $tenant->id,
                'full_name' => $landlord->name,
                'phone' => $landlord->phone,
                'property_name' => $tenant->name,
                'property_address' => 'Trụ sở Mạng lưới Căn hộ Dịch vụ & Phòng trọ Renty 34 Tỉnh Thành Toàn Quốc',
                'national_id' => '001095034567',
                'business_license' => '0109343434',
                'status' => 'verified',
                'verification_status' => 'kyc_verified',
            ]
        );

        // 2. Danh mục 34 Tỉnh Thành tiêu biểu trải dài 3 miền Bắc - Trung - Nam
        $provincesData = [
            // MIỀN BẮC
            [
                'code' => 'HN',
                'province' => 'Hà Nội',
                'building_name' => 'Renty Complex Cầu Giấy',
                'address' => 'Số 68 Đường Cầu Giấy, Phường Quan Hoa, Quận Cầu Giấy, Hà Nội',
                'description' => 'Tòa nhà căn hộ mini dịch vụ cao cấp ngay trung tâm Cầu Giấy, gần ĐH Sư Phạm, ĐH Quốc Gia và ga Metro Nhổn.',
                'base_price' => 3800000,
            ],
            [
                'code' => 'HP',
                'province' => 'Hải Phòng',
                'building_name' => 'Renty Cảng Biển Lạch Tray',
                'address' => 'Số 45 Đường Lạch Tray, Phường Lạch Tray, Quận Ngô Quyền, Hải Phòng',
                'description' => 'Chung cư mini hiện đại gần ĐH Hàng Hải, sân vận động Lạch Tray và trung tâm thành phố Hoa Phượng Đỏ.',
                'base_price' => 3200000,
                'target_rooms' => 80,
            ],
            [
                'code' => 'QN',
                'province' => 'Quảng Ninh',
                'building_name' => 'Renty Vịnh Hạ Long Paradise',
                'address' => 'Số 18 Đường Hạ Long, Phường Bãi Cháy, TP. Hạ Long, Quảng Ninh',
                'description' => 'Khu căn hộ studio view vịnh Hạ Long, gần Sun World Bãi Cháy và bãi tắm quốc tế.',
                'base_price' => 3500000,
            ],
            [
                'code' => 'BN',
                'province' => 'Bắc Ninh',
                'building_name' => 'Renty Kinh Bắc Smart Home',
                'address' => 'Số 56 Đường Lý Thái Tổ, Phường Ninh Xá, TP. Bắc Ninh, Bắc Ninh',
                'description' => 'Tòa nhà chuyên phục vụ chuyên gia và kỹ sư gần các KCN Samsung, Quế Võ và trung tâm Bắc Ninh.',
                'base_price' => 3000000,
            ],
            [
                'code' => 'TN',
                'province' => 'Thái Nguyên',
                'building_name' => 'Renty Đại Học Thái Nguyên',
                'address' => 'Số 42 Đường Lương Ngọc Quyến, Phường Quang Trung, TP. Thái Nguyên, Thái Nguyên',
                'description' => 'Cụm phòng trọ sinh viên tiện nghi cao cấp, liền kề ĐH Y Dược và ĐH Sư Phạm Thái Nguyên.',
                'base_price' => 2000000,
            ],
            [
                'code' => 'HD',
                'province' => 'Hải Dương',
                'building_name' => 'Renty Thành Đông Residence',
                'address' => 'Số 78 Đường Bạch Đằng, Phường Trần Phú, TP. Hải Dương, Hải Dương',
                'description' => 'Khu studio khép kín view sông Bạch Đằng, yên tĩnh, an ninh bảo vệ 24/7.',
                'base_price' => 2400000,
            ],
            [
                'code' => 'ND',
                'province' => 'Nam Định',
                'building_name' => 'Renty Thành Nam Garden',
                'address' => 'Số 34 Đường Trần Hưng Đạo, Phường Lộc Vượng, TP. Nam Định, Nam Định',
                'description' => 'Khu nhà trọ xanh mát, đầy đủ nội thất, gần ĐH Điều Dưỡng và ĐH Sư Phạm Kỹ Thuật Nam Định.',
                'base_price' => 2100000,
            ],
            [
                'code' => 'VP',
                'province' => 'Vĩnh Phúc',
                'building_name' => 'Renty Vĩnh Yên Central House',
                'address' => 'Số 28 Đường Mê Linh, Phường Liên Bảo, TP. Vĩnh Yên, Vĩnh Phúc',
                'description' => 'Tòa nhà căn hộ dịch vụ gần quảng trường Vĩnh Yên, thuận tiện đi lại các KCN Khai Quang, Bình Xuyên.',
                'base_price' => 2600000,
            ],
            [
                'code' => 'HY',
                'province' => 'Hưng Yên',
                'building_name' => 'Renty Phố Hiến Riverside',
                'address' => 'Số 62 Đường Chùa Chuông, Phường Hiến Nam, TP. Hưng Yên, Hưng Yên',
                'description' => 'Phòng trọ cao cấp gần hồ Bán Nguyệt, ĐH Chu Văn An, môi trường văn minh sạch đẹp.',
                'base_price' => 2200000,
            ],
            [
                'code' => 'PT',
                'province' => 'Phú Thọ',
                'building_name' => 'Renty Đất Tổ Việt Trì',
                'address' => 'Số 65 Đường Hùng Vương, Phường Tiên Cát, TP. Việt Trì, Phú Thọ',
                'description' => 'Phòng trọ studio tiện nghi trên trục đại lộ Hùng Vương, gần ĐH Hùng Vương và công viên Văn Lang.',
                'base_price' => 2100000,
            ],
            [
                'code' => 'TB',
                'province' => 'Thái Bình',
                'building_name' => 'Renty Quê Lúa Thái Bình',
                'address' => 'Số 39 Đường Lê Lợi, Phường Đề Thám, TP. Thái Bình, Thái Bình',
                'description' => 'Căn hộ dịch vụ tiện nghi gần ĐH Y Dược Thái Bình và bệnh viện Đa khoa tỉnh.',
                'base_price' => 2300000,
            ],
            [
                'code' => 'NB',
                'province' => 'Ninh Bình',
                'building_name' => 'Renty Cố Đô Tràng An',
                'address' => 'Số 50 Đường Trần Hưng Đạo, Phường Đông Thành, TP. Ninh Bình, Ninh Bình',
                'description' => 'Căn hộ khép kín ngay trung tâm Ninh Bình, thuận tiện đi lại danh thắng Tràng An và Bái Đính.',
                'base_price' => 2400000,
            ],

            // BẮC TRUNG BỘ & DUYÊN HẢI MIỀN TRUNG
            [
                'code' => 'TH',
                'province' => 'Thanh Hóa',
                'building_name' => 'Renty Lam Sơn Plaza',
                'address' => 'Số 89 Đường Lê Hoàn, Phường Lam Sơn, TP. Thanh Hóa, Thanh Hóa',
                'description' => 'Căn hộ mini cao cấp gần Vincom Thanh Hóa, ĐH Hồng Đức và trục đại lộ Lê Lợi.',
                'base_price' => 2500000,
            ],
            [
                'code' => 'NA',
                'province' => 'Nghệ An',
                'building_name' => 'Renty Thành Vinh Center',
                'address' => 'Số 102 Đường Nguyễn Thị Minh Khai, Phường Hưng Bình, TP. Vinh, Nghệ An',
                'description' => 'Căn hộ dịch vụ hiện đại gần ĐH Vinh, quảng trường Hồ Chí Minh và chợ Vinh.',
                'base_price' => 2400000,
            ],
            [
                'code' => 'HUE',
                'province' => 'Thừa Thiên Huế',
                'building_name' => 'Renty Sông Hương Cố Đô',
                'address' => 'Số 25 Đường Lê Lợi, Phường Vĩnh Ninh, TP. Huế, Thừa Thiên Huế',
                'description' => 'Căn hộ view sông Hương thơ mộng, gần ĐH Sư Phạm Huế, ĐH Y Dược và cầu Tràng Tiền.',
                'base_price' => 2500000,
            ],
            [
                'code' => 'DN',
                'province' => 'Đà Nẵng',
                'building_name' => 'Renty Biển Mỹ Khê Đà Nẵng',
                'address' => 'Số 120 Đường Nguyễn Văn Thoại, Phường Mỹ An, Quận Ngũ Hành Sơn, Đà Nẵng',
                'description' => 'Tòa nhà căn hộ cao cấp cách bãi biển Mỹ Khê 300m, gần ĐH Kinh Tế Đà Nẵng, khu phố Tây An Thượng.',
                'base_price' => 3800000,
                'target_rooms' => 100,
            ],
            [
                'code' => 'QNM',
                'province' => 'Quảng Nam',
                'building_name' => 'Renty Phố Cổ Hội An',
                'address' => 'Số 82 Đường Cửa Đại, Phường Cẩm Châu, TP. Hội An, Quảng Nam',
                'description' => 'Căn hộ phong cách Đông Dương thoáng mát, chỉ 5 phút đạp xe vào phố cổ Hội An và biển An Bàng.',
                'base_price' => 3200000,
            ],
            [
                'code' => 'BDH',
                'province' => 'Bình Định',
                'building_name' => 'Renty Biển Quy Nhơn Sun',
                'address' => 'Số 108 Đường An Dương Vương, Phường Nguyễn Văn Cừ, TP. Quy Nhơn, Bình Định',
                'description' => 'Phòng trọ studio sát biển Quy Nhơn, ngay cạnh ĐH Quy Nhơn và bãi biển Xuân Diệu.',
                'base_price' => 2600000,
            ],
            [
                'code' => 'NT',
                'province' => 'Khánh Hòa',
                'building_name' => 'Renty Nha Trang Ocean View',
                'address' => 'Số 72 Đường Trần Phú, Phường Lộc Thọ, TP. Nha Trang, Khánh Hòa',
                'description' => 'Khu căn hộ studio view biển Nha Trang, gần ĐH Nha Trang, chợ Đêm và tháp Trầm Hương.',
                'base_price' => 3500000,
            ],
            [
                'code' => 'BTH',
                'province' => 'Bình Thuận',
                'building_name' => 'Renty Mũi Né Phan Thiết',
                'address' => 'Số 124 Đường Nguyễn Đình Chiểu, Phường Hàm Tiến, TP. Phan Thiết, Bình Thuận',
                'description' => 'Căn hộ nghỉ dưỡng kết hợp cho thuê dài hạn, view rặng dừa Hàm Tiến Mũi Né.',
                'base_price' => 2800000,
            ],

            // TÂY NGUYÊN
            [
                'code' => 'DL',
                'province' => 'Lâm Đồng',
                'building_name' => 'Renty Sương Mù Đà Lạt',
                'address' => 'Số 35 Đường Bùi Thị Xuân, Phường 2, TP. Đà Lạt, Lâm Đồng',
                'description' => 'Căn hộ ấm cúng view đồi thông, cách hồ Xuân Hương và chợ Đà Lạt chỉ 500m.',
                'base_price' => 3200000,
            ],
            [
                'code' => 'DLK',
                'province' => 'Đắk Lắk',
                'building_name' => 'Renty Thủ Phủ Cà Phê Ban Mê',
                'address' => 'Số 54 Đường Lê Duẩn, Phường Tân Thành, TP. Buôn Ma Thuột, Đắk Lắk',
                'description' => 'Phòng trọ cao cấp trung tâm Buôn Ma Thuột, gần ĐH Tây Nguyên và bảo tàng Cà Phê.',
                'base_price' => 2200000,
            ],
            [
                'code' => 'GL',
                'province' => 'Gia Lai',
                'building_name' => 'Renty Phố Núi Pleiku',
                'address' => 'Số 22 Đường Hùng Vương, Phường Diên Hồng, TP. Pleiku, Gia Lai',
                'description' => 'Căn hộ mini an ninh, gần Biển Hồ T’Nưng và quảng trường Đại Đoàn Kết.',
                'base_price' => 2100000,
            ],

            // ĐÔNG NAM BỘ
            [
                'code' => 'HCM',
                'province' => 'TP. Hồ Chí Minh',
                'building_name' => 'Renty Landmark Sài Gòn Sky',
                'address' => 'Số 208 Đường Nguyễn Hữu Cảnh, Phường 22, Quận Bình Thạnh, TP. Hồ Chí Minh',
                'description' => 'Chung cư mini cao cấp liền kề Landmark 81, ga Metro Tân Cảng, ĐH HUTECH, ĐH Giao Thông Vận Tải.',
                'base_price' => 4500000,
                'target_rooms' => 120,
            ],
            [
                'code' => 'BD',
                'province' => 'Bình Dương',
                'building_name' => 'Renty Thủ Dầu Một Smart Center',
                'address' => 'Số 230 Đại Lộ Bình Dương, Phường Phú Hòa, TP. Thủ Dầu Một, Bình Dương',
                'description' => 'Căn hộ dịch vụ tiện nghi gần ĐH Bình Dương, ĐH Thủ Dầu Một và KCN VSIP 1.',
                'base_price' => 3000000,
            ],
            [
                'code' => 'DNI',
                'province' => 'Đồng Nai',
                'building_name' => 'Renty Biên Hòa Metro Plaza',
                'address' => 'Số 15 Đường Đồng Khởi, Phường Tân Hiệp, TP. Biên Hòa, Đồng Nai',
                'description' => 'Khu phòng trọ khép kín hiện đại gần ĐH Đồng Nai, KCN Amata và Bệnh viện Đa khoa Đồng Nai.',
                'base_price' => 2700000,
            ],
            [
                'code' => 'VT',
                'province' => 'Bà Rịa - Vũng Tàu',
                'building_name' => 'Renty Bãi Sau Vũng Tàu Sunset',
                'address' => 'Số 52 Đường Thùy Vân, Phường Thắng Tam, TP. Vũng Tàu, Bà Rịa - Vũng Tàu',
                'description' => 'Căn hộ studio cách bãi tắm Thùy Vân 200m, gió biển thoáng mát quanh năm.',
                'base_price' => 3200000,
            ],
            [
                'code' => 'TNINH',
                'province' => 'Tây Ninh',
                'building_name' => 'Renty Núi Bà Tây Ninh',
                'address' => 'Số 73 Đường 30 Tháng 4, Phường 3, TP. Tây Ninh, Tây Ninh',
                'description' => 'Căn hộ studio view Núi Bà Đen hùng vĩ, ngay trung tâm TP. Tây Ninh.',
                'base_price' => 2100000,
            ],

            // ĐỒNG BẰNG SÔNG CỬU LONG
            [
                'code' => 'CT',
                'province' => 'Cần Thơ',
                'building_name' => 'Renty Tây Đô Riverside Ninh Kiều',
                'address' => 'Số 88 Đường 30 Tháng 4, Phường An Phú, Quận Ninh Kiều, Cần Thơ',
                'description' => 'Tòa nhà căn hộ cao cấp cạnh bến Ninh Kiều, gần ĐH Cần Thơ, ĐH Y Dược Cần Thơ.',
                'base_price' => 2800000,
                'target_rooms' => 80,
            ],
            [
                'code' => 'LA',
                'province' => 'Long An',
                'building_name' => 'Renty Tân An Vàm Cỏ Green',
                'address' => 'Số 45 Đường Hùng Vương, Phường 2, TP. Tân An, Long An',
                'description' => 'Phòng trọ tiện nghi liền kề cửa ngõ TP.HCM, thuận tiện di chuyển các KCN Long An.',
                'base_price' => 2300000,
            ],
            [
                'code' => 'TG',
                'province' => 'Tiền Giang',
                'building_name' => 'Renty Mỹ Tho Sông Tiền',
                'address' => 'Số 48 Đường Ấp Bắc, Phường 5, TP. Mỹ Tho, Tiền Giang',
                'description' => 'Căn hộ mini thoáng mát ngay trung tâm TP. Mỹ Tho, gần ĐH Tiền Giang.',
                'base_price' => 2200000,
            ],
            [
                'code' => 'AG',
                'province' => 'An Giang',
                'building_name' => 'Renty Long Xuyên Diamond',
                'address' => 'Số 15 Đường Trần Hưng Đạo, Phường Mỹ Bình, TP. Long Xuyên, An Giang',
                'description' => 'Phòng trọ cao cấp gần ĐH An Giang và TTTM Vincom Plaza Long Xuyên.',
                'base_price' => 2300000,
            ],
            [
                'code' => 'BT',
                'province' => 'Bến Tre',
                'building_name' => 'Renty Xứ Dừa Bến Tre',
                'address' => 'Số 31 Đường Đồng Khởi, Phường Phú Khương, TP. Bến Tre, Bến Tre',
                'description' => 'Khu phòng trọ khép kín yên tĩnh, trong lành gần bảo tàng Bến Tre và công viên Đồng Khởi.',
                'base_price' => 2000000,
            ],
            [
                'code' => 'KG',
                'province' => 'Kiên Giang',
                'building_name' => 'Renty Phú Quốc Pearl Island',
                'address' => 'Số 91 Đường Trần Hưng Đạo, Phường Dương Đông, TP. Phú Quốc, Kiên Giang',
                'description' => 'Căn hộ studio cao cấp tại trung tâm Dương Đông, Phú Quốc, đi bộ 3 phút ra biển.',
                'base_price' => 3800000,
            ],
        ];

        // 3. Danh sách ảnh nội thất phòng trọ thực tế từ Unsplash
        $imageSets = [
            [
                'main' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1554995207-c18c203602cb?auto=format&fit=crop&w=1200&q=80',
                ],
            ],
            [
                'main' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1560185127-6ed189bf02f4?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80',
                ],
            ],
            [
                'main' => 'https://images.unsplash.com/photo-1502005229762-ee1b2b814a0f?auto=format&fit=crop&w=1200&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1552321554-5fefe8c9ef14?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1540518614846-7ede433c4ef3?auto=format&fit=crop&w=1200&q=80',
                ],
            ],
            [
                'main' => 'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=1200&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1200&q=80',
                ],
            ],
        ];

        $now = now();
        $totalCreatedRooms = 0;
        $totalCreatedBuildings = 0;

        foreach ($provincesData as $index => $item) {
            // Tạo toà nhà cho từng tỉnh thành
            $building = Building::updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'name' => $item['building_name'],
                ],
                [
                    'address' => $item['address'],
                    'description' => $item['description'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
            $totalCreatedBuildings++;

            // Tiêu chuẩn 6 phòng/nơi để phân tán đẹp và tối ưu hiệu năng bản đồ không bị lag
            $targetRooms = 6;

            // Kiểm tra số lượng phòng hiện tại của toà nhà để đảm bảo đủ số lượng mục tiêu
            $existingRoomCount = DB::table('rooms')->where('building_id', $building->id)->count();
            if ($existingRoomCount >= $targetRooms) {
                $this->command?->line(sprintf("  [%02d/34] %s: Đã có đủ %d/%d phòng, bỏ qua.", $index + 1, $item['province'], $existingRoomCount, $targetRooms));
                continue;
            }

            $roomsToCreate = [];
            // Sinh phòng theo số lượng mục tiêu: mỗi tầng 10 phòng (101 - 110, 201 - 210, ...)
            $totalFloors = (int) ceil($targetRooms / 10);
            for ($floor = 1; $floor <= $totalFloors; $floor++) {
                for ($roomIdx = 1; $roomIdx <= 10; $roomIdx++) {
                    if ($existingRoomCount + count($roomsToCreate) >= $targetRooms) {
                        break 2;
                    }

                    $roomNumber = sprintf("%d%02d", $floor, $roomIdx);

                    // Kiểm tra nếu phòng này đã tồn tại trong toà nhà thì bỏ qua
                    $roomExists = DB::table('rooms')
                        ->where('building_id', $building->id)
                        ->where('room_number', $roomNumber)
                        ->exists();

                    if ($roomExists) {
                        continue;
                    }

                    // Phân loại phòng: studio (30%), vip (20%), deluxe (20%), standard (30%)
                    if ($roomIdx <= 3) {
                        $roomType = 'studio';
                        $area = rand(32, 42);
                        $priceMultiplier = 1.25;
                    } elseif ($roomIdx <= 5) {
                        $roomType = 'vip';
                        $area = rand(28, 36);
                        $priceMultiplier = 1.15;
                    } elseif ($roomIdx <= 7) {
                        $roomType = 'deluxe';
                        $area = rand(25, 30);
                        $priceMultiplier = 1.05;
                    } else {
                        $roomType = 'standard';
                        $area = rand(20, 26);
                        $priceMultiplier = 0.95;
                    }

                    // Tính toán giá phòng
                    $roomPrice = (int) round(($item['base_price'] * $priceMultiplier + ($floor * 50000)) / 100000) * 100000;
                    $deposit = $roomPrice; // Cọc 1 tháng chuẩn thị trường

                    // Trạng thái: 80% phòng trống (empty) để tìm kiếm, 16% occupied, 4% maintenance
                    $statusSeed = rand(1, 100);
                    if ($statusSeed <= 80) {
                        $status = 'empty';
                        $cleaningStatus = 'clean';
                        $housekeepingStatus = 'clean';
                    } elseif ($statusSeed <= 96) {
                        $status = 'occupied';
                        $cleaningStatus = 'clean';
                        $housekeepingStatus = 'clean';
                    } else {
                        $status = 'maintenance';
                        $cleaningStatus = 'cleaning';
                        $housekeepingStatus = 'out_of_service';
                    }

                    // Tiện ích phong phú, có đầy đủ từ khoá tiếng Việt và mã tiếng Anh phục vụ Smart Search / Scout
                    $amenities = [
                        'Máy lạnh', 'Nóng lạnh', 'Wifi',
                        'ac', 'air_conditioner', 'wifi',
                        'khép kín', 'wc'
                    ];

                    if ($roomType === 'studio' || $roomType === 'vip' || rand(1, 10) > 4) {
                        $amenities[] = 'Tủ lạnh';
                        $amenities[] = 'fridge';
                        $amenities[] = 'refrigerator';
                    }
                    if ($roomType === 'studio' || rand(1, 10) > 5) {
                        $amenities[] = 'Máy giặt';
                        $amenities[] = 'washer';
                        $amenities[] = 'washing_machine';
                    }
                    if (rand(1, 10) > 5) {
                        $amenities[] = 'Gác lửng';
                        $amenities[] = 'gác lửng';
                        $amenities[] = 'loft';
                    }
                    if (rand(1, 10) > 4) {
                        $amenities[] = 'Ban công';
                        $amenities[] = 'ban công';
                        $amenities[] = 'balcony';
                    }
                    if (rand(1, 10) > 6) {
                        $amenities[] = 'Thú cưng';
                        $amenities[] = 'cho nuôi thú cưng';
                        $amenities[] = 'pets';
                        $amenities[] = 'pet';
                    }
                    if ($floor >= 2) {
                        $amenities[] = 'Thang máy';
                        $amenities[] = 'elevator';
                    }
                    if ($roomType === 'vip' || $roomType === 'studio') {
                        $amenities[] = 'Khóa vân tay';
                        $amenities[] = 'SmartLock';
                    }

                    // Bộ ảnh phòng
                    $imgSet = $imageSets[($floor + $roomIdx) % count($imageSets)];

                    // Mô tả chi tiết phục vụ tìm kiếm toàn văn
                    $roomDesc = sprintf(
                        "Phòng %s (%s, %dm²) tại %s, %s. Đầy đủ tiện ích cao cấp: %s. An ninh tuyệt đối với camera 24/7, giờ giấc tự do không chung chủ, hệ thống PCCC đạt chuẩn quốc gia.",
                        $roomNumber,
                        ucfirst($roomType),
                        $area,
                        $item['building_name'],
                        $item['province'],
                        implode(', ', array_slice($amenities, 0, 6))
                    );

                    $roomsToCreate[] = [
                        'building_id' => $building->id,
                        'tenant_id' => $tenant->id,
                        'room_number' => $roomNumber,
                        'floor' => $floor,
                        'status' => $status,
                        'cleaning_status' => $cleaningStatus,
                        'housekeeping_status' => $housekeepingStatus,
                        'assigned_staff_id' => null,
                        'priority' => 'normal',
                        'inspection_notes' => 'Phòng nghiệm thu đạt chuẩn Renty Quality Standard.',
                        'inspected_by' => $landlord->id,
                        'inspected_at' => $now->copy()->subDays(rand(1, 15)),
                        'room_type' => $roomType,
                        'rental_type' => 'month',
                        'price' => $roomPrice,
                        'deposit' => $deposit,
                        'price_per_day' => (int) round($roomPrice / 15),
                        'price_per_hour' => (int) round($roomPrice / 80),
                        'price_extra_hour' => 50000,
                        'area' => $area,
                        'electric_meter_serial' => sprintf("EM-%s-%s", $item['code'], $roomNumber),
                        'water_meter_serial' => sprintf("WM-%s-%s", $item['code'], $roomNumber),
                        'amenities' => json_encode(array_values(array_unique($amenities)), JSON_UNESCAPED_UNICODE),
                        'description' => $roomDesc,
                        'image' => $imgSet['main'],
                        'images' => json_encode($imgSet['gallery'], JSON_UNESCAPED_UNICODE),
                        'video' => null,
                        'version' => 1,
                        'rating_security' => round(rand(46, 50) / 10, 1),
                        'rating_landlord' => round(rand(47, 50) / 10, 1),
                        'rating_pricing' => round(rand(45, 49) / 10, 1),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            if (!empty($roomsToCreate)) {
                // Bulk insert theo chunk 50 phòng / lần
                foreach (array_chunk($roomsToCreate, 50) as $chunk) {
                    DB::table('rooms')->insert($chunk);
                }
                $totalCreatedRooms += count($roomsToCreate);
                $this->command?->line(sprintf("  [%02d/34] %s (%s): Đã thêm thành công %d phòng trọ.", $index + 1, $item['province'], $item['building_name'], count($roomsToCreate)));
            }
        }

        $this->command?->info("==========================================================================");
        $this->command?->info(sprintf("HOÀN TẤT SEEDER! Đã khởi tạo/đồng bộ %d toà nhà và thêm %d phòng trọ cho 34 tỉnh thành.", $totalCreatedBuildings, $totalCreatedRooms));
        $this->command?->info("Tài khoản quản lý chuỗi toàn quốc: chutrovietnam / 123456");
        $this->command?->info("==========================================================================");
    }
}
