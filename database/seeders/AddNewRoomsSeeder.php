<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\Building;
use Illuminate\Database\Seeder;

class AddNewRoomsSeeder extends Seeder
{
    /**
     * Thêm các phòng mới cao cấp đầy đủ tiện ích (Máy lạnh, Máy giặt, Tủ lạnh, Wifi)
     * phân bổ tại các toà nhà trọng điểm để kiểm thử bộ lọc tiện ích.
     */
    public function run(): void
    {
        $buildings = Building::take(6)->get();

        if ($buildings->isEmpty()) {
            $this->command?->warn('Chưa có toà nhà nào để thêm phòng.');
            return;
        }

        $imageSets = [
            [
                'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1554995207-c18c203602cb?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1560185127-6ed189bf02f4?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1552321554-5fefe8c9ef14?auto=format&fit=crop&w=1200&q=80',
            ],
        ];

        $newRoomsData = [
            // Cầu Giấy
            [
                'building_id' => 1,
                'room_number' => 'CG-101',
                'floor' => 1,
                'room_type' => 'studio',
                'rental_type' => 'month',
                'price' => 3800000,
                'deposit' => 3800000,
                'area' => 28,
                'status' => 'empty',
                'amenities' => ['Máy lạnh', 'Máy giặt', 'Tủ lạnh', 'Wifi', 'ac', 'washer', 'fridge', 'wifi', 'gác lửng', 'loft', 'ban công', 'balcony', 'khép kín', 'wc'],
                'description' => 'Studio cao cấp tại Xuân Thủy, Cầu Giấy. Trang bị Full nội thất: Máy lạnh Inverter, Máy giặt riêng, Tủ lạnh 2 cánh, Wifi cáp quang siêu tốc. Có gác lửng cao, ban công thoáng và WC khép kín sạch sẽ.',
            ],
            [
                'building_id' => 1,
                'room_number' => 'CG-202',
                'floor' => 2,
                'room_type' => 'deluxe',
                'rental_type' => 'month',
                'price' => 4200000,
                'deposit' => 4200000,
                'area' => 32,
                'status' => 'empty',
                'amenities' => ['Máy lạnh', 'Máy giặt', 'Tủ lạnh', 'Wifi', 'ac', 'washer', 'fridge', 'wifi', 'ban công', 'balcony', 'khép kín', 'wc'],
                'description' => 'Phòng Deluxe Cầu Giấy thoáng sáng, có ban công riêng view thoáng. Đầy đủ Máy lạnh, Máy giặt, Tủ lạnh và Wifi tốc độ cao. Giờ giấc tự do, khóa vân tay an toàn.',
            ],
            [
                'building_id' => 2,
                'room_number' => 'CG-305',
                'floor' => 3,
                'room_type' => 'vip',
                'rental_type' => 'month',
                'price' => 4800000,
                'deposit' => 4800000,
                'area' => 35,
                'status' => 'empty',
                'amenities' => ['Máy lạnh', 'Máy giặt', 'Tủ lạnh', 'Wifi', 'ac', 'washer', 'fridge', 'wifi', 'thú cưng', 'pets', 'gác lửng', 'loft', 'khép kín', 'wc'],
                'description' => 'Phòng VIP Chùa Hà, Cầu Giấy cho phép nuôi thú cưng (pets). Đầy đủ nội thất: Máy lạnh, Máy giặt, Tủ lạnh, Wifi. Thiết kế gác lửng hiện đại, rộng rãi.',
            ],
            [
                'building_id' => 2,
                'room_number' => 'CG-401',
                'floor' => 4,
                'room_type' => 'studio',
                'rental_type' => 'month',
                'price' => 3900000,
                'deposit' => 3900000,
                'area' => 26,
                'status' => 'empty',
                'amenities' => ['Máy lạnh', 'Máy giặt', 'Tủ lạnh', 'Wifi', 'ac', 'washer', 'fridge', 'wifi', 'khép kín', 'wc'],
                'description' => 'Căn hộ Studio tiện nghi đầy đủ: Máy lạnh, Máy giặt, Tủ lạnh, Wifi tốc độ cao. Gần ĐH Sư Phạm, ĐH Quốc Gia Hà Nội.',
            ],

            // Thanh Xuân
            [
                'building_id' => 3,
                'room_number' => 'TX-102',
                'floor' => 1,
                'room_type' => 'studio',
                'rental_type' => 'month',
                'price' => 3500000,
                'deposit' => 3500000,
                'area' => 25,
                'status' => 'empty',
                'amenities' => ['Máy lạnh', 'Máy giặt', 'Tủ lạnh', 'Wifi', 'ac', 'washer', 'fridge', 'wifi', 'gác lửng', 'loft', 'khép kín', 'wc'],
                'description' => 'Studio Vũ Tông Phan, Thanh Xuân. Đầy đủ tiện ích: Máy lạnh, Máy giặt, Tủ lạnh, Wifi. Gác lửng cao không đụng đầu, ngõ thông thoáng.',
            ],
            [
                'building_id' => 3,
                'room_number' => 'TX-203',
                'floor' => 2,
                'room_type' => 'vip',
                'rental_type' => 'month',
                'price' => 4500000,
                'deposit' => 4500000,
                'area' => 30,
                'status' => 'empty',
                'amenities' => ['Máy lạnh', 'Máy giặt', 'Tủ lạnh', 'Wifi', 'ac', 'washer', 'fridge', 'wifi', 'ban công', 'balcony', 'thú cưng', 'pets'],
                'description' => 'Phòng VIP Thanh Xuân có ban công phơi đồ riêng, nuôi pet thoải mái. Đã trang bị Máy lạnh, Máy giặt, Tủ lạnh, Wifi trọn gói.',
            ],
            [
                'building_id' => 4,
                'room_number' => 'TX-301',
                'floor' => 3,
                'room_type' => 'deluxe',
                'rental_type' => 'month',
                'price' => 4100000,
                'deposit' => 4100000,
                'area' => 28,
                'status' => 'empty',
                'amenities' => ['Máy lạnh', 'Máy giặt', 'Tủ lạnh', 'Wifi', 'ac', 'washer', 'fridge', 'wifi', 'khép kín', 'wc'],
                'description' => 'Phòng Khương Đình, Thanh Xuân đầy đủ tiện nghi sinh hoạt: Máy lạnh mát sâu, Máy giặt riêng, Tủ lạnh, Wifi tốc độ cao.',
            ],

            // Đống Đa
            [
                'building_id' => 5,
                'room_number' => 'DD-101',
                'floor' => 1,
                'room_type' => 'studio',
                'rental_type' => 'month',
                'price' => 4600000,
                'deposit' => 4600000,
                'area' => 30,
                'status' => 'empty',
                'amenities' => ['Máy lạnh', 'Máy giặt', 'Tủ lạnh', 'Wifi', 'ac', 'washer', 'fridge', 'wifi', 'gác lửng', 'loft', 'ban công', 'balcony'],
                'description' => 'Căn hộ Xã Đàn, Đống Đa trung tâm. Full 4 tiện ích: Máy lạnh, Máy giặt, Tủ lạnh, Wifi. Có gác lửng và ban công cực thoáng mát.',
            ],
            [
                'building_id' => 5,
                'room_number' => 'DD-205',
                'floor' => 2,
                'room_type' => 'vip',
                'rental_type' => 'month',
                'price' => 5200000,
                'deposit' => 5200000,
                'area' => 36,
                'status' => 'empty',
                'amenities' => ['Máy lạnh', 'Máy giặt', 'Tủ lạnh', 'Wifi', 'ac', 'washer', 'fridge', 'wifi', 'thú cưng', 'pets', 'ban công', 'balcony', 'khép kín', 'wc'],
                'description' => 'Phòng VIP Đống Đa gần các trường Bách Khoa, Kinh Tế, Xây Dựng. Trang bị Full 4 thiết bị cao cấp: Máy lạnh, Máy giặt, Tủ lạnh, Wifi. Ban công rộng, cho nuôi pet.',
            ],
            [
                'building_id' => 5,
                'room_number' => 'DD-302',
                'floor' => 3,
                'room_type' => 'deluxe',
                'rental_type' => 'month',
                'price' => 4400000,
                'deposit' => 4400000,
                'area' => 29,
                'status' => 'empty',
                'amenities' => ['Máy lạnh', 'Máy giặt', 'Tủ lạnh', 'Wifi', 'ac', 'washer', 'fridge', 'wifi', 'khép kín', 'wc'],
                'description' => 'Phòng Deluxe Đống Đa hiện đại: Máy lạnh, Máy giặt riêng, Tủ lạnh, Wifi cáp quang. An ninh bảo vệ 24/24.',
            ],
        ];

        $created = 0;
        foreach ($newRoomsData as $idx => $data) {
            $building = Building::find($data['building_id']) ?? $buildings->first();
            $images = $imageSets[$idx % count($imageSets)];

            Room::updateOrCreate(
                [
                    'building_id' => $building->id,
                    'room_number' => $data['room_number'],
                ],
                [
                    'tenant_id' => $building->tenant_id,
                    'floor' => $data['floor'],
                    'room_type' => $data['room_type'],
                    'rental_type' => $data['rental_type'],
                    'price' => $data['price'],
                    'deposit' => $data['deposit'],
                    'area' => $data['area'],
                    'status' => $data['status'],
                    'cleaning_status' => 'clean',
                    'housekeeping_status' => 'clean',
                    'priority' => 'normal',
                    'amenities' => $data['amenities'],
                    'description' => $data['description'],
                    'image' => $images[0],
                    'images' => $images,
                    'rating_security' => 4.9,
                    'rating_landlord' => 4.8,
                    'rating_pricing' => 4.9,
                ]
            );
            $created++;
        }

        $this->command?->info("Đã tạo mới và cập nhật thành công {$created} phòng cao cấp có đầy đủ 4 tiện ích (Máy lạnh, Máy giặt, Tủ lạnh, Wifi)!");
    }
}
