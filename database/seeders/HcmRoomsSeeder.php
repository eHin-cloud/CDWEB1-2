<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\LandlordProfile;
use App\Models\LandlordVerificationRequest;
use App\Models\Review;
use App\Models\Role;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class HcmRoomsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo Roles nếu chưa có
        $landlordRole = Role::firstOrCreate(['slug' => 'landlord'], ['name' => 'Chủ trọ / Quản lý']);
        $managerRole = Role::firstOrCreate(['slug' => 'manager'], ['name' => 'Nhân viên quản lý']);
        $userRole = Role::firstOrCreate(['slug' => 'user'], ['name' => 'Khách tìm phòng / Người dùng']);

        // 2. Tạo Tenant đại diện chuỗi phòng trọ tại TP.HCM
        $tenant = Tenant::updateOrCreate(
            ['email' => 'contact@renty-saigon.vn'],
            [
                'name' => 'Hệ thống Căn hộ & Ký túc xá Renty Sài Gòn (TP.HCM)',
                'phone' => '0938112233',
                'bank_name' => 'Vietcombank',
                'bank_account_no' => '0071001234567',
                'bank_account_name' => 'HUYNH MINH TRI',
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
                'boost_score' => 98,
                'onboarding_step' => 4,
            ]
        );

        // 3. Tạo Chủ trọ TP.HCM (chutrohcm / 123456)
        $landlord = User::updateOrCreate(
            ['username' => 'chutrohcm'],
            [
                'tenant_id' => $tenant->id,
                'role_id' => $landlordRole->id,
                'name' => 'Huỳnh Minh Trí (Chủ chuỗi Renty TP.HCM)',
                'phone' => '0938112233',
                'email' => 'chutro.hcm@smartroom.local',
                'password' => Hash::make('123456'),
                'role' => 'landlord',
                'like' => 'Huỳnh Minh Trí',
            ]
        );

        LandlordProfile::updateOrCreate(
            ['user_id' => $landlord->id],
            [
                'tenant_id' => $tenant->id,
                'full_name' => $landlord->name,
                'phone' => $landlord->phone,
                'property_name' => $tenant->name,
                'property_address' => 'Số 475A Điện Biên Phủ, Phường 25, Quận Bình Thạnh, TP. Hồ Chí Minh',
                'status' => 'verified',
                'verification_status' => 'kyc_verified',
            ]
        );

        $adminId = User::where('role', 'admin')->value('id');
        LandlordVerificationRequest::updateOrCreate(
            ['tenant_id' => $tenant->id, 'user_id' => $landlord->id],
            [
                'type' => 'kyc',
                'cccd_number' => '079095012345',
                'admin_review_consent_given' => true,
                'admin_review_consent_at' => Carbon::now()->subDays(10),
                'admin_review_consent_ip' => '127.0.0.1',
                'status' => 'approved',
                'reviewed_by' => $adminId,
                'reviewed_at' => Carbon::now()->subDays(5),
            ]
        );

        // 4. Quản lý chi nhánh HCM (quanlyhcm / 123456)
        User::updateOrCreate(
            ['username' => 'quanlyhcm'],
            [
                'tenant_id' => $tenant->id,
                'role_id' => $managerRole->id,
                'name' => 'Trần Anh Khoa (Quản lý chi nhánh TP.HCM)',
                'phone' => '0938998877',
                'email' => 'quanly.hcm@smartroom.local',
                'password' => Hash::make('123456'),
                'role' => 'manager',
                'like' => 'Trần Anh Khoa',
            ]
        );

        // 5. Khách thuê sinh viên TP.HCM demo (khachhcm / 123456)
        User::updateOrCreate(
            ['username' => 'khachhcm'],
            [
                'tenant_id' => null,
                'role_id' => $userRole->id,
                'name' => 'Nguyễn Văn Thuận (Sinh viên HUTECH)',
                'phone' => '0912345678',
                'email' => 'khach.hcm@smartroom.local',
                'password' => Hash::make('123456'),
                'role' => 'user',
                'like' => 'Nguyễn Văn Thuận',
            ]
        );

        // 6. Danh sách 6 Tòa nhà và Phòng trọ tại các cụm trường Đại Học TP.HCM
        $buildingsData = [
            [
                'name' => 'Ký túc xá & Studio Renty HUTECH - Bình Thạnh',
                'address' => 'Số 475A Điện Biên Phủ, Phường 25, Quận Bình Thạnh, TP. Hồ Chí Minh',
                'description' => 'Cách trường ĐH HUTECH chỉ 100m, gần ĐH Ngoại Thương CS2, ĐH GTVT TP.HCM, ga Metro Văn Thánh. Đầy đủ tiện nghi cao cấp, vân tay ra vào 24/7.',
                'rooms' => [
                    [
                        'room_number' => 'BT-101',
                        'floor' => 1,
                        'status' => 'empty',
                        'room_type' => 'studio',
                        'price' => 3500000,
                        'area' => 25,
                        'amenities' => ['ban công', 'wc', 'máy lạnh', 'gác lửng', 'tủ lạnh'],
                        'description' => 'Studio ban công ngập tràn ánh sáng đối diện HUTECH, có gác lửng gỗ cao 2m, máy lạnh tiết kiệm điện.',
                        'review' => 'Phòng cực kỳ sạch đẹp, bước vài bước là sang tới cổng trường HUTECH, chủ trọ thân thiện nhiệt tình.',
                        'author' => 'Thanh Thảo (SV HUTECH)',
                        'rating' => 5
                    ],
                    [
                        'room_number' => 'BT-102',
                        'floor' => 1,
                        'status' => 'empty',
                        'room_type' => 'standard',
                        'price' => 3200000,
                        'area' => 22,
                        'amenities' => ['wc', 'gác lửng', 'máy lạnh', 'thú cưng'],
                        'description' => 'Phòng gác lửng hiện đại tại đường Điện Biên Phủ, cho nuôi thú cưng nhỏ, khu dân trí cao.',
                        'review' => 'Khu trọ yên tĩnh, an ninh tốt, có chỗ để xe rộng rãi và camera 24/24.',
                        'author' => 'Minh Tuấn (SV GTVT)',
                        'rating' => 5
                    ],
                    [
                        'room_number' => 'BT-201',
                        'floor' => 2,
                        'status' => 'occupied',
                        'room_type' => 'deluxe',
                        'price' => 4200000,
                        'area' => 28,
                        'amenities' => ['ban công', 'wc', 'máy lạnh', 'gác lửng', 'máy giặt', 'thú cưng'],
                        'description' => 'Phòng Deluxe view Landmark 81 cực đẹp, ban công rộng phơi đồ đón nắng, full combo nội thất.',
                        'review' => 'View Landmark 81 đỉnh chóp, tối ngắm pháo hoa siêu nét!',
                        'author' => 'Phương Linh (SV Ngoại Thương)',
                        'rating' => 5
                    ],
                    [
                        'room_number' => 'BT-202',
                        'floor' => 2,
                        'status' => 'empty',
                        'room_type' => 'studio',
                        'price' => 3800000,
                        'area' => 26,
                        'amenities' => ['ban công', 'wc', 'máy lạnh', 'tủ quần áo'],
                        'description' => 'Phòng studio trung tâm Bình Thạnh, ngay sát ngã tư Hàng Xanh di chuyển sang Quận 1 chỉ 5 phút.',
                        'review' => 'Đi học và đi làm thêm đều tiện đường, phòng thoáng mát không bị nóng.',
                        'author' => 'Hoàng Nam',
                        'rating' => 4
                    ],
                ]
            ],
            [
                'name' => 'Chung cư mini Renty Thủ Đức Campus - SPKT',
                'address' => 'Số 24 Võ Văn Ngân, Phường Linh Chiểu, TP. Thủ Đức, TP. Hồ Chí Minh',
                'description' => 'Cách ĐH Sư Phạm Kỹ Thuật (HCMUTE) 200m, Vincom Plaza Thủ Đức 300m, ga Metro Thủ Đức 400m. Khu sinh viên văn minh.',
                'rooms' => [
                    [
                        'room_number' => 'SPKT-101',
                        'floor' => 1,
                        'status' => 'empty',
                        'room_type' => 'standard',
                        'price' => 2600000,
                        'area' => 22,
                        'amenities' => ['wc', 'gác lửng', 'thú cưng', 'wifi'],
                        'description' => 'Phòng trọ sinh viên giá rẻ gần SPKT, gác lửng đúc chắc chắn, wc khép kín sạch sẽ.',
                        'review' => 'Giá rất vừa túi tiền sinh viên, đi bộ qua SPKT chỉ 3 phút.',
                        'author' => 'Đức Trọng (SV HCMUTE)',
                        'rating' => 5
                    ],
                    [
                        'room_number' => 'SPKT-102',
                        'floor' => 1,
                        'status' => 'empty',
                        'room_type' => 'studio',
                        'price' => 2900000,
                        'area' => 24,
                        'amenities' => ['ban công', 'wc', 'máy lạnh', 'gác lửng'],
                        'description' => 'Studio ban công thoáng mát đường Võ Văn Ngân, máy lạnh inverter mới 100%.',
                        'review' => 'Phòng mới xây nên sạch bong, không ngập nước khi mưa.',
                        'author' => 'Hải Yến (SV SPKT)',
                        'rating' => 5
                    ],
                    [
                        'room_number' => 'SPKT-201',
                        'floor' => 2,
                        'status' => 'occupied',
                        'room_type' => 'deluxe',
                        'price' => 3200000,
                        'area' => 26,
                        'amenities' => ['ban công', 'wc', 'máy lạnh', 'tủ lạnh', 'thú cưng'],
                        'description' => 'Phòng deluxe đầy đủ tiện nghi, cho nuôi pet, giờ giấc tự do không chung chủ.',
                        'review' => 'Nuôi mèo thoải mái, phòng cách âm tốt.',
                        'author' => 'Bảo Ngọc',
                        'rating' => 4
                    ],
                ]
            ],
            [
                'name' => 'Nhà trọ Renty Sinh Viên Làng Đại Học Quốc Gia',
                'address' => 'Khu phố 6, Phường Linh Trung, TP. Thủ Đức, TP. Hồ Chí Minh',
                'description' => 'Liền kề KTX Khu A & Khu B ĐHQG, ĐH Bách Khoa CS2, ĐH Khoa học Tự Nhiên, ĐH CNTT (UIT). Môi trường xanh, yên tĩnh học tập.',
                'rooms' => [
                    [
                        'room_number' => 'DHQG-101',
                        'floor' => 1,
                        'status' => 'empty',
                        'room_type' => 'standard',
                        'price' => 2200000,
                        'area' => 20,
                        'amenities' => ['wc', 'gác lửng', 'wifi'],
                        'description' => 'Phòng trọ sinh viên giá siêu rẻ trong Làng ĐH, điện nước tính theo đơn giá nhà nước.',
                        'review' => 'Gần các trường thành viên ĐHQG, đồ ăn rẻ ngập tràn.',
                        'author' => 'Quốc Huy (SV UIT)',
                        'rating' => 5
                    ],
                    [
                        'room_number' => 'DHQG-102',
                        'floor' => 1,
                        'status' => 'empty',
                        'room_type' => 'studio',
                        'price' => 2500000,
                        'area' => 24,
                        'amenities' => ['ban công', 'wc', 'gác lửng', 'thú cưng'],
                        'description' => 'Phòng trọ có ban công phơi đồ rộng, có thể ở 2-3 bạn sinh viên thoải mái.',
                        'review' => 'Chủ nhà dễ tính, an ninh có bảo vệ trực đêm.',
                        'author' => 'Khánh Linh (SV KHTN)',
                        'rating' => 5
                    ],
                    [
                        'room_number' => 'DHQG-201',
                        'floor' => 2,
                        'status' => 'empty',
                        'room_type' => 'studio',
                        'price' => 2800000,
                        'area' => 25,
                        'amenities' => ['máy lạnh', 'wc', 'gác lửng', 'tủ quần áo'],
                        'description' => 'Phòng máy lạnh mới tinh tại Linh Trung, có gác lửng cao không đụng đầu.',
                        'review' => 'Phòng rộng và thoáng, wifi kéo riêng từng phòng nên học online hay chơi game rất mượt.',
                        'author' => 'Tấn Phát (SV Bách Khoa)',
                        'rating' => 5
                    ]
                ]
            ],
            [
                'name' => 'Căn hộ Studio Renty Quận 10 - UEH Center',
                'address' => 'Số 279 Nguyễn Tri Phương, Phường 5, Quận 10, TP. Hồ Chí Minh',
                'description' => 'Ngay cạnh ĐH Kinh Tế TP.HCM (UEH cơ sở B), cách ĐH Bách Khoa CS1 chỉ 900m, trung tâm ẩm thực và cà phê sầm uất.',
                'rooms' => [
                    [
                        'room_number' => 'Q10-101',
                        'floor' => 1,
                        'status' => 'empty',
                        'room_type' => 'studio',
                        'price' => 4200000,
                        'area' => 26,
                        'amenities' => ['máy lạnh', 'wc', 'ban công', 'kệ bếp', 'tủ lạnh'],
                        'description' => 'Căn hộ mini Quận 10 đầy đủ nội thất, cách cổng trường UEH đúng 2 phút đi bộ.',
                        'review' => 'Vị trí đắc địa nhất khu Quận 10, đi bộ sang trường UEH tiện vô cùng!',
                        'author' => 'Thảo Vy (SV UEH)',
                        'rating' => 5
                    ],
                    [
                        'room_number' => 'Q10-102',
                        'floor' => 1,
                        'status' => 'empty',
                        'room_type' => 'deluxe',
                        'price' => 4800000,
                        'area' => 30,
                        'amenities' => ['ban công', 'wc', 'máy lạnh', 'gác lửng', 'máy giặt', 'thú cưng'],
                        'description' => 'Phòng Deluxe góc 2 mặt thoáng có ban công lớn, bố trí bếp âm hiện đại.',
                        'review' => 'Phòng đẹp y như hình chụp, ban công lộng gió ngắm đường Nguyễn Tri Phương.',
                        'author' => 'Minh Khang',
                        'rating' => 5
                    ],
                    [
                        'room_number' => 'Q10-201',
                        'floor' => 2,
                        'status' => 'occupied',
                        'room_type' => 'studio',
                        'price' => 4500000,
                        'area' => 28,
                        'amenities' => ['máy lạnh', 'wc', 'tủ quần áo', 'bàn làm việc'],
                        'description' => 'Studio trung tâm Quận 10 dành cho sinh viên và nhân viên văn phòng.',
                        'review' => 'Phòng cách âm tốt, hàng xóm văn minh lịch sự.',
                        'author' => 'Thanh Trúc',
                        'rating' => 4
                    ],
                ]
            ],
            [
                'name' => 'Renty Boutique Residence Quận 7 - RMIT & TDTU',
                'address' => 'Số 702 Nguyễn Văn Linh, Phường Tân Phong, Quận 7, TP. Hồ Chí Minh',
                'description' => 'Cách cổng trường ĐH RMIT 350m, ĐH Tôn Đức Thắng (TDTU) 700m, đối diện TTTM SC VivoCity. Đẳng cấp căn hộ dịch vụ cao cấp.',
                'rooms' => [
                    [
                        'room_number' => 'Q7-101',
                        'floor' => 1,
                        'status' => 'empty',
                        'room_type' => 'deluxe',
                        'price' => 5200000,
                        'area' => 32,
                        'amenities' => ['ban công', 'wc', 'máy lạnh', 'smart tv', 'tủ lạnh', 'thú cưng'],
                        'description' => 'Căn hộ dịch vụ cao cấp cạnh RMIT, thiết kế Scandinavian hiện đại, thẻ từ thang máy.',
                        'review' => 'Dịch vụ dọn vệ sinh hàng tuần, đi bộ sang RMIT cực kỳ an toàn và thuận tiện.',
                        'author' => 'Jonathan Lee (SV RMIT)',
                        'rating' => 5
                    ],
                    [
                        'room_number' => 'Q7-102',
                        'floor' => 1,
                        'status' => 'empty',
                        'room_type' => 'studio',
                        'price' => 4800000,
                        'area' => 28,
                        'amenities' => ['ban công', 'wc', 'máy lạnh', 'gác lửng', 'máy giặt'],
                        'description' => 'Studio ban công ngắm khu đô thị Phú Mỹ Hưng, nội thất cao cấp chuẩn resort.',
                        'review' => 'Phòng ở sướng nhất từ trước đến nay, quản lý hỗ trợ cực nhanh.',
                        'author' => 'Hoàng Oanh (SV Tôn Đức Thắng)',
                        'rating' => 5
                    ],
                    [
                        'room_number' => 'Q7-201',
                        'floor' => 2,
                        'status' => 'occupied',
                        'room_type' => 'deluxe',
                        'price' => 5800000,
                        'area' => 35,
                        'amenities' => ['ban công', 'wc', 'máy lạnh', 'bếp từ', 'lò vi sóng', 'thú cưng'],
                        'description' => 'Căn hộ 1 phòng ngủ khép kín sang trọng, đầy đủ đồ điện tử thông minh.',
                        'review' => 'An ninh tuyệt đối 24/7, bãi xe thông minh.',
                        'author' => 'Bảo Trâm',
                        'rating' => 5
                    ]
                ]
            ],
            [
                'name' => 'Nhà trọ Renty Sinh Viên Gò Vấp - IUH',
                'address' => 'Số 12 Nguyễn Văn Bảo, Phường 4, Quận Gò Vấp, TP. Hồ Chí Minh',
                'description' => 'Cách cổng trường ĐH Công Nghiệp TP.HCM (IUH) 150m, chợ Gò Vấp 250m. Tuyến đường trung tâm ăn uống sầm uất giá sinh viên.',
                'rooms' => [
                    [
                        'room_number' => 'GV-101',
                        'floor' => 1,
                        'status' => 'empty',
                        'room_type' => 'standard',
                        'price' => 2500000,
                        'area' => 20,
                        'amenities' => ['wc', 'gác lửng', 'wifi', 'thú cưng'],
                        'description' => 'Phòng trọ gác cao ráo cách cổng ĐH Công Nghiệp 2 phút đi bộ, giờ tự do, có bảo vệ.',
                        'review' => 'Gần IUH đi bộ đi học tiện lợi, không lo kẹt xe trễ giờ điểm danh.',
                        'author' => 'Quốc Cường (SV IUH)',
                        'rating' => 5
                    ],
                    [
                        'room_number' => 'GV-102',
                        'floor' => 1,
                        'status' => 'empty',
                        'room_type' => 'studio',
                        'price' => 3000000,
                        'area' => 24,
                        'amenities' => ['ban công', 'wc', 'máy lạnh', 'gác lửng'],
                        'description' => 'Phòng studio máy lạnh gác lửng gỗ sang trọng, ban công riêng phơi đồ thoáng mát.',
                        'review' => 'Phòng sạch đẹp, ban công thoáng đãng, chủ nhà hỗ trợ tiền nước rất rẻ.',
                        'author' => 'Thanh Thảo (SV IUH)',
                        'rating' => 5
                    ]
                ]
            ],
        ];

        // 7. Tạo Tòa nhà, Phòng và Đánh giá mẫu
        foreach ($buildingsData as $bData) {
            $building = Building::updateOrCreate(
                [
                    'name' => $bData['name'],
                ],
                [
                    'tenant_id' => $tenant->id,
                    'address' => $bData['address'],
                    'description' => $bData['description'],
                ]
            );

            foreach ($bData['rooms'] as $rData) {
                $room = Room::updateOrCreate(
                    [
                        'building_id' => $building->id,
                        'room_number' => $rData['room_number'],
                    ],
                    [
                        'tenant_id' => $tenant->id,
                        'floor' => $rData['floor'],
                        'status' => $rData['status'],
                        'room_type' => $rData['room_type'],
                        'price' => $rData['price'],
                        'area' => $rData['area'],
                        'amenities' => $rData['amenities'],
                        'description' => $rData['description'],
                        'version' => 1,
                    ]
                );

                if (!empty($rData['review'])) {
                    Review::updateOrCreate(
                        [
                            'room_id' => $room->id,
                            'author_name' => $rData['author'],
                        ],
                        [
                            'rating' => $rData['rating'],
                            'comment' => $rData['review'],
                            'created_at' => Carbon::now()->subDays(rand(2, 30)),
                        ]
                    );
                }
            }
        }
    }
}
