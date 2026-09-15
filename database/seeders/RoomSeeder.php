<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            [
                'tenant' => [
                    'name' => 'Hệ thống SmartRoom Cầu Giấy',
                    'email' => 'contact@smartroom-caugiay.vn',
                    'phone' => '0988123456',
                ],
                'building' => [
                    'name' => 'SmartRoom Cầu Giấy',
                    'address' => 'Số 12 Ngõ 105 Xuân Thủy, Cầu Giấy, Hà Nội',
                    'description' => 'Khu nhà gần ĐH Sư Phạm, ĐH Quốc Gia và trục Cầu Giấy - Hồ Tùng Mậu.',
                ],
                'rooms' => [
                    ['401', 4, 'occupied', 'standard', 3700000, 28, ['Máy lạnh', 'Nóng lạnh', 'Ban công']],
                    ['402', 4, 'maintenance', 'standard', 3700000, 28, ['Máy lạnh', 'Nóng lạnh']],
                    ['403', 4, 'empty', 'vip', 4000000, 32, ['Máy lạnh', 'Nóng lạnh', 'Minibar', 'SmartLock', 'Ban công']],
                    ['404', 4, 'occupied', 'deluxe', 3900000, 30, ['Máy lạnh', 'Nóng lạnh', 'SmartLock']],
                    ['501', 5, 'empty', 'studio', 4100000, 32, ['Máy lạnh', 'Nóng lạnh', 'Minibar', 'SmartLock', 'Ban công']],
                    ['502', 5, 'occupied', 'vip', 4300000, 35, ['Máy lạnh', 'Nóng lạnh', 'Minibar', 'SmartLock', 'Máy giặt']],
                ],
            ],
            [
                'tenant' => [
                    'name' => 'Hệ thống Rentry Home Thanh Xuân',
                    'email' => 'contact@rentry-thanhxuan.vn',
                    'phone' => '0977222333',
                ],
                'building' => [
                    'name' => 'Rentry Home Thanh Xuân',
                    'address' => 'Số 85 Vũ Tông Phan, Thanh Xuân, Hà Nội',
                    'description' => 'Khu studio gần Royal City, Ngã Tư Sở và các tuyến xe buýt lớn.',
                ],
                'rooms' => [
                    ['301', 3, 'empty', 'standard', 4400000, 34, ['Máy lạnh', 'Nóng lạnh', 'Minibar', 'Ban công']],
                    ['302', 3, 'occupied', 'vip', 4600000, 36, ['Máy lạnh', 'Nóng lạnh', 'Minibar', 'SmartLock', 'Ban công']],
                    ['303', 3, 'empty', 'deluxe', 4400000, 34, ['Máy lạnh', 'Nóng lạnh', 'SmartLock']],
                    ['401', 4, 'occupied', 'vip', 4900000, 38, ['Máy lạnh', 'Nóng lạnh', 'Minibar', 'SmartLock', 'Máy giặt']],
                    ['402', 4, 'empty', 'studio', 4700000, 36, ['Máy lạnh', 'Nóng lạnh', 'Minibar', 'SmartLock', 'Ban công']],
                ],
            ],
            [
                'tenant' => [
                    'name' => 'Hệ thống Rentry Studio Quận 10',
                    'email' => 'contact@rentry-quan10.vn',
                    'phone' => '0966888999',
                ],
                'building' => [
                    'name' => 'Rentry Studio Quận 10',
                    'address' => 'Số 42 Thành Thái, Quận 10, TP. Hồ Chí Minh',
                    'description' => 'Khu studio gần Đại học Bách Khoa, Học viện Hành chính và trung tâm Quận 10.',
                ],
                'rooms' => [
                    ['101', 1, 'empty', 'standard', 5200000, 30, ['Máy lạnh', 'Nóng lạnh', 'SmartLock']],
                    ['102', 1, 'occupied', 'deluxe', 5300000, 31, ['Máy lạnh', 'Nóng lạnh', 'Ban công']],
                    ['201', 2, 'empty', 'vip', 5800000, 35, ['Máy lạnh', 'Nóng lạnh', 'Minibar', 'SmartLock']],
                    ['202', 2, 'maintenance', 'standard', 5400000, 32, ['Máy lạnh', 'Nóng lạnh']],
                    ['301', 3, 'occupied', 'studio', 6200000, 38, ['Máy lạnh', 'Nóng lạnh', 'Minibar', 'SmartLock', 'Ban công']],
                ],
            ],
        ];

        foreach ($areas as $area) {
            $tenant = Tenant::updateOrCreate(
                ['email' => $area['tenant']['email']],
                [
                    'name' => $area['tenant']['name'],
                    'phone' => $area['tenant']['phone'],
                    'bank_name' => 'MB',
                    'bank_account_no' => '9999888889999',
                    'bank_account_name' => 'SMARTROOM RENTAL',
                ]
            );

            $building = Building::updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'name' => $area['building']['name'],
                ],
                [
                    'address' => $area['building']['address'],
                    'description' => $area['building']['description'],
                ]
            );

            foreach ($area['rooms'] as [$roomNumber, $floor, $status, $roomType, $price, $roomArea, $amenities]) {
                Room::updateOrCreate(
                    [
                        'building_id' => $building->id,
                        'room_number' => $roomNumber,
                    ],
                    array_merge([
                        'tenant_id' => $tenant->id,
                        'floor' => $floor,
                        'status' => $status,
                        'room_type' => $roomType,
                        'price' => $price,
                        'area' => $roomArea,
                        'amenities' => $amenities,
                        'description' => $area['building']['name'] . ' - phòng ' . $roomNumber . ' thuộc khu vực riêng.',
                        'version' => 1,
                    ], $this->mediaForRoom($area['building']['name'], $roomNumber))
                );
            }
        }
    }

    private function mediaForRoom(string $buildingName, string $roomNumber): array
    {
        $sets = [
            [
                'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1400&q=85',
                'https://images.unsplash.com/photo-1588153203274-4a392d28ed9f?auto=format&fit=crop&w=1400&q=85',
                'https://images.unsplash.com/photo-1484154218962-a197022b5858?auto=format&fit=crop&w=1400&q=85',
                'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1400&q=85',
            ],
            [
                'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1400&q=85',
                'https://images.unsplash.com/photo-1552321554-5fefe8c9ef14?auto=format&fit=crop&w=1400&q=85',
                'https://images.unsplash.com/photo-1556911220-bff31c812dba?auto=format&fit=crop&w=1400&q=85',
                'https://images.unsplash.com/photo-1560448075-bb485b067938?auto=format&fit=crop&w=1400&q=85',
            ],
            [
                'https://images.unsplash.com/photo-1554995207-c18c203602cb?auto=format&fit=crop&w=1400&q=85',
                'https://images.unsplash.com/photo-1620626011761-996317b8d101?auto=format&fit=crop&w=1400&q=85',
                'https://images.unsplash.com/photo-1560185127-6ed189bf02f4?auto=format&fit=crop&w=1400&q=85',
                'https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=1400&q=85',
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
