<?php

namespace App\Services;

class VietnameseTokenizerService
{
    protected static ?array $cachedDictionary = null;
    protected string $dictionaryPath;
    protected string $cacheFilePath;

    /**
     * Danh sách hư từ tiếng Việt thường đứng trước/sau danh từ
     */
    protected array $stopParticles = [
        'có', 'và', 'ở', 'gần', 'tại', 'cho', 'với', 'trong', 'được', 'của', 'là', 'các', 'những', 'cần', 'tìm'
    ];

    /**
     * Danh sách từ vựng đặc thù bất động sản / nhà trọ (Ưu tiên cao nhất khi tách từ)
     */
    protected array $domainTerms = [
        'phòng trọ', 'nhà trọ', 'căn hộ', 'chung cư mini', 'chung cư', 'gác lửng', 'gác xép',
        'ban công', 'khép kín', 'thú cưng', 'nuôi pet', 'máy giặt', 'điều hòa', 'máy lạnh',
        'nóng lạnh', 'bình nóng lạnh', 'tủ lạnh', 'thang máy', 'khóa vân tay', 'khóa từ',
        'an ninh', 'camera an ninh', 'nhà vệ sinh', 'wc riêng', 'vệ sinh khép kín', 'wc khép kín',
        'ở ghép', 'nấu ăn', 'kệ bếp', 'bếp riêng', 'sân phơi', 'chỗ để xe', 'bãi giữ xe',
        'cầu giấy', 'thanh xuân', 'đống đa', 'hai bà trưng', 'ba đình', 'tây hồ',
        'hoàng mai', 'hà đông', 'nam từ liêm', 'bắc từ liêm', 'xa đàn', 'nguyễn trãi',
        'quận 1', 'quận 3', 'quận 4', 'quận 7', 'quận 10', 'bình thạnh', 'tân bình', 'gò vấp', 'thủ đức',
        'phú mỹ hưng', 'làng đại học', 'điện biên phủ', 'nguyễn gia trí', 'xa lộ hà nội',
        'bách khoa', 'kinh tế quốc dân', 'xây dựng', 'ngoại thương', 'hutech', 'đại học quốc gia', 'đại học',
        'dưới 3 triệu', 'dưới 4 triệu', 'dưới 5 triệu', 'giá rẻ', 'chính chủ', 'ở ngay',
    ];

    public function __construct(?string $dictionaryPath = null)
    {
        if ($dictionaryPath) {
            $this->dictionaryPath = $dictionaryPath;
        } elseif (function_exists('resource_path') && file_exists(resource_path('data/dictionaries/Viet74K.txt'))) {
            $this->dictionaryPath = resource_path('data/dictionaries/Viet74K.txt');
        } else {
            $this->dictionaryPath = dirname(__DIR__, 2) . '/resources/data/dictionaries/Viet74K.txt';
        }

        try {
            $storage = function_exists('storage_path') ? storage_path('app/viet74k_map.cache') : null;
            $this->cacheFilePath = $storage ?: (dirname(__DIR__, 2) . '/storage/app/viet74k_map.cache');
        } catch (\Throwable $e) {
            $this->cacheFilePath = sys_get_temp_dir() . '/viet74k_map.cache';
        }
    }

