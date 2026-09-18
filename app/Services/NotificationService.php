<?php

namespace App\Services;

use App\Jobs\SendSmsZaloNotification;
use App\Models\Contract;
use App\Models\NotificationLog;
use App\Models\RoomEquipment;
use App\Models\Tenant;
use App\Models\UtilityRecord;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class NotificationService
{
    private const SERVICE_FEE = 150000;

    public const DEFAULT_CHANNELS = ['email', 'telegram', 'zalo', 'sms'];

    public function __construct(
        private readonly AiReminderService $aiReminderService,
        private readonly TelegramService $telegramService,
        private readonly SmsZaloService $smsZaloService
    ) {
    }

    public function sendPaymentReminders(int $tenantId, ?string $billingMonth = null, array $channels = self::DEFAULT_CHANNELS): Collection
    {
        $billingMonth ??= now()->format('Y-m');
        $tenant = Tenant::find($tenantId);

        return UtilityRecord::with(['room.residents' => fn ($query) => $query->where('status', 'active')])
            ->where('billing_month', $billingMonth)
            ->where('status', '!=', 'paid')
            ->whereHas('room', fn ($query) => $query->where('tenant_id', $tenantId))
            ->get()
            ->flatMap(function (UtilityRecord $record) use ($tenantId, $tenant, $billingMonth, $channels) {
                $room = $record->room;
                $resident = $room?->residents->first();

                if (!$room || !$resident) {
                    return collect();
                }

                if ($record->status !== 'overdue') {
                    $record->update(['status' => 'sent']);
                }

                $total = $this->utilityTotal($record);

                // Tạo VietQR URL nếu chủ nhà đã cấu hình ngân hàng
                $vietQrUrl = null;
                if ($tenant && $tenant->bank_name && $tenant->bank_account_no) {
                    $addInfo = "Thanh toan tien phong {$room->room_number} thang {$billingMonth}";
                    $vietQrUrl = "https://img.vietqr.io/image/{$tenant->bank_name}-{$tenant->bank_account_no}-compact.png?amount={$total}&addInfo=" . rawurlencode($addInfo) . "&accountName=" . rawurlencode($tenant->bank_account_name ?? 'CHU TRO');
                }

                $recipient = [
                    'name' => $resident->name,
                    'email' => $resident->email,
                    'phone' => $resident->phone,
                    'telegram_chat_id' => $resident->telegram_chat_id ?? $tenant?->telegram_chat_id ?? config('services.telegram.chat_id'),
                ];

                return collect($channels)
                    ->map(function (string $channel) use ($tenantId, $record, $room, $resident, $total, $recipient, $billingMonth, $vietQrUrl) {
                        $content = $this->aiReminderService->generatePaymentReminder($record, $room, $resident, $total, $channel);

                        return $this->send($tenantId, 'payment_reminder', $channel, $recipient, $content['subject'], $content['message'], UtilityRecord::class, $record->id, [
                            'room_number' => $room->room_number,
                            'billing_month' => $billingMonth,
                            'total_amount' => $total,
                            'vietqr_url' => $vietQrUrl,
                            'ai_generated' => $content['used_ai'],
                            'ai_fallback_reason' => $content['fallback_reason'],
                            'simulated' => false,
                        ]);
                    });
            })
            ->values();
    }

    public function sendContractExpiryReminders(int $tenantId, int $days = 30, array $channels = self::DEFAULT_CHANNELS): Collection
    {
        $today = Carbon::today();
        $limitDate = $today->copy()->addDays($days);
        $tenant = Tenant::find($tenantId);

        return Contract::with(['room', 'resident'])
            ->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->whereDate('end_date', '>=', $today)
            ->whereDate('end_date', '<=', $limitDate)
            ->orderBy('end_date')
            ->get()
            ->flatMap(function (Contract $contract) use ($tenantId, $tenant, $today, $channels) {
                $resident = $contract->resident;
                if (!$resident) {
                    return collect();
                }

                $endDate = Carbon::parse($contract->end_date);
                $subject = 'Nhac hop dong sap het han ' . $contract->contract_code;
                $message = 'Hop dong ' . $contract->contract_code . ' cua phong '
                    . ($contract->room->room_number ?? 'N/A') . ' se het han ngay '
                    . $endDate->format('d/m/Y') . ' (con ' . $today->diffInDays($endDate) . ' ngay).';

                return $this->sendToChannels($tenantId, 'contract_expiry', $channels, [
                    'name' => $resident->name,
                    'email' => $resident->email,
                    'phone' => $resident->phone,
                    'telegram_chat_id' => $resident->telegram_chat_id ?? $tenant?->telegram_chat_id ?? config('services.telegram.chat_id'),
                ], $subject, $message, Contract::class, $contract->id, [
                    'contract_code' => $contract->contract_code,
                    'end_date' => $endDate->toDateString(),
                    'simulated' => false,
                ]);
            })
            ->values();
    }

    public function sendMaintenanceReminders(int $tenantId, int $daysSinceAllocated = 90, array $channels = ['email', 'telegram']): Collection
    {
        $cutoff = now()->subDays($daysSinceAllocated);
        $tenant = Tenant::find($tenantId);

        return RoomEquipment::with(['room', 'equipment'])
            ->where('tenant_id', $tenantId)
            ->where('quantity', '>', 0)
            ->where(function ($query) use ($cutoff) {
                $query->whereNull('last_allocated_at')
                    ->orWhere('last_allocated_at', '<=', $cutoff);
            })
            ->orderBy('last_allocated_at')
            ->get()
            ->flatMap(function (RoomEquipment $allocation) use ($tenantId, $tenant, $channels, $daysSinceAllocated) {
                $subject = 'Nhac bao tri thiet bi phong ' . ($allocation->room->room_number ?? 'N/A');
                $message = 'Thiet bi ' . ($allocation->equipment->name ?? 'N/A') . ' tai phong '
                    . ($allocation->room->room_number ?? 'N/A') . ' can kiem tra bao tri dinh ky sau '
                    . $daysSinceAllocated . ' ngay su dung.';

                return $this->sendToChannels($tenantId, 'maintenance_reminder', $channels, [
                    'name' => 'Ban quan ly',
                    'email' => config('mail.from.address'),
                    'phone' => null,
                    'telegram_chat_id' => $tenant?->telegram_chat_id ?? config('services.telegram.chat_id'),
                ], $subject, $message, RoomEquipment::class, $allocation->id, [
                    'room_number' => $allocation->room->room_number ?? null,
                    'equipment_name' => $allocation->equipment->name ?? null,
                    'quantity' => $allocation->quantity,
                    'simulated' => false,
                ]);
            })
            ->values();
    }

    private function sendToChannels(
        int $tenantId,
        string $type,
        array $channels,
        array $recipient,
        string $subject,
        string $message,
        string $targetType,
        int $targetId,
        array $meta = []
    ): Collection {
        return collect($channels)
            ->map(fn ($channel) => $this->send($tenantId, $type, $channel, $recipient, $subject, $message, $targetType, $targetId, $meta));
    }

    private function send(
        int $tenantId,
        string $type,
        string $channel,
        array $recipient,
        string $subject,
        string $message,
        string $targetType,
        int $targetId,
        array $meta
    ): NotificationLog {
        $contact = match ($channel) {
            'email' => $recipient['email'] ?? null,
            'telegram' => $recipient['telegram_chat_id'] ?? config('services.telegram.chat_id'),
            default => $recipient['phone'] ?? null,
        };

        $status = $contact ? 'sent' : 'skipped';
        $error = null;

        if ($channel === 'email' && $contact) {
            try {
                Mail::raw($message, function ($mail) use ($contact, $recipient, $subject) {
                    $mail->to($contact, $recipient['name'] ?? null)
                        ->subject($subject);
                });
            } catch (Throwable $exception) {
                $status = 'failed';
                $error = $exception->getMessage();
            }
        } elseif ($channel === 'telegram' && $contact) {
            try {
                $photoUrl = $meta['vietqr_url'] ?? null;
                $telegramText = "<b>[SmartRoom - " . htmlspecialchars($subject) . "]</b>\n\n" . htmlspecialchars($message);

                if ($photoUrl) {
                    $result = $this->telegramService->sendPhoto($photoUrl, $telegramText, $contact);
                } else {
                    $result = $this->telegramService->sendMessage($telegramText, $contact);
                }

                if (!($result['success'] ?? false) && ($result['error'] ?? '') === 'api_error') {
                    $status = 'failed';
                    $error = json_encode($result['details'] ?? 'Lỗi gửi Telegram');
                }
            } catch (Throwable $exception) {
                $status = 'failed';
                $error = $exception->getMessage();
            }
        } elseif (in_array($channel, ['sms', 'zalo']) && $contact) {
            try {
                SendSmsZaloNotification::dispatch($channel, $contact, $message, [
                    'photo_url' => $meta['vietqr_url'] ?? null,
                    'customer_name' => $recipient['name'] ?? 'Cư dân',
                    'room_number' => $meta['room_number'] ?? '',
                    'billing_month' => $meta['billing_month'] ?? '',
                    'total_amount' => $meta['total_amount'] ?? 0,
                    'vietqr_url' => $meta['vietqr_url'] ?? null,
                ]);
            } catch (Throwable $exception) {
                $status = 'failed';
                $error = $exception->getMessage();
            }
        }

        Log::info('Notification dispatched', [
            'type' => $type,
            'channel' => $channel,
            'recipient' => $contact,
            'status' => $status,
            'subject' => $subject,
            'error' => $error,
        ]);

        return NotificationLog::create([
            'tenant_id' => $tenantId,
            'type' => $type,
            'channel' => $channel,
            'recipient_name' => $recipient['name'] ?? null,
            'recipient_contact' => $contact,
            'subject' => $subject,
            'message' => $message,
            'status' => $status,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'meta' => array_merge($meta, [
                'real_email' => $channel === 'email',
                'telegram_dispatched' => $channel === 'telegram',
                'simulated' => false,
                'error' => $error,
                'queued' => in_array($channel, ['sms', 'zalo']),
            ]),
            'sent_at' => $status === 'sent' ? now() : null,
        ]);
    }

    private function utilityTotal(UtilityRecord $record): int
    {
        $electricityUsage = max(0, (int) $record->new_electricity - (int) $record->old_electricity);
        $waterUsage = max(0, (int) $record->new_water - (int) $record->old_water);
        $roomAmount = (int) optional($record->room)->price;

        return $roomAmount
            + ($electricityUsage * (int) $record->electricity_price)
            + ($waterUsage * (int) $record->water_price)
            + self::SERVICE_FEE;
    }
}
