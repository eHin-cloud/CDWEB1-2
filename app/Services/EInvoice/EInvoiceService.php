<?php

namespace App\Services\EInvoice;

use App\Helpers\VietnameseCurrencyHelper;
use App\Jobs\SendSmsZaloNotification;
use App\Models\Bill;
use App\Models\ElectronicInvoice;
use App\Models\NotificationLog;
use App\Models\Resident;
use App\Models\Tenant;
use App\Models\UtilityRecord;
use App\Services\EInvoice\Contracts\EInvoiceDriverInterface;
use App\Services\EInvoice\Drivers\MisaMeInvoiceDriver;
use App\Services\EInvoice\Drivers\MockEInvoiceDriver;
use App\Services\EInvoice\Drivers\ViettelSinvoiceDriver;
use App\Services\EInvoice\Drivers\VnptInvoiceDriver;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class EInvoiceService
{
    /**
     * Khởi tạo Driver tương ứng với cấu hình nhà cung cấp
     */
    public function resolveDriver(string $provider): EInvoiceDriverInterface
    {
        return match (strtolower($provider)) {
            'misa' => new MisaMeInvoiceDriver(),
            'vnpt' => new VnptInvoiceDriver(),
            'viettel' => new ViettelSinvoiceDriver(),
            default => new MockEInvoiceDriver(),
        };
    }

    /**
     * Tự động phát hành HĐĐT từ đối tượng Bill (Hóa đơn phòng)
     */
    public function issueFromBill(Bill $bill): ?ElectronicInvoice
    {
        // 1. Kiểm tra nếu đã xuất hóa đơn rồi thì không xuất trùng
        $existing = ElectronicInvoice::where('bill_id', $bill->id)->first();
        if ($existing) {
            return $existing;
        }

        $tenant = $bill->tenant ?? Tenant::find($bill->tenant_id);
        if (!$tenant) {
            Log::warning("Cannot issue e-invoice: Tenant {$bill->tenant_id} not found.");
            return null;
        }

        $config = $tenant->einvoice_config ?? [];
        $providerName = $config['provider'] ?? 'mock';
        $driver = $this->resolveDriver($providerName);

        // 2. Thu thập thông tin khách thuê (Bên mua)
        $resident = Resident::where('room_id', $bill->room_id)->where('status', 'active')->first()
            ?? Resident::where('room_id', $bill->room_id)->latest()->first();

        // 3. Chuẩn bị danh mục hàng hóa / dịch vụ chi tiết
        $month = explode('-', $bill->billing_month)[1] ?? date('m');
        $year = explode('-', $bill->billing_month)[0] ?? date('Y');
        $taxRate = (float)($config['tax_rate'] ?? 8.0); // Thuế suất mặc định 8% theo NĐ giảm thuế

        $items = [];
        // Tiền phòng
        $items[] = [
            'name' => "Tiền thuê phòng {$bill->room->room_number} tháng {$month}/{$year}",
            'unit' => 'Tháng',
            'quantity' => 1,
            'price' => (int) $bill->room_price,
            'amount' => (int) $bill->room_price,
            'vat_rate' => $taxRate,
            'vat_amount' => (int) round(($bill->room_price * $taxRate) / 100),
        ];

        // Tiền điện
        if ($bill->electricity_usage > 0 || $bill->electricity_cost > 0) {
            $items[] = [
                'name' => "Tiền điện tiêu thụ tháng {$month}/{$year} ({$bill->electricity_usage} kWh)",
                'unit' => 'kWh',
                'quantity' => (int) $bill->electricity_usage,
                'price' => (int) ($bill->electricity_usage > 0 ? round($bill->electricity_cost / $bill->electricity_usage) : 3500),
                'amount' => (int) $bill->electricity_cost,
                'vat_rate' => $taxRate,
                'vat_amount' => (int) round(($bill->electricity_cost * $taxRate) / 100),
            ];
        }

        // Tiền nước
        if ($bill->water_usage > 0 || $bill->water_cost > 0) {
            $items[] = [
                'name' => "Tiền nước sinh hoạt tháng {$month}/{$year} ({$bill->water_usage} m³)",
                'unit' => 'm³',
                'quantity' => (int) $bill->water_usage,
                'price' => (int) ($bill->water_usage > 0 ? round($bill->water_cost / $bill->water_usage) : 15000),
                'amount' => (int) $bill->water_cost,
                'vat_rate' => $taxRate,
                'vat_amount' => (int) round(($bill->water_cost * $taxRate) / 100),
            ];
        }

        // Phí dịch vụ chung
        if ($bill->service_cost > 0) {
            $items[] = [
                'name' => "Phí quản lý dịch vụ chung tháng {$month}/{$year} (Vệ sinh, wifi, rác)",
                'unit' => 'Tháng',
                'quantity' => 1,
                'price' => (int) $bill->service_cost,
                'amount' => (int) $bill->service_cost,
                'vat_rate' => $taxRate,
                'vat_amount' => (int) round(($bill->service_cost * $taxRate) / 100),
            ];
        }

        // 4. Tính toán tổng trước thuế, tiền thuế GTGT và tổng sau thuế
        $subtotal = array_sum(array_column($items, 'amount'));
        $totalTax = array_sum(array_column($items, 'vat_amount'));
        $grandTotal = $subtotal + $totalTax;
        $inWords = VietnameseCurrencyHelper::readMoney($grandTotal);

        // 5. Chuẩn bị payload chuẩn Nghị định 123
        $payload = [
            'bill_id' => $bill->id,
            'seller' => [
                'name' => $config['company_name'] ?? $tenant->name,
                'tax_code' => $config['tax_code'] ?? '0101234567-001',
                'address' => $config['address'] ?? ($bill->room->building->address ?? 'Cơ sở lưu trú SmartRoom'),
                'phone' => $tenant->phone ?? '19008888',
                'bank_account' => ($tenant->bank_account_no ? "{$tenant->bank_account_no} ({$tenant->bank_name})" : '9999888889999 (MB)'),
            ],
            'buyer' => [
                'name' => $resident->name ?? 'Khách thuê phòng ' . $bill->room->room_number,
                'legal_name' => $resident->buyer_company_name ?? null,
                'tax_code' => $resident->tax_code ?? null,
                'id_card' => $resident->cccd ?? null,
                'address' => $bill->room->building->address ?? 'Hà Nội / TP.HCM',
                'phone' => $resident->phone ?? null,
                'email' => $resident->email ?? null,
            ],
            'items' => $items,
            'subtotal_amount' => $subtotal,
            'tax_rate' => $taxRate,
            'tax_amount' => $totalTax,
            'total_amount' => $grandTotal,
            'total_amount_in_words' => $inWords,
        ];

        // 6. Gọi Driver phát hành và cấp mã CQT
        $result = $driver->issueInvoice($payload, $config);

        if (!$result['success']) {
            Log::error("Failed to issue e-invoice for Bill #{$bill->id}: " . ($result['error'] ?? 'Unknown error'));
            return null;
        }

        // 7. Lưu bản ghi vào bảng electronic_invoices
        $invoice = ElectronicInvoice::create([
            'tenant_id' => $tenant->id,
            'room_id' => $bill->room_id,
            'resident_id' => $resident?->id,
            'bill_id' => $bill->id,
            'utility_record_id' => null,
            'invoice_template' => $result['invoice_template'] ?? '1',
            'invoice_series' => $result['invoice_series'] ?? 'C26TAA',
            'invoice_symbol' => $result['invoice_symbol'] ?? '1C26TAA',
            'invoice_number' => $result['invoice_number'],
            'tax_authority_code' => $result['tax_authority_code'],
            'lookup_code' => $result['lookup_code'],
            'lookup_url' => $result['lookup_url'],
            'provider' => $providerName,
            'provider_ref_id' => $result['provider_ref_id'],
            'status' => $result['status'] ?? 'issued',
            'cqt_status' => $result['cqt_status'] ?? 'CQT_ACCEPTED',
            'cqt_message' => $result['cqt_message'] ?? null,
            'seller_tax_code' => $payload['seller']['tax_code'],
            'seller_name' => $payload['seller']['name'],
            'seller_address' => $payload['seller']['address'],
            'seller_phone' => $payload['seller']['phone'],
            'seller_bank_account' => $payload['seller']['bank_account'],
            'buyer_name' => $payload['buyer']['name'],
            'buyer_legal_name' => $payload['buyer']['legal_name'],
            'buyer_tax_code' => $payload['buyer']['tax_code'],
            'buyer_id_card' => $payload['buyer']['id_card'],
            'buyer_address' => $payload['buyer']['address'],
            'buyer_phone' => $payload['buyer']['phone'],
            'buyer_email' => $payload['buyer']['email'],
            'subtotal_amount' => $subtotal,
            'tax_rate' => $taxRate,
            'tax_amount' => $totalTax,
            'total_amount' => $grandTotal,
            'total_amount_in_words' => $inWords,
            'items' => $items,
            'issue_date' => now(),
            'signed_at' => now(),
            'digital_signature' => $result['digital_signature'] ?? null,
            'xml_content' => $result['xml_content'] ?? null,
            'pdf_path' => $result['pdf_url'] ?? null,
        ]);

        // 8. Tự động gửi Email và Zalo nếu được kích hoạt
        $autoEmail = $config['auto_send_email'] ?? true;
        $autoZalo = $config['auto_send_zalo'] ?? true;

        if ($autoEmail || $autoZalo) {
            $this->sendInvoiceNotifications($invoice, $autoEmail, $autoZalo);
        }

        return $invoice;
    }

    /**
     * Tự động phát hành HĐĐT từ đối tượng UtilityRecord
     */
    public function issueFromUtility(UtilityRecord $record): ?ElectronicInvoice
    {
        $existing = ElectronicInvoice::where('utility_record_id', $record->id)->first();
        if ($existing) {
            return $existing;
        }

        $tenant = $record->tenant ?? Tenant::find($record->tenant_id);
        if (!$tenant) {
            return null;
        }

        $config = $tenant->einvoice_config ?? [];
        $providerName = $config['provider'] ?? 'mock';
        $driver = $this->resolveDriver($providerName);

        $resident = Resident::where('room_id', $record->room_id)->where('status', 'active')->first()
            ?? Resident::where('room_id', $record->room_id)->latest()->first();

        $month = explode('-', $record->billing_month)[1] ?? date('m');
        $year = explode('-', $record->billing_month)[0] ?? date('Y');
        $taxRate = (float)($config['tax_rate'] ?? 8.0);

        $elecUsage = max(0, (int) $record->new_electricity - (int) $record->old_electricity);
        $elecCost = $elecUsage * (int) $record->electricity_price;
        $waterUsage = max(0, (int) $record->new_water - (int) $record->old_water);
        $waterCost = $waterUsage * (int) $record->water_price;
        $roomPrice = (int) optional($record->room)->price;
        $serviceFee = 150000;

        $items = [];
        $items[] = [
            'name' => "Tiền thuê phòng {$record->room->room_number} tháng {$month}/{$year}",
            'unit' => 'Tháng',
            'quantity' => 1,
            'price' => $roomPrice,
            'amount' => $roomPrice,
            'vat_rate' => $taxRate,
            'vat_amount' => (int) round(($roomPrice * $taxRate) / 100),
        ];

        if ($elecUsage > 0) {
            $items[] = [
                'name' => "Tiền điện tiêu thụ tháng {$month}/{$year} ({$elecUsage} kWh)",
                'unit' => 'kWh',
                'quantity' => $elecUsage,
                'price' => (int) $record->electricity_price,
                'amount' => $elecCost,
                'vat_rate' => $taxRate,
                'vat_amount' => (int) round(($elecCost * $taxRate) / 100),
            ];
        }

        if ($waterUsage > 0) {
            $items[] = [
                'name' => "Tiền nước sinh hoạt tháng {$month}/{$year} ({$waterUsage} m³)",
                'unit' => 'm³',
                'quantity' => $waterUsage,
                'price' => (int) $record->water_price,
                'amount' => $waterCost,
                'vat_rate' => $taxRate,
                'vat_amount' => (int) round(($waterCost * $taxRate) / 100),
            ];
        }

        $items[] = [
            'name' => "Phí quản lý dịch vụ chung tháng {$month}/{$year}",
            'unit' => 'Tháng',
            'quantity' => 1,
            'price' => $serviceFee,
            'amount' => $serviceFee,
            'vat_rate' => $taxRate,
            'vat_amount' => (int) round(($serviceFee * $taxRate) / 100),
        ];

        $subtotal = array_sum(array_column($items, 'amount'));
        $totalTax = array_sum(array_column($items, 'vat_amount'));
        $grandTotal = $subtotal + $totalTax;
        $inWords = VietnameseCurrencyHelper::readMoney($grandTotal);

        $payload = [
            'utility_record_id' => $record->id,
            'seller' => [
                'name' => $config['company_name'] ?? $tenant->name,
                'tax_code' => $config['tax_code'] ?? '0101234567-001',
                'address' => $config['address'] ?? ($record->room->building->address ?? 'Cơ sở lưu trú SmartRoom'),
                'phone' => $tenant->phone ?? '19008888',
                'bank_account' => ($tenant->bank_account_no ? "{$tenant->bank_account_no} ({$tenant->bank_name})" : '9999888889999 (MB)'),
            ],
            'buyer' => [
                'name' => $resident->name ?? 'Khách thuê phòng ' . $record->room->room_number,
                'legal_name' => $resident->buyer_company_name ?? null,
                'tax_code' => $resident->tax_code ?? null,
                'id_card' => $resident->cccd ?? null,
                'address' => $record->room->building->address ?? 'Hà Nội / TP.HCM',
                'phone' => $resident->phone ?? null,
                'email' => $resident->email ?? null,
            ],
            'items' => $items,
            'subtotal_amount' => $subtotal,
            'tax_rate' => $taxRate,
            'tax_amount' => $totalTax,
            'total_amount' => $grandTotal,
            'total_amount_in_words' => $inWords,
        ];

        $result = $driver->issueInvoice($payload, $config);

        if (!$result['success']) {
            return null;
        }

        $invoice = ElectronicInvoice::create([
            'tenant_id' => $tenant->id,
            'room_id' => $record->room_id,
            'resident_id' => $resident?->id,
            'bill_id' => null,
            'utility_record_id' => $record->id,
            'invoice_template' => $result['invoice_template'] ?? '1',
            'invoice_series' => $result['invoice_series'] ?? 'C26TAA',
            'invoice_symbol' => $result['invoice_symbol'] ?? '1C26TAA',
            'invoice_number' => $result['invoice_number'],
            'tax_authority_code' => $result['tax_authority_code'],
            'lookup_code' => $result['lookup_code'],
            'lookup_url' => $result['lookup_url'],
            'provider' => $providerName,
            'provider_ref_id' => $result['provider_ref_id'],
            'status' => $result['status'] ?? 'issued',
            'cqt_status' => $result['cqt_status'] ?? 'CQT_ACCEPTED',
            'cqt_message' => $result['cqt_message'] ?? null,
            'seller_tax_code' => $payload['seller']['tax_code'],
            'seller_name' => $payload['seller']['name'],
            'seller_address' => $payload['seller']['address'],
            'seller_phone' => $payload['seller']['phone'],
            'seller_bank_account' => $payload['seller']['bank_account'],
            'buyer_name' => $payload['buyer']['name'],
            'buyer_legal_name' => $payload['buyer']['legal_name'],
            'buyer_tax_code' => $payload['buyer']['tax_code'],
            'buyer_id_card' => $payload['buyer']['id_card'],
            'buyer_address' => $payload['buyer']['address'],
            'buyer_phone' => $payload['buyer']['phone'],
            'buyer_email' => $payload['buyer']['email'],
            'subtotal_amount' => $subtotal,
            'tax_rate' => $taxRate,
            'tax_amount' => $totalTax,
            'total_amount' => $grandTotal,
            'total_amount_in_words' => $inWords,
            'items' => $items,
            'issue_date' => now(),
            'signed_at' => now(),
            'digital_signature' => $result['digital_signature'] ?? null,
            'xml_content' => $result['xml_content'] ?? null,
            'pdf_path' => $result['pdf_url'] ?? null,
        ]);

        $autoEmail = $config['auto_send_email'] ?? true;
        $autoZalo = $config['auto_send_zalo'] ?? true;

        if ($autoEmail || $autoZalo) {
            $this->sendInvoiceNotifications($invoice, $autoEmail, $autoZalo);
        }

        return $invoice;
    }

    /**
     * Gửi bản thể hiện điện tử tới Email và Zalo của khách thuê
     */
    public function sendInvoiceNotifications(ElectronicInvoice $invoice, bool $sendEmail = true, bool $sendZalo = true): array
    {
        $status = ['email' => false, 'zalo' => false];
        $lookupUrl = $invoice->lookup_url ?: url("/tra-cuu-hoa-don?code={$invoice->lookup_code}");
        $totalFormatted = number_format($invoice->total_amount, 0, ',', '.') . ' đ';

        // 1. Gửi Email thông báo hóa đơn điện tử
        if ($sendEmail && !empty($invoice->buyer_email)) {
            try {
                $emailSubject = "📜 [SmartRoom] Hóa Đơn Điện Tử Đã Cấp Mã CQT - Số {$invoice->invoice_number} (Ký hiệu {$invoice->invoice_symbol})";
                $emailBody = "Kính gửi Quý khách {$invoice->buyer_name},\n\n"
                    . "Cơ sở lưu trú {$invoice->seller_name} xin trân trọng gửi tới Quý khách bản thể hiện Hóa Đơn Điện Tử có mã của Cơ quan Thuế theo Nghị định 123/2020/NĐ-CP:\n\n"
                    . "--------------------------------------------------\n"
                    . "• Ký hiệu mẫu số & Hóa đơn: {$invoice->invoice_symbol}\n"
                    . "• Số hóa đơn: {$invoice->invoice_number}\n"
                    . "• Mã Cơ quan Thuế cấp: {$invoice->tax_authority_code}\n"
                    . "• Mã tra cứu hóa đơn: {$invoice->lookup_code}\n"
                    . "• Tổng tiền thanh toán: {$totalFormatted} ({$invoice->total_amount_in_words})\n"
                    . "• Trạng thái CQT: {$invoice->cqtLabel()}\n"
                    . "--------------------------------------------------\n\n"
                    . "Quý khách có thể xem và tải bản thể hiện PDF / tệp XML ký số hợp pháp tại đường dẫn sau:\n"
                    . "👉 {$lookupUrl}\n\n"
                    . "Trân trọng cảm ơn Quý khách!";

                Mail::raw($emailBody, function ($mail) use ($invoice, $emailSubject) {
                    $mail->to($invoice->buyer_email, $invoice->buyer_name)
                        ->subject($emailSubject);
                });

                $invoice->update(['sent_email_at' => now()]);
                $status['email'] = true;

                // Lưu NotificationLog
                NotificationLog::create([
                    'tenant_id' => $invoice->tenant_id,
                    'type' => 'einvoice_issued',
                    'channel' => 'email',
                    'recipient_name' => $invoice->buyer_name,
                    'recipient_contact' => $invoice->buyer_email,
                    'subject' => $emailSubject,
                    'message' => $emailBody,
                    'status' => 'sent',
                    'target_type' => ElectronicInvoice::class,
                    'target_id' => $invoice->id,
                    'meta' => [
                        'invoice_number' => $invoice->invoice_number,
                        'cqt_code' => $invoice->tax_authority_code,
                        'lookup_code' => $invoice->lookup_code,
                    ],
                    'sent_at' => now(),
                ]);
            } catch (Throwable $e) {
                Log::error("Failed to send e-invoice email to {$invoice->buyer_email}: " . $e->getMessage());
            }
        }

        // 2. Gửi tin nhắn Zalo (ZNS) qua Job nền SendSmsZaloNotification
        if ($sendZalo && !empty($invoice->buyer_phone)) {
            try {
                $zaloMessage = "📜 [SmartRoom] HÓA ĐƠN ĐIỆN TỬ CÓ MÃ CQT: Kính gửi {$invoice->buyer_name}, hóa đơn số {$invoice->invoice_number} (Ký hiệu {$invoice->invoice_symbol}) tổng tiền {$totalFormatted} đã được Cơ quan Thuế cấp mã: {$invoice->tax_authority_code}. Mã tra cứu: {$invoice->lookup_code}. Xem bản thể hiện chính thức tại: {$lookupUrl}";

                SendSmsZaloNotification::dispatch('zalo', $invoice->buyer_phone, $zaloMessage);

                $invoice->update(['sent_zalo_at' => now()]);
                $status['zalo'] = true;

                NotificationLog::create([
                    'tenant_id' => $invoice->tenant_id,
                    'type' => 'einvoice_issued',
                    'channel' => 'zalo',
                    'recipient_name' => $invoice->buyer_name,
                    'recipient_contact' => $invoice->buyer_phone,
                    'subject' => "Hóa đơn điện tử số {$invoice->invoice_number}",
                    'message' => $zaloMessage,
                    'status' => 'sent',
                    'target_type' => ElectronicInvoice::class,
                    'target_id' => $invoice->id,
                    'meta' => [
                        'invoice_number' => $invoice->invoice_number,
                        'cqt_code' => $invoice->tax_authority_code,
                        'lookup_code' => $invoice->lookup_code,
                        'channel' => 'zalo',
                    ],
                    'sent_at' => now(),
                ]);
            } catch (Throwable $e) {
                Log::error("Failed to dispatch e-invoice Zalo notification to {$invoice->buyer_phone}: " . $e->getMessage());
            }
        }

        return $status;
    }
}
