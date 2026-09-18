<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('rent:send-reminders {--channels=telegram,zalo : Danh sách kênh gửi (telegram,zalo,email,sms)}', function () {
    $this->info('Starting automatic rent reminders scan for unpaid bills...');

    $currentMonth = now()->format('Y-m');
    $tenantIds = \App\Models\Tenant::query()->pluck('id');

    if ($tenantIds->isEmpty()) {
        $this->info('No tenants found.');
        return;
    }

    $rawChannels = (string) $this->option('channels');
    $channels = array_filter(array_map('trim', explode(',', $rawChannels)));
    if (empty($channels)) {
        $channels = ['telegram', 'zalo'];
    }

    $this->info('Dispatching reminders via channels: ' . implode(', ', $channels));

    $sentCount = 0;
    foreach ($tenantIds as $tenantId) {
        $logs = app(\App\Services\NotificationService::class)
            ->sendPaymentReminders((int) $tenantId, $currentMonth, $channels);

        $sentCount += $logs->where('status', 'sent')->count();
    }

    if ($sentCount === 0) {
        $this->info("No unpaid bills found or reminders dispatched for {$currentMonth}.");
        return;
    }

    $this->info("Finished sending {$sentCount} reminders.");
})->purpose('Scan and send Telegram & Zalo rent payment reminders automatically on the 10th of each month for unpaid bills');
