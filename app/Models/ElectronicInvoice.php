<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ElectronicInvoice extends Model
{
    use HasFactory;

    protected $table = 'electronic_invoices';

    protected $fillable = [
        'tenant_id',
        'room_id',
        'resident_id',
        'bill_id',
        'utility_record_id',
        'invoice_template',
        'invoice_series',
        'invoice_symbol',
        'invoice_number',
        'tax_authority_code',
        'lookup_code',
        'lookup_url',
        'provider',
        'provider_ref_id',
        'status',
        'cqt_status',
        'cqt_message',
        'seller_tax_code',
        'seller_name',
        'seller_address',
        'seller_phone',
        'seller_bank_account',
        'buyer_name',
        'buyer_legal_name',
        'buyer_tax_code',
        'buyer_id_card',
        'buyer_address',
        'buyer_phone',
        'buyer_email',
        'subtotal_amount',
        'tax_rate',
        'tax_amount',
        'total_amount',
        'total_amount_in_words',
        'items',
        'issue_date',
        'signed_at',
        'digital_signature',
        'xml_content',
        'pdf_path',
        'sent_email_at',
        'sent_zalo_at',
    ];

    protected $casts = [
        'items' => 'array',
        'tax_rate' => 'float',
        'subtotal_amount' => 'integer',
        'tax_amount' => 'integer',
        'total_amount' => 'integer',
        'issue_date' => 'datetime',
        'signed_at' => 'datetime',
        'sent_email_at' => 'datetime',
        'sent_zalo_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function utilityRecord(): BelongsTo
    {
        return $this->belongsTo(UtilityRecord::class);
    }

    public function isAcceptedByCqt(): bool
    {
        return $this->cqt_status === 'CQT_ACCEPTED' && !empty($this->tax_authority_code);
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'issued' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
            'processing' => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
            'rejected' => 'bg-rose-500/10 text-rose-400 border border-rose-500/20',
            'cancelled' => 'bg-slate-700/50 text-slate-400 border border-slate-600/30',
            default => 'bg-blue-500/10 text-blue-400 border border-blue-500/20',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'issued' => 'Đã phát hành',
            'processing' => 'Đang xử lý CQT',
            'rejected' => 'Bị từ chối',
            'cancelled' => 'Đã hủy',
            'replaced' => 'Bị thay thế',
            default => 'Bản nháp',
        };
    }

    public function cqtBadgeClass(): string
    {
        return match ($this->cqt_status) {
            'CQT_ACCEPTED' => 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30',
            'CQT_REJECTED' => 'bg-rose-500/15 text-rose-400 border border-rose-500/30',
            'CQT_PENDING' => 'bg-amber-500/15 text-amber-400 border border-amber-500/30',
            default => 'bg-slate-700 text-slate-400',
        };
    }

    public function cqtLabel(): string
    {
        return match ($this->cqt_status) {
            'CQT_ACCEPTED' => 'CQT Đã Cấp Mã Hợp Lệ',
            'CQT_REJECTED' => 'CQT Từ Chối Cấp Mã',
            'CQT_PENDING' => 'Đang Chờ Mã CQT',
            default => 'Chưa Gửi CQT',
        };
    }

    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total_amount, 0, ',', '.') . ' đ';
    }

    public function getFormattedTaxAttribute(): string
    {
        return number_format($this->tax_amount, 0, ',', '.') . ' đ';
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return number_format($this->subtotal_amount, 0, ',', '.') . ' đ';
    }
}
