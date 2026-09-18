<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class TelegramService
{
    protected ?string $botToken;
    protected ?string $defaultChatId;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->defaultChatId = config('services.telegram.chat_id');
    }

    public function isConnected(): bool
    {
        return !empty($this->botToken) && !empty($this->defaultChatId);
    }

    /**
     * Gửi tin nhắn text qua Telegram Bot API.
     */
    public function sendMessage(string $message, ?string $chatId = null, string $parseMode = 'HTML'): array
    {
        $targetChatId = $chatId ?: $this->defaultChatId;

        if (empty($this->botToken) || empty($targetChatId)) {
            Log::warning('Telegram message skipped: missing bot_token or chat_id', [
                'has_token' => !empty($this->botToken),
                'chat_id' => $targetChatId,
            ]);

            return [
                'success' => false,
                'error' => 'missing_credentials',
                'message' => 'Chưa cấu hình TELEGRAM_BOT_TOKEN hoặc TELEGRAM_CHAT_ID trong .env',
            ];
        }

        try {
            $response = Http::timeout(10)
                ->post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                    'chat_id' => $targetChatId,
                    'text' => $message,
                    'parse_mode' => $parseMode,
                    'disable_web_page_preview' => false,
                ]);

            if ($response->successful() && $response->json('ok') === true) {
                Log::info('Telegram message sent successfully', [
                    'chat_id' => $targetChatId,
                    'message_id' => $response->json('result.message_id'),
                ]);

                return [
                    'success' => true,
                    'message_id' => $response->json('result.message_id'),
                    'response' => $response->json(),
                ];
            }

            Log::error('Telegram API error response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => 'api_error',
                'details' => $response->json() ?: $response->body(),
            ];
        } catch (Throwable $exception) {
            Log::error('Telegram sendMessage exception: ' . $exception->getMessage(), [
                'trace' => $exception->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => 'exception',
                'message' => $exception->getMessage(),
            ];
        }
    }

    /**
     * Gửi hình ảnh (kèm caption) qua Telegram Bot (ví dụ: ảnh QR thanh toán).
     */
    public function sendPhoto(string $photoUrl, string $caption = '', ?string $chatId = null, string $parseMode = 'HTML'): array
    {
        $targetChatId = $chatId ?: $this->defaultChatId;

        if (empty($this->botToken) || empty($targetChatId)) {
            return [
                'success' => false,
                'error' => 'missing_credentials',
            ];
        }

        try {
            $response = Http::timeout(15)
                ->post("https://api.telegram.org/bot{$this->botToken}/sendPhoto", [
                    'chat_id' => $targetChatId,
                    'photo' => $photoUrl,
                    'caption' => $caption,
                    'parse_mode' => $parseMode,
                ]);

            if ($response->successful() && $response->json('ok') === true) {
                return [
                    'success' => true,
                    'message_id' => $response->json('result.message_id'),
                ];
            }

            // Fallback: nếu sendPhoto lỗi (URL ảnh không truy cập được từ Telegram), gửi tin nhắn text thường
            return $this->sendMessage("📸 <b>Ảnh thanh toán:</b> {$photoUrl}\n\n" . $caption, $targetChatId, $parseMode);
        } catch (Throwable $exception) {
            Log::warning('Telegram sendPhoto failed, fallback to sendMessage: ' . $exception->getMessage());
            return $this->sendMessage("📸 <b>Ảnh thanh toán:</b> {$photoUrl}\n\n" . $caption, $targetChatId, $parseMode);
        }
    }

    /**
     * Định dạng và gửi mẫu nhắc nhở tiền phòng chuẩn HTML kèm VietQR.
     */
    public function sendPaymentReminderFormatted(
        string $roomNumber,
        string $residentName,
        string $billingMonth,
        int $totalAmount,
        array $details = [],
        ?string $vietQrUrl = null,
        ?string $chatId = null
    ): array {
        $month = explode('-', $billingMonth)[1] ?? $billingMonth;
        $totalFormatted = number_format($totalAmount) . ' VNĐ';

        $text = "🔔 <b>[SmartRoom] THÔNG BÁO HÓA ĐƠN THÁNG {$month}</b>\n";
        $text .= "━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "🏠 <b>Phòng:</b> {$roomNumber}\n";
        $text .= "👤 <b>Cư dân:</b> {$residentName}\n";

        if (!empty($details['room_price'])) {
            $text .= "💵 <b>Tiền phòng:</b> " . number_format($details['room_price']) . " đ\n";
        }
        if (isset($details['electricity_usage'], $details['electricity_cost'])) {
            $text .= "⚡ <b>Điện:</b> {$details['electricity_usage']} kWh (" . number_format($details['electricity_cost']) . " đ)\n";
        }
        if (isset($details['water_usage'], $details['water_cost'])) {
            $text .= "💧 <b>Nước:</b> {$details['water_usage']} m³ (" . number_format($details['water_cost']) . " đ)\n";
        }
        if (!empty($details['service_cost'])) {
            $text .= "🛠️ <b>Phí dịch vụ:</b> " . number_format($details['service_cost']) . " đ\n";
        }

        $text .= "━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "💰 <b>TỔNG CỘNG:</b> <code>{$totalFormatted}</code>\n";
        $text .= "⏰ <b>Hạn thanh toán:</b> Trước ngày 10 hàng tháng\n";

        if (!empty($vietQrUrl)) {
            $text .= "\n📲 <b>Quét VietQR chuyển khoản nhanh:</b>\n" . $vietQrUrl . "\n";
            return $this->sendPhoto($vietQrUrl, $text, $chatId);
        }

        $text .= "\n<i>Vui lòng thanh toán đúng hạn để đảm bảo dịch vụ vận hành tốt nhất. Trân trọng cảm ơn!</i>";

        return $this->sendMessage($text, $chatId);
    }
}