    /**
     * Nạp từ điển Viet74K vào bộ nhớ (kết hợp Domain Terms và File Cache)
     */
    public function loadDictionary(): array
    {
        if (self::$cachedDictionary !== null) {
            return self::$cachedDictionary;
        }

        if (file_exists($this->cacheFilePath) && file_exists($this->dictionaryPath) && filemtime($this->cacheFilePath) >= filemtime($this->dictionaryPath)) {
            $data = @unserialize((string) file_get_contents($this->cacheFilePath));
            if (is_array($data) && !empty($data['by_len'])) {
                self::$cachedDictionary = $data;
                return self::$cachedDictionary;
            }
        }

        $map = [
            'domain' => [],
            'by_len' => [
                4 => [],
                3 => [],
                2 => [],
                1 => [],
            ],
            'normalized' => [],
        ];

        // Nạp domain terms với độ ưu tiên cao
        foreach ($this->domainTerms as $dTerm) {
            $low = mb_strtolower($dTerm, 'UTF-8');
            $noAcc = $this->removeAccents($low);
            $parts = preg_split('/\s+/', $low);
            $len = count($parts);

            $map['domain'][$low] = true;
            $map['domain'][$noAcc] = true;

            if ($len >= 1 && $len <= 4) {
                $map['by_len'][$len][$low] = true;
                $map['by_len'][$len][$noAcc] = true;
            }
        }

        if (file_exists($this->dictionaryPath)) {
            $handle = fopen($this->dictionaryPath, 'r');
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    $word = trim($line);
                    if ($word === '') {
                        continue;
                    }

                    $lower = mb_strtolower($word, 'UTF-8');
                    $noAccent = $this->removeAccents($lower);

                    $parts = preg_split('/\s+/', $lower);
                    $len = count($parts);

                    if ($len >= 1 && $len <= 4) {
                        $map['by_len'][$len][$lower] = true;
                        if ($noAccent !== $lower) {
                            $map['by_len'][$len][$noAccent] = true;
                        }
                    }

                    $map['normalized'][$noAccent] = $lower;
                }
                fclose($handle);
            }
        }

        @file_put_contents($this->cacheFilePath, serialize($map));

        self::$cachedDictionary = $map;
        return self::$cachedDictionary;
    }

    /**
     * Bỏ dấu tiếng Việt
     */
    public function removeAccents(string $text): string
    {
        $text = mb_strtolower(trim($text), 'UTF-8');
        $text = preg_replace('/[àáạảãâầấậẩẫăằắặẳẵ]/u', 'a', $text);
        $text = preg_replace('/[èéẹẻẽêềếệểễ]/u', 'e', $text);
        $text = preg_replace('/[ìíịỉĩ]/u', 'i', $text);
        $text = preg_replace('/[òóọỏõôồốộổỗơờớợởỡ]/u', 'o', $text);
        $text = preg_replace('/[ùúụủũưừứựửữ]/u', 'u', $text);
        $text = preg_replace('/[ỳýỵỷỹ]/u', 'y', $text);
        $text = preg_replace('/[đ]/u', 'd', $text);
        return $text;
    }

    /**
     * Chuẩn hoá chuỗi
     */
    public function cleanText(string $text): string
    {
        $clean = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
        return preg_replace('/\s+/', ' ', trim($clean));
    }

    /**
     * Tách từ tiếng Việt bằng thuật toán Maximal Matching thông minh
     */
    public function tokenize(string $text): array
    {
        $cleaned = $this->cleanText($text);
        if ($cleaned === '') {
            return [];
        }

        $dict = $this->loadDictionary();
        $domain = $dict['domain'] ?? [];
        $byLen = $dict['by_len'] ?? [];

        $words = preg_split('/\s+/', $cleaned);
        $count = count($words);
        $tokens = [];
        $i = 0;

        while ($i < $count) {
            $currentWordLower = mb_strtolower($words[$i], 'UTF-8');

            // 1. Kiểm tra nếu từ hiện tại là stop particle (ví dụ: 'có', 'và') mà từ tiếp theo bắt đầu cụm domain
            // thì ưu tiên nhường cho cụm domain phía sau
            if (in_array($currentWordLower, $this->stopParticles, true) && ($i + 1 < $count)) {
                $hasFollowDomain = false;
                for ($checkLen = min(3, $count - ($i + 1)); $checkLen >= 2; $checkLen--) {
                    $nextSlice = array_slice($words, $i + 1, $checkLen);
                    $nextPhrase = mb_strtolower(implode(' ', $nextSlice), 'UTF-8');
                    $nextPhraseNoAcc = $this->removeAccents($nextPhrase);
                    if (isset($domain[$nextPhrase]) || isset($domain[$nextPhraseNoAcc])) {
                        $hasFollowDomain = true;
                        break;
                    }
                }

                if ($hasFollowDomain) {
                    $tokens[] = $words[$i];
                    $i++;
                    continue;
                }
            }

            $matched = false;

            // 2. Ưu tiên tìm trong Domain Terms trước
            for ($len = min(4, $count - $i); $len >= 2; $len--) {
                $slice = array_slice($words, $i, $len);
                $phrase = mb_strtolower(implode(' ', $slice), 'UTF-8');
                $phraseNoAccent = $this->removeAccents($phrase);

                if (isset($domain[$phrase]) || isset($domain[$phraseNoAccent])) {
                    $tokens[] = implode(' ', $slice);
                    $i += $len;
                    $matched = true;
                    break;
                }
            }

            // 3. Tìm trong Viet74K tổng quát nếu chưa khớp domain
            if (!$matched) {
                for ($len = min(4, $count - $i); $len >= 2; $len--) {
                    $slice = array_slice($words, $i, $len);
                    $phrase = mb_strtolower(implode(' ', $slice), 'UTF-8');
                    $phraseNoAccent = $this->removeAccents($phrase);

                    // Tránh ghép từ với stop particle nếu có thể
                    if (in_array(mb_strtolower($slice[0], 'UTF-8'), $this->stopParticles, true) && $len > 1) {
                        continue;
                    }

                    if (isset($byLen[$len][$phrase]) || isset($byLen[$len][$phraseNoAccent])) {
                        $tokens[] = implode(' ', $slice);
                        $i += $len;
                        $matched = true;
                        break;
                    }
                }
            }

            if (!$matched) {
                $tokens[] = $words[$i];
                $i++;
            }
        }

        return $tokens;
    }

    /**
     * Trích xuất các cụm từ ghép có nghĩa trong câu truy vấn
     */
    public function extractCompoundWords(string $text): array
    {
        $tokens = $this->tokenize($text);
        $compounds = [];

        foreach ($tokens as $token) {
            if (str_contains($token, ' ')) {
                $compounds[] = $token;
            }
        }

        return array_values(array_unique($compounds));
    }

    /**
     * Kiểm tra một cụm từ có tồn tại trong từ điển không
     */
    public function hasWord(string $phrase): bool
    {
        $dict = $this->loadDictionary();
        $lower = mb_strtolower(trim($phrase), 'UTF-8');
        $noAccent = $this->removeAccents($lower);

        $words = preg_split('/\s+/', $lower);
        $len = count($words);

        if (isset($dict['domain'][$lower]) || isset($dict['domain'][$noAccent])) {
            return true;
        }

        if ($len >= 1 && $len <= 4 && isset($dict['by_len'][$len])) {
            return isset($dict['by_len'][$len][$lower]) || isset($dict['by_len'][$len][$noAccent]);
        }

        return false;
    }
}
