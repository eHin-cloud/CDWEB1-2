<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class ThuDucChatbotRoomsSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::firstOrCreate(
            ['email' => 'contact@renty-hcm.vn'],
            [
                'name' => 'Hệ thống Căn hộ & Phòng trọ Renty TP.HCM',
                'phone' => '0938112233',
                'bank_name' => 'Vietcombank',
                'bank_account_no' => '0071001234567',
                'bank_account_name' => 'RENTY HCM',
            ]
        );

        $buildings = [
            [
                'name' => 'Chung cư mini Renty Thủ Đức Central',
                'address' => 'Số 18 Võ Văn Ngân, Phường Linh Chiểu, TP. Thủ Đức, TP. Hồ Chí Minh',
                'description' => 'Gần Đại học Sư Phạm Kỹ Thuật (HCMUTE), ngã tư Thủ Đức, chợ Bắc Ninh và tuyến Metro số 1.',
                'rooms' => [
                    [
                        'room_number' => 'TD-101',
                        'floor' => 1,
                        'status' => 'empty',
                        'room_type' => 'studio',
                        'price' => 2500000,
                        'area' => 22,
                        'amenities' => ['ban công', 'wc', 'thú cưng', 'gác lửng'],
                        'description' => 'Phòng trọ Thủ Đức gần trường SPKT, có ban công thoáng mát, gác lửng cao ráo, cho nuôi thú cưng.',
                    ],
                    [
                        'room_number' => 'TD-102',
                        'floor' => 1,
                        'status' => 'empty',
                        'room_type' => 'standard',
                        'price' => 2800000,
                        'area' => 25,
                        'amenities' => ['ban công', 'wc', 'gác lửng', 'máy lạnh'],
                        'description' => 'Phòng studio Thủ Đức đầy đủ tiện nghi, giá rẻ dưới 3 triệu, an ninh 24/7.',
                    ],
                    [
                        'room_number' => 'TD-201',
                        'floor' => 2,
                        'status' => 'occupied',
                        'room_type' => 'deluxe',
                        'price' => 2900000,
                        'area' => 26,
                        'amenities' => ['wc', 'ban công', 'máy lạnh'],
                        'description' => 'Phòng ban công thoáng đãng tại Thủ Đức.',
                    ],
                ]
            ],
            [
                'name' => 'Nhà trọ Renty Sinh Viên Quận 9',
                'address' => 'Số 45 Lê Văn Việt, Phường Tăng Nhơn Phú A, TP. Thủ Đức, TP. Hồ Chí Minh',
                'description' => 'Gần Đại học Giao Thông Vận Tải cơ sở 2, Học viện Công nghệ Bưu chính Viễn thông và Khu Công Nghệ Cao.',
                'rooms' => [
                    [
                        'room_number' => 'Q9-101',
                        'floor' => 1,
                        'status' => 'empty',
                        'room_type' => 'standard',
                        'price' => 2300000,
                        'area' => 20,
                        'amenities' => ['wc', 'gác lửng', 'thú cưng'],
                        'description' => 'Phòng trọ sinh viên Quận 9 giá rẻ dưới 3 triệu, gần trường đại học.',
                    ],
                    [
                        'room_number' => 'Q9-102',
                        'floor' => 1,
                        'status' => 'empty',
                        'room_type' => 'studio',
                        'price' => 2700000,
                        'area' => 24,
                        'amenities' => ['ban công', 'wc', 'thú cưng'],
                        'description' => 'Phòng trọ Lê Văn Việt Quận 9 có ban công, cho nuôi pet.',
                    ],
                ]
            ],
        ];

        foreach ($buildings as $bData) {
            $building = Building::firstOrCreate(
                [
                    'name' => $bData['name'],
                ],
                [
                    'tenant_id' => $tenant->id,
                    'address' => $bData['address'],
                    'description' => $bData['description'],
                ]
            );

            // Cập nhật địa chỉ nếu đã có
            $building->update([
                'tenant_id' => $tenant->id,
                'address' => $bData['address'],
                'description' => $bData['description'],
            ]);

            foreach ($bData['rooms'] as $rData) {
                Room::updateOrCreate(
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
            }
        }
    }
}
