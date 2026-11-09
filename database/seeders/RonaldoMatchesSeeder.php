<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class RonaldoMatchesSeeder extends Seeder
{
    /**
     * Seeder riêng cho Cristiano Ronaldo - CHỈ lưu thông tin các TRẬN ĐẤU lịch sử của Cristiano Ronaldo (Ronado, CR7)
     * Phục vụ test cả 2 tính năng:
     * 1. Tìm kiếm bình thường (Normal Search)
     * 2. Tìm kiếm bằng Search Engine (Scout, Solr, Smart Hybrid Search)
     */
    public function run(): void
    {
        // Dọn dẹp dữ liệu cũ từ seeder gộp trước đó (nếu có)
        $oldBuilding = Building::where('name', 'Tòa nhà Cristiano Ronaldo - CR7 Kingdom')->first();
        if ($oldBuilding) {
            Room::where('building_id', $oldBuilding->id)->delete();
            $oldBuilding->delete();
        }

        $tenant = Tenant::first() ?? Tenant::create([
            'name' => 'Bộ Sưu Tập Trận Đấu Huyền Thoại Renty',
            'email' => 'matches@renty.vn',
            'phone' => '0988101010',
            'bank_name' => 'Vietcombank',
            'bank_account_no' => '1010101010',
            'bank_account_name' => 'RENTY MATCHES',
        ]);

        $defaultImages = [
            'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1200&q=80',
        ];

        // 1. Tạo toà nhà đại diện cho các trận đấu của Cristiano Ronaldo
        $building = Building::firstOrCreate(
            ['name' => 'Bộ Sưu Tập Trận Đấu Bất Hủ Của Cristiano Ronaldo'],
            [
                'tenant_id' => $tenant->id,
                'address' => 'Sân vận động Thiên niên kỷ Cardiff - Quận 1, TP. Hồ Chí Minh',
                'description' => 'Không gian tái hiện những trận cầu lịch sử kinh điển của Cristiano Ronaldo (CR7, Ronado): Chung kết Champions League 2017 tại Cardiff vs Juventus, Siêu phẩm xe đạp chổng ngược 2018 tại Turin, Hat-trick rực lửa World Cup 2018 vs Tây Ban Nha tại Sochi, Cú ngược dòng hat-trick 2019 vs Atletico Madrid và Bán kết Euro 2016 vs Xứ Wales.',
                'property_type' => 'apartment',
                'total_floors' => 10,
                'image' => $defaultImages[0],
            ]
        );

        $building->update([
            'tenant_id' => $tenant->id,
            'address' => 'Sân vận động Thiên niên kỷ Cardiff - Quận 1, TP. Hồ Chí Minh',
            'description' => 'Không gian tái hiện những trận cầu lịch sử kinh điển của Cristiano Ronaldo (CR7, Ronado): Chung kết Champions League 2017 tại Cardiff vs Juventus, Siêu phẩm xe đạp chổng ngược 2018 tại Turin, Hat-trick rực lửa World Cup 2018 vs Tây Ban Nha tại Sochi, Cú ngược dòng hat-trick 2019 vs Atletico Madrid và Bán kết Euro 2016 vs Xứ Wales.',
            'image' => $defaultImages[0],
        ]);

        // 2. Danh sách các trận đấu kinh điển của Cristiano Ronaldo
        $matches = [
            [
                'room_number' => 'CR7-UCL2017',
                'floor' => 7,
                'room_type' => 'vip',
                'price' => 7000000,
                'deposit' => 7000000,
                'area' => 40,
                'status' => 'empty',
                'amenities' => ['Máy lạnh', 'Máy giặt', 'Tủ lạnh', 'Wifi', 'ac', 'washer', 'fridge', 'wifi', 'ban công', 'balcony', 'khép kín', 'wc'],
                'description' => 'Trận Chung kết UEFA Champions League ngày 03/06/2017: Real Madrid 4-1 Juventus tại sân Thiên niên kỷ Cardiff Xứ Wales. Cristiano Ronaldo (CR7, Ronado) rực sáng với cú đúp bàn thắng xé lưới thủ môn huyền thoại Buffon ở phút 20 và 64, đưa Real Madrid trở thành CLB đầu tiên bảo vệ thành công ngôi vô địch Champions League thời hiện đại.',
            ],
            [
                'room_number' => 'CR7-WC2018',
                'floor' => 3,
                'room_type' => 'deluxe',
                'price' => 5500000,
                'deposit' => 5500000,
                'area' => 36,
                'status' => 'empty',
                'amenities' => ['Máy lạnh', 'Tủ lạnh', 'Wifi', 'ac', 'fridge', 'wifi', 'ban công', 'balcony', 'khép kín', 'wc'],
                'description' => 'Trận đấu bảng B World Cup 2018 ngày 15/06/2018: Bồ Đào Nha 3-3 Tây Ban Nha tại sân vận động Fisht Olympic Sochi nước Nga. Cristiano Ronaldo (CR7, Ronado) bùng nổ lập cú Hat-trick vĩ đại vào lưới David de Gea, trong đó có siêu phẩm sút phạt hàng rào gỡ hòa 3-3 ngoạn mục ở phút 88.',
            ],
            [
                'room_number' => 'CR7-BICYCLE2018',
                'floor' => 8,
                'room_type' => 'vip',
                'price' => 6800000,
                'deposit' => 6800000,
                'area' => 38,
                'status' => 'empty',
                'amenities' => ['Máy lạnh', 'Máy giặt', 'Tủ lạnh', 'Wifi', 'ac', 'washer', 'fridge', 'wifi', 'ban công', 'balcony', 'khép kín', 'wc'],
                'description' => 'Trận Tứ kết Champions League ngày 03/04/2018: Juventus 0-3 Real Madrid tại sân vận động Allianz Turin Italia. Cristiano Ronaldo (CR7, Ronado) ghi bàn thắng để đời bằng cú tung người móc bóng xe đạp chổng ngược (xe dap chong nguoc) ở độ cao 2m38 khiến cả sân vận động Juventus đứng dậy vỗ tay tán thưởng.',
            ],
            [
                'room_number' => 'CR7-ATLETICO2019',
                'floor' => 4,
                'room_type' => 'studio',
                'price' => 4200000,
                'deposit' => 4200000,
                'area' => 30,
                'status' => 'empty',
                'amenities' => ['Máy lạnh', 'Wifi', 'ac', 'wifi', 'gác lửng', 'loft', 'khép kín', 'wc'],
                'description' => 'Trận Lượt về vòng 16 đội Champions League ngày 12/03/2019: Juventus 3-0 Atletico Madrid (chung cuộc 3-2) tại Allianz Stadium. Thua 0-2 ở lượt đi, Cristiano Ronaldo (CR7, Ronado) lập cú Hat-trick ngược dòng điên rồ với 2 pha bật cao đánh đầu uy lực và cú sút phạt đền quyết định ở phút 86.',
            ],
            [
                'room_number' => 'CR7-EURO2016',
                'floor' => 5,
                'room_type' => 'standard',
                'price' => 3600000,
                'deposit' => 3600000,
                'area' => 28,
                'status' => 'empty',
                'amenities' => ['Máy lạnh', 'Wifi', 'ac', 'wifi', 'thú cưng', 'pets', 'khép kín', 'wc'],
                'description' => 'Trận Bán kết Euro 2016 ngày 06/07/2016: Bồ Đào Nha 2-0 Xứ Wales tại sân Parc Olympique Lyonnais. Cristiano Ronaldo (CR7, Ronado) bật cao kỷ lục 2m88 đánh đầu mở tỷ số và kiến tạo bàn ấn định 2-0, trực tiếp đưa Bồ Đào Nha vào chung kết và lần đầu tiên trong lịch sử đăng quang vô địch Euro 2016.',
            ],
        ];

        foreach ($matches as $idx => $mData) {
            $img = $defaultImages[$idx % count($defaultImages)];
            $subImg = $defaultImages[($idx + 1) % count($defaultImages)];

            Room::updateOrCreate(
                [
                    'building_id' => $building->id,
                    'room_number' => $mData['room_number'],
                ],
                [
                    'tenant_id' => $tenant->id,
                    'floor' => $mData['floor'],
                    'status' => $mData['status'],
                    'room_type' => $mData['room_type'],
                    'rental_type' => 'month',
                    'price' => $mData['price'],
                    'deposit' => $mData['deposit'],
                    'area' => $mData['area'],
                    'cleaning_status' => 'clean',
                    'housekeeping_status' => 'clean',
                    'priority' => 'normal',
                    'amenities' => $mData['amenities'],
                    'description' => $mData['description'],
                    'image' => $img,
                    'images' => [$img, $subImg],
                    'rating_security' => 5.0,
                    'rating_landlord' => 5.0,
                    'rating_pricing' => 4.9,
                    'version' => 1,
                ]
            );
        }

        $this->command?->info("Đã tạo thành công Seeder riêng Ronaldo: Toà nhà và " . count($matches) . " trận đấu lịch sử của Cristiano Ronaldo (Ronado)!");
    }
}
