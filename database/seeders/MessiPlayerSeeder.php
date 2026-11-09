<?php

namespace Database\Seeders;

use App\Models\Player;
use Illuminate\Database\Seeder;

class MessiPlayerSeeder extends Seeder
{
    /**
     * Seeder lưu trữ thông tin chuẩn về cầu thủ vĩ đại Lionel Messi
     * Bao gồm: Tiểu sử, thông tin cá nhân, CLB, vị trí, thống kê sự nghiệp, danh hiệu và các trận đấu kinh điển
     */
    public function run(): void
    {
        Player::updateOrCreate(
            ['name' => 'Lionel Messi'],
            [
                'full_name' => 'Lionel Andrés Messi Cuccittini',
                'nickname' => 'La Pulga, M10, Leo, El Diez, GOAT',
                'date_of_birth' => '1987-06-24',
                'birth_place' => 'Rosario, Santa Fe, Argentina',
                'nationality' => 'Argentina',
                'position' => 'Tiền đạo cánh phải / Tiền vệ tấn công / Nhạc trưởng (Forward / Attacking Midfielder)',
                'jersey_number' => 10,
                'current_club' => 'Inter Miami CF (MLS, Hoa Kỳ)',
                'former_clubs' => [
                    [
                        'club' => "Newell's Old Boys",
                        'period' => '1995 - 2000',
                        'role' => 'Học viện đào tạo trẻ'
                    ],
                    [
                        'club' => 'FC Barcelona',
                        'period' => '2000 - 2021',
                        'role' => 'Đội một & Đội trưởng huyền thoại'
                    ],
                    [
                        'club' => 'Paris Saint-Germain (PSG)',
                        'period' => '2021 - 2023',
                        'role' => 'Cầu thủ chính thức'
                    ],
                    [
                        'club' => 'Inter Miami CF',
                        'period' => '2023 - Hiện tại',
                        'role' => 'Đội trưởng'
                    ]
                ],
                'height' => '1.70 m',
                'preferred_foot' => 'Chân trái (Left-footed)',
                'biography' => 'Lionel Andrés Messi (sinh ngày 24 tháng 6 năm 1987 tại Rosario, Argentina) là một trong những cầu thủ bóng đá vĩ đại nhất lịch sử nhân loại (The GOAT). Khởi đầu sự nghiệp chuyên nghiệp tại FC Barcelona từ lò đào tạo danh tiếng La Masia, Messi đã cống hiến hơn hai thập kỷ đỉnh cao, ghi hàng trăm siêu phẩm và thiết lập vô số kỷ lục thế giới. Anh là thủ lĩnh dẫn dắt Đội tuyển bóng đá quốc gia Argentina giành chức vô địch Copa America 2021, 2024 và đỉnh cao là chiếc cúp vô địch thế giới FIFA World Cup 2022 tại Qatar.',
                'career_stats' => [
                    'total_goals' => '840+ bàn thắng chính thức',
                    'total_assists' => '375+ kiến tạo thành bàn',
                    'total_appearances' => '1080+ trận đấu chuyên nghiệp',
                    'barcelona_record' => '672 bàn thắng / 778 trận (Kỷ lục lịch sử CLB)',
                    'argentina_national_team' => '112 bàn thắng / 189 trận (Đội trưởng & Chân sút vĩ đại nhất lịch sử Argentina)',
                    'hat_tricks' => '57 cú Hat-trick trong sự nghiệp',
                    'free_kick_goals' => '65+ bàn thắng từ đá phạt trực tiếp',
                ],
                'achievements' => [
                    'individual' => [
                        '8 Quả bóng vàng thế giới (Ballon d\'Or): 2009, 2010, 2011, 2012, 2015, 2019, 2021, 2023 (Kỷ lục vô tiền khoáng hậu)',
                        '6 Chiếc giày vàng châu Âu (European Golden Shoe)',
                        '2 Quả bóng vàng World Cup (FIFA World Cup Golden Ball): 2014, 2022',
                        '3 Cầu thủ nam xuất sắc nhất năm của FIFA (The Best FIFA Men\'s Player): 2019, 2022, 2023',
                    ],
                    'national_team' => [
                        '1 FIFA World Cup: 2022 (Qatar)',
                        '2 Copa America: 2021 (Brazil), 2024 (Hoa Kỳ)',
                        '1 Siêu cúp Liên lục địa Conmebol - UEFA Finalissima: 2022 (Đánh bại tuyển Ý 3-0 tại Wembley)',
                        '1 Huy chương Vàng Olympic môn bóng đá nam: 2008 (Bắc Kinh)',
                        '1 FIFA U-20 World Cup: 2005 (Hà Lan)',
                    ],
                    'club' => [
                        '4 UEFA Champions League (Cúp C1 châu Âu): 2005-06, 2008-09, 2010-11, 2014-15',
                        '10 La Liga (Vô địch bóng đá Tây Ban Nha): 2005, 2006, 2009, 2010, 2011, 2013, 2015, 2016, 2018, 2019',
                        '7 Copa del Rey (Cúp Nhà vua Tây Ban Nha)',
                        '3 FIFA Club World Cup: 2009, 2011, 2015',
                        '3 Siêu cúp châu Âu (UEFA Super Cup): 2009, 2011, 2015',
                        '2 Ligue 1 (Vô địch nước Pháp cùng PSG): 2021-22, 2022-23',
                        '1 Leagues Cup (Inter Miami): 2023',
                        '1 Supporters\' Shield (Inter Miami): 2024',
                    ]
                ],
                'famous_matches' => [
                    [
                        'match' => 'Chung kết FIFA World Cup 2022: Argentina 3-3 Pháp (Pen 4-2)',
                        'date' => '18/12/2022',
                        'tournament' => 'FIFA World Cup 2022',
                        'stadium' => 'Sân vận động Lusail Iconic Stadium (Lusail, Qatar)',
                        'highlight' => 'Trận chung kết vĩ đại nhất lịch sử thể thao. Messi lập cú đúp lịch sử ở phút 23 và 108, sút thành công quả phạt đền đầu tiên trong loạt luân lưu, chính thức hoàn tất bộ sưu tập danh hiệu để trở thành GOAT.',
                    ],
                    [
                        'match' => 'El Clasico La Liga 2017: Real Madrid 2-3 FC Barcelona',
                        'date' => '23/04/2017',
                        'tournament' => 'La Liga Tây Ban Nha',
                        'stadium' => 'Santiago Bernabéu (Madrid)',
                        'highlight' => 'Messi lập cú đúp với bàn thắng quyết định ở phút 90+2 sau đường kiến tạo của Jordi Alba, cởi áo giơ lên trước khán đài Bernabeu tạo nên khoảnh khắc ăn mừng kinh điển.',
                    ],
                    [
                        'match' => 'Chung kết UEFA Champions League 2011: FC Barcelona 3-1 Manchester United',
                        'date' => '28/05/2011',
                        'tournament' => 'UEFA Champions League 2010 - 2011',
                        'stadium' => 'Wembley Stadium (London, Anh)',
                        'highlight' => 'Messi tung cú sút xa sấm sét hạ gục thủ thành huyền thoại Edwin van der Sar, đưa lối đá tiki-taka của HLV Pep Guardiola lên đỉnh cao vũ trụ bóng đá.',
                    ],
                    [
                        'match' => 'Bán kết lượt đi UEFA Champions League 2015: FC Barcelona 3-0 Bayern Munich',
                        'date' => '06/05/2015',
                        'tournament' => 'UEFA Champions League 2014 - 2015',
                        'stadium' => 'Camp Nou (Barcelona)',
                        'highlight' => 'Cú đúp siêu phàm của Messi, trong đó có pha lắc bóng vặn sườn làm ngã trung vệ Jerome Boateng và cú lốp bóng qua đầu Manuel Neuer.',
                    ],
                    [
                        'match' => 'Vòng 16 đội UEFA Champions League 2012: FC Barcelona 7-1 Bayer Leverkusen',
                        'date' => '07/03/2012',
                        'tournament' => 'UEFA Champions League 2011 - 2012',
                        'stadium' => 'Camp Nou (Barcelona)',
                        'highlight' => 'Lionel Messi trở thành cầu thủ đầu tiên trong lịch sử bóng đá thế giới ghi 5 bàn thắng trong một trận đấu knock-out tại đấu trường UEFA Champions League.',
                    ],
                ],
                'avatar_url' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=800&q=80',
            ]
        );

        $this->command?->info("Đã tạo thành công dữ liệu thông tin chi tiết về cầu thủ Lionel Messi trong bảng 'players'!");
    }
}

