<?php

namespace App\Jobs;

use App\Services\SmsZaloService;
use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSmsZaloNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $channel,
        protected ?string $recipient,
        protected string $message,
        protected array $extraData = []
    ) {}

    /**
     * Execute the job.
     */
    public function handle(SmsZaloService $smsZaloService, TelegramService $telegramService): void
    {
        Log::info("Executing background job SendSmsZaloNotification", [
            'channel' => $this->channel,
            'recipient' => $this->recipient,
        ]);

        if ($this->channel === 'sms' && $this->recipient) {
            $smsZaloService->sendSms($this->recipient, $this->message);
        } elseif ($this->channel === 'zalo' && $this->recipient) {
            $smsZaloService->sendZalo($this->recipient, $this->message, $this->extraData);
        } elseif ($this->channel === 'telegram') {
            if (!empty($this->extraData['photo_url'])) {
                $telegramService->sendPhoto($this->extraData['photo_url'], $this->message, $this->recipient);
            } else {
                $telegramService->sendMessage($this->message, $this->recipient);
            }
        }
    }
}
