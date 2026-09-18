<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\NotificationLog;
use App\Models\Resident;
use App\Models\Role;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UtilityRecord;
use App\Services\NotificationService;
use App\Services\SmsZaloService;
use App\Services\TelegramService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TelegramAndZaloNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('SQLite driver is required for isolated tests.');
        }

        parent::setUp();
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
    }

    public function test_telegram_service_handles_missing_credentials_gracefully(): void
    {
        config()->set('services.telegram.bot_token', null);
        config()->set('services.telegram.chat_id', null);

        $service = new TelegramService();
        $this->assertFalse($service->isConnected());

        $result = $service->sendMessage('Hello');
        $this->assertFalse($result['success']);
        $this->assertSame('missing_credentials', $result['error']);
    }

    public function test_telegram_service_sends_message_successfully(): void
    {
        config()->set('services.telegram.bot_token', '123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11');
        config()->set('services.telegram.chat_id', '-100987654321');

        Http::fake([
            'api.telegram.org/bot123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11/sendMessage' => Http::response([
                'ok' => true,
                'result' => [
                    'message_id' => 777,
                    'text' => 'Hello SmartRoom',
                ],
            ]),
        ]);

        $service = new TelegramService();
        $this->assertTrue($service->isConnected());

        $result = $service->sendMessage('Hello SmartRoom');
        $this->assertTrue($result['success']);
        $this->assertSame(777, $result['message_id']);
    }

    public function test_telegram_service_sends_payment_reminder_formatted_with_vietqr(): void
    {
        config()->set('services.telegram.bot_token', 'test_token');
        config()->set('services.telegram.chat_id', 'test_chat_id');

        Http::fake([
            'api.telegram.org/bottest_token/sendPhoto' => Http::response([
                'ok' => true,
                'result' => ['message_id' => 888],
            ]),
        ]);

        $service = new TelegramService();
        $result = $service->sendPaymentReminderFormatted(
            '102',
            'Tran Van B',
            '2026-06',
            3500000,
            ['room_price' => 3000000],
            'https://img.vietqr.io/image/test-qr.png'
        );

        $this->assertTrue($result['success']);
        $this->assertSame(888, $result['message_id']);
    }

    public function test_zalo_service_runs_in_sandbox_mode(): void
    {
        config()->set('services.zalo.mode', 'sandbox');

        $zaloService = new SmsZaloService();
        $result = $zaloService->sendPaymentReminder(
            '0912345678',
            'Le Thi C',
            '201',
            '2026-06',
            2800000,
            'https://img.vietqr.io/image/test-qr.png'
        );

        $this->assertTrue($result['success']);
        $this->assertSame('sandbox', $result['mode']);
        $this->assertSame('84912345678', $result['recipient']);
    }

    public function test_notification_service_dispatches_telegram_and_zalo_and_records_logs(): void
    {
        [$tenant, $record] = $this->createUnpaidUtilityRecord();

        config()->set('services.telegram.bot_token', 'mock_token');
        config()->set('services.telegram.chat_id', 'mock_group_id');
        config()->set('services.zalo.mode', 'sandbox');

        Http::fake([
            'api.telegram.org/*' => Http::response([
                'ok' => true,
                'result' => ['message_id' => 999],
            ]),
        ]);

        $logs = app(NotificationService::class)->sendPaymentReminders(
            $tenant->id,
            '2026-06',
            ['telegram', 'zalo']
        );

        $this->assertCount(2, $logs);

        // Kiểm tra log Telegram
        $telegramLog = NotificationLog::where('channel', 'telegram')->first();
        $this->assertNotNull($telegramLog);
        $this->assertSame('sent', $telegramLog->status);
        $this->assertSame('101', $telegramLog->meta['room_number']);
        $this->assertNotNull($telegramLog->meta['vietqr_url']);

        // Kiểm tra log Zalo
        $zaloLog = NotificationLog::where('channel', 'zalo')->first();
        $this->assertNotNull($zaloLog);
        $this->assertSame('sent', $zaloLog->status);
        $this->assertSame('0900000001', $zaloLog->recipient_contact);
    }

    public function test_rent_send_reminders_artisan_command(): void
    {
        [$tenant] = $this->createUnpaidUtilityRecord();

        config()->set('services.telegram.bot_token', 'mock_token');
        config()->set('services.telegram.chat_id', 'mock_group_id');

        Http::fake([
            'api.telegram.org/*' => Http::response([
                'ok' => true,
                'result' => ['message_id' => 111],
            ]),
        ]);

        $exitCode = Artisan::call('rent:send-reminders', [
            '--channels' => 'telegram,zalo',
        ]);

        $this->assertSame(0, $exitCode);
    }

    private function createUnpaidUtilityRecord(): array
    {
        $tenant = Tenant::create([
            'name' => 'SmartRoom Test Landlord',
            'email' => 'landlord-test@example.com',
            'bank_name' => 'MB',
            'bank_account_no' => '0987654321',
            'bank_account_name' => 'NGUYEN VAN CHU TRO',
        ]);

        $role = Role::firstOrCreate([
            'slug' => 'landlord',
        ], [
            'name' => 'Chu tro',
        ]);

        User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $role->id,
            'name' => 'Landlord Admin',
            'username' => 'landlord-admin-test',
            'email' => 'landlord-admin@example.com',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $building = Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Toa Nha Trung Tam',
            'address' => 'Cau Giay, Ha Noi',
        ]);

        $room = Room::create([
            'tenant_id' => $tenant->id,
            'building_id' => $building->id,
            'room_number' => '101',
            'floor' => 1,
            'status' => 'occupied',
            'price' => 2500000,
        ]);

        $resident = Resident::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'name' => 'Nguyen Van Cư Dân',
            'phone' => '0900000001',
            'email' => 'cudan-101@example.com',
            'start_date' => '2026-06-01',
            'status' => 'active',
        ]);

        $record = UtilityRecord::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'billing_month' => '2026-06',
            'old_electricity' => 100,
            'new_electricity' => 180,
            'old_water' => 20,
            'new_water' => 26,
            'electricity_price' => 3500,
            'water_price' => 15000,
            'status' => 'sent',
        ]);

        return [$tenant, $record, $room, $resident];
    }
}
