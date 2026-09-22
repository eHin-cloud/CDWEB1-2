<?php

namespace App\Helpers;

class VietnameseCurrencyHelper
{
    private static array $digits = [
        'không', 'một', 'hai', 'ba', 'bốn', 'năm', 'sáu', 'bảy', 'tám', 'chín'
    ];

    /**
     * Đọc số tiền thành chữ bằng tiếng Việt chuẩn ngữ pháp kế toán.
     * Ví dụ: 3500000 => "Ba triệu năm trăm nghìn đồng"
     */
    public static function readMoney(int|float $amount): string
    {
        $amount = (int) round($amount);
        if ($amount === 0) {
            return 'Không đồng';
        }

        if ($amount < 0) {
            return 'Âm ' . mb_strtolower(self::readMoney(abs($amount)));
        }

        $strAmount = (string) $amount;
        $groups = [];
        $len = strlen($strAmount);

        // Chia số thành từng nhóm 3 chữ số từ phải sang trái
        while ($len > 0) {
            $take = min(3, $len);
            $start = $len - $take;
            $groups[] = substr($strAmount, $start, $take);
            $len -= $take;
        }

        $units = ['', 'nghìn', 'triệu', 'tỷ', 'nghìn tỷ', 'triệu tỷ'];
        $resultParts = [];

        for ($i = 0; $i < count($groups); $i++) {
            $groupVal = (int) $groups[$i];
            if ($groupVal > 0) {
                $isHighest = ($i === count($groups) - 1);
                $groupText = self::readThreeDigits($groups[$i], !$isHighest);
                $unit = $units[$i] ?? '';
                $resultParts[] = trim($groupText . ' ' . $unit);
            }
        }

        // Đảo ngược lại đúng thứ tự từ hàng cao nhất đến thấp nhất
        $result = implode(' ', array_reverse($resultParts));
        $result = trim(preg_replace('/\s+/', ' ', $result)) . ' đồng';

        // Viết hoa chữ cái đầu tiên
        return mb_strtoupper(mb_substr($result, 0, 1)) . mb_substr($result, 1);
    }

    /**
     * Đọc nhóm tối đa 3 chữ số
     */
    private static function readThreeDigits(string $digitsStr, bool $showHundredZero = true): string
    {
        $val = (int) $digitsStr;
        if ($val === 0) {
            return '';
        }

        $pad = str_pad($digitsStr, 3, '0', STR_PAD_LEFT);
        $h = (int) $pad[0];
        $t = (int) $pad[1];
        $u = (int) $pad[2];

        $res = '';

        // Hàng trăm
        if ($h > 0 || $showHundredZero) {
            $res .= self::$digits[$h] . ' trăm ';
        }

        // Hàng chục
        if ($t === 0) {
            if ($u > 0 && ($h > 0 || $showHundredZero)) {
                $res .= 'linh ';
            }
        } elseif ($t === 1) {
            $res .= 'mười ';
        } else {
            $res .= self::$digits[$t] . ' mươi ';
        }

        // Hàng đơn vị
        if ($u > 0) {
            if ($u === 1 && $t > 1) {
                $res .= 'mốt';
            } elseif ($u === 5 && $t > 0) {
                $res .= 'lăm';
            } else {
                $res .= self::$digits[$u];
            }
        }

        return trim($res);
    }
}
