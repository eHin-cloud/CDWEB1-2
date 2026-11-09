<?php

namespace Database\Seeders;

use App\Models\Player;
use Illuminate\Database\Seeder;

class RonaldoPlayerSeeder extends Seeder
{
    /**
     * Seeder lưu trữ thông tin chuẩn về siêu sao Cristiano Ronaldo (CR7, Ronado)
     */
    public function run(): void
    {
        Player::updateOrCreate(
            ['name' => 'Cristiano Ronaldo'],
            [
                'full_name' => 'Cristiano Ronaldo dos Santos Aveiro',
                'nickname' => 'CR7, Ronado, El Bicho, Mr. Champions League, GOAT',
                'date_of_birth' => '1985-02-05',
                'birth_place' => 'Funchal, Madeira, Bồ Đào Nha',
                'nationality' => 'Bồ Đào Nha (Portugal)',
                'position' => 'Tiền đạo cắm / Tiền đạo cánh trái (Striker / Left Winger)',
                'jersey_number' => 7,
                'current_club' => 'Al Nassr FC (Saudi Pro League)',
                'former_clubs' => [
                    [
                        'club' => 'Sporting CP',
                        'period' => '2002 - 2003',
                        'role' => 'Ra mắt bóng đá chuyên nghiệp'
                    ],
                    [
                        'club' => 'Manchester United',
                        'period' => '2003 - 2009 & 2021 - 2022',
                        'role' => 'Số 7 huyền thoại & Quả bóng vàng đầu tiên'
                    ],
                    [
                        'club' => 'Real Madrid',
                        'period' => '2009 - 2018',
                        'role' => 'Chân sút vĩ đại nhất lịch sử CLB (450 bàn)'
                    ],
                    [
                        'club' => 'Juventus',
                        'period' => '2018 - 2021',
                        'role' => 'Vua phá lưới Serie A'
                    ],
                    [
                        'club' => 'Al Nassr FC',
                        'period' => '2023 - Hiện tại',
                        'role' => 'Đội trưởng'
                    ]
                ],
                'height' => '1.87 m',
                'preferred_foot' => 'Chân phải (Right-footed, hai chân như một)',
                'biography' => 'Cristiano Ronaldo dos Santos Aveiro (sinh ngày 5 tháng 2 năm 1985 tại Funchal, Madeira, Bồ Đào Nha) là một trong những chân sút vĩ đại nhất mọi thời đại. Nổi tiếng với ý chí thép, thể hình lý tưởng và khả năng săn bàn thiện nghệ, CR7 đã chinh phục mọi giải đấu hàng đầu châu Âu (Premier League, La Liga, Serie A) và dẫn dắt tuyển Bồ Đào Nha đăng quang chức vô địch UEFA Euro 2016 lịch sử.',
                'career_stats' => [
                    'total_goals' => '900+ bàn thắng chính thức (Kỷ lục mọi thời đại bóng đá thế giới)',
                    'total_assists' => '250+ kiến tạo',
                    'total_appearances' => '1200+ trận đấu chính thức',
                    'real_madrid_record' => '450 bàn thắng / 438 trận (1.03 bàn/trận - Kỷ lục tuyệt đối Real Madrid)',
                    'champions_league_record' => '140 bàn thắng (Chân sút số 1 lịch sử UEFA Champions League)',
                    'portugal_national_team' => '130+ bàn thắng (Chân sút số 1 lịch sử các ĐTQG trên thế giới)',
                    'hat_tricks' => '66 cú Hat-trick đỉnh cao',
                ],
                'achievements' => [
                    'individual' => [
                        '5 Quả bóng vàng thế giới (Ballon d\'Or): 2008, 2013, 2014, 2016, 2017',
                        '4 Chiếc giày vàng châu Âu (European Golden Shoe)',
                        '3 Cầu thủ xuất sắc nhất năm của FIFA (The Best FIFA / FIFA World Player)',
                    ],
                    'national_team' => [
                        '1 UEFA Euro: 2016 (Pháp)',
                        '1 UEFA Nations League: 2019 (Bồ Đào Nha)',
                    ],
                    'club' => [
                        '5 UEFA Champions League (Cúp C1 châu Âu): 2008 (MU), 2014, 2016, 2017, 2018 (Real Madrid)',
                        '3 Ngoại Hạng Anh (Premier League cùng MU): 2007, 2008, 2009',
                        '2 La Liga (Vô địch Tây Ban Nha cùng Real Madrid): 2012, 2017',
                        '2 Serie A (Vô địch Italia cùng Juventus): 2019, 2020',
                        '4 FIFA Club World Cup: 2008, 2014, 2016, 2017',
                    ]
                ],
                'famous_matches' => [
                    [
                        'match' => 'Chung kết UEFA Champions League 2017: Real Madrid 4-1 Juventus',
                        'date' => '03/06/2017',
                        'tournament' => 'UEFA Champions League 2016 - 2017',
                        'stadium' => 'Millennium Stadium (Cardiff, Xứ Wales)',
                        'highlight' => 'Ronaldo lập cú đúp xé lưới Buffon phút 20 và 64, đưa Real Madrid trở thành đội bóng đầu tiên bảo vệ thành công chức vô địch cúp C1.',
                    ],
                    [
                        'match' => 'Tứ kết UEFA Champions League 2018: Juventus 0-3 Real Madrid',
                        'date' => '03/04/2018',
                        'tournament' => 'UEFA Champions League 2017 - 2018',
                        'stadium' => 'Allianz Stadium (Turin, Italia)',
                        'highlight' => 'Siêu phẩm ngả người móc bóng xe đạp chổng ngược ở độ cao 2m38 khiến toàn thể CĐV Juventus phải đứng dậy vỗ tay tán thưởng.',
                    ],
                    [
                        'match' => 'Vòng bảng World Cup 2018: Bồ Đào Nha 3-3 Tây Ban Nha',
                        'date' => '15/06/2018',
                        'tournament' => 'FIFA World Cup 2018',
                        'stadium' => 'Fisht Olympic Stadium (Sochi, Nga)',
                        'highlight' => 'Hat-trick rực lửa vào lưới De Gea, cú đá phạt hàng rào hình quả chuối gỡ hòa 3-3 ở phút 88.',
                    ],
                    [
                        'match' => 'Vòng 16 đội UEFA Champions League 2019: Juventus 3-0 Atletico Madrid',
                        'date' => '12/03/2019',
                        'tournament' => 'UEFA Champions League 2018 - 2019',
                        'stadium' => 'Allianz Stadium (Turin, Italia)',
                        'highlight' => 'Cú Hat-trick lội ngược dòng không tưởng (thua 0-2 ở lượt đi), 2 pha đánh đầu dũng mãnh và quả phạt đền phút 86.',
                    ],
                    [
                        'match' => 'Bán kết UEFA Euro 2016: Bồ Đào Nha 2-0 Xứ Wales',
                        'date' => '06/07/2016',
                        'tournament' => 'UEFA Euro 2016',
                        'stadium' => 'Parc Olympique Lyonnais (Lyon, Pháp)',
                        'highlight' => 'Cú bật cao kỷ lục 2m88 đánh đầu mở tỷ số và kiến tạo, đưa Bồ Đào Nha thẳng tiến đến chức vô địch Euro 2016.',
                    ],
                ],
                'avatar_url' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=800&q=80',
            ]
        );

        $this->command?->info("Đã tạo thành công dữ liệu thông tin chi tiết về cầu thủ Cristiano Ronaldo trong bảng 'players'!");
    }
}

