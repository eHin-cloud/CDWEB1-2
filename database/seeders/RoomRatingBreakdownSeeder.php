<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\Review;
use Carbon\Carbon;

class RoomRatingBreakdownSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = Room::all();

        $sampleReviews = [
            ['author' => 'Trần Văn Hoàng', 'comment' => 'Phòng ở rất êm, an ninh tốt, khóa vân tay và camera an toàn.'],
            ['author' => 'Lê Thị Thu Hằng', 'comment' => 'Chủ nhà dễ tính, hỗ trợ nhiệt tình khi báo hỏng hóc đồ đạc.'],
            ['author' => 'Nguyễn Minh Tuấn', 'comment' => 'Giá điện nước tính theo đồng hồ riêng rõ ràng, không bị đội chi phí.'],
            ['author' => 'Phạm Quỳnh Nga', 'comment' => 'Khu trọ yên tĩnh, vệ sinh sạch sẽ, lối đi lại rộng rãi thuận tiện.'],
            ['author' => 'Vũ Đình Trọng', 'comment' => 'Không gian thoáng mát, đồ dùng cơ bản đầy đủ, đáng tiền so với khu vực.'],
            ['author' => 'Hoàng Mai Anh', 'comment' => 'Chỗ để xe rộng, an ninh đảm bảo, bác chủ nhà thân thiện.'],
            ['author' => 'Đặng Quốc Bảo', 'comment' => 'Mạng internet mạnh, phòng cách âm tốt, giá cả rất hợp lý cho sinh viên.'],
        ];

        foreach ($rooms as $room) {
            // Tạo các con số random tự nhiên khác nhau cho 3 tiêu chí
            $sec = round(mt_rand(39, 50) / 10, 1);
            $land = round(mt_rand(41, 50) / 10, 1);
            $price = round(mt_rand(37, 49) / 10, 1);

            // Đảm bảo không bị trùng nhau cả 3 số nếu vô tình trùng
            if ($sec == $land && $land == $price) {
                $land = min(5.0, $land + 0.2);
                $price = max(3.5, $price - 0.2);
            }

            $room->update([
                'rating_security' => $sec,
                'rating_landlord' => $land,
                'rating_pricing' => $price,
            ]);

            // Thêm review mẫu ngẫu nhiên nếu phòng chưa có đánh giá nào
            if ($room->reviews()->count() === 0) {
                $revCount = mt_rand(1, 3);
                $pickedKeys = (array) array_rand($sampleReviews, $revCount);
                $overallStar = (int) round(($sec + $land + $price) / 3);

                foreach ($pickedKeys as $key) {
                    $item = $sampleReviews[$key];
                    $star = max(3, min(5, (int) round($overallStar + (mt_rand(-1, 1) * 0.5))));
                    Review::create([
                        'room_id' => $room->id,
                        'author_name' => $item['author'],
                        'rating' => $star,
                        'comment' => $item['comment'],
                        'created_at' => Carbon::now()->subDays(mt_rand(1, 30)),
                    ]);
                }
            }
        }
    }
}
