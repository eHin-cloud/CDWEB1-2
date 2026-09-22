<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Thêm cấu hình einvoice_config vào bảng tenants nếu chưa có
        if (Schema::hasTable('tenants') && !Schema::hasColumn('tenants', 'einvoice_config')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->json('einvoice_config')->nullable()->after('payment_gateway_config');
            });
        }

        // 2. Tạo bảng electronic_invoices lưu trữ Hóa đơn điện tử theo Nghị định 123/2020/NĐ-CP & Thông tư 78/2021/TT-BTC
        Schema::create('electronic_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->foreignId('resident_id')->nullable()->constrained('residents')->nullOnDelete();
            $table->foreignId('bill_id')->nullable()->constrained('bills')->nullOnDelete();
            $table->foreignId('utility_record_id')->nullable()->constrained('utility_records')->nullOnDelete();

            // Định danh hóa đơn theo NĐ 123/2020/NĐ-CP
            $table->string('invoice_template', 10)->default('1'); // Mẫu số 1: Hóa đơn GTGT, Mẫu số 2: Hóa đơn bán hàng
            $table->string('invoice_series', 20)->default('C26TAA'); // Ký hiệu (C26TAA)
            $table->string('invoice_symbol', 30)->default('1C26TAA'); // Ký hiệu mẫu số & ký hiệu hóa đơn
            $table->string('invoice_number', 20); // Số hóa đơn: 8 chữ số (ví dụ 00000001)
            $table->string('tax_authority_code', 50)->nullable()->index(); // Mã của Cơ quan Thuế cấp (34 ký tự alphanumeric)
            $table->string('lookup_code', 64)->unique(); // Mã tra cứu hóa đơn bí mật
            $table->string('lookup_url', 255)->nullable(); // Đường dẫn tra cứu hóa đơn

            // Nhà cung cấp giải pháp & Trạng thái CQT
            $table->string('provider', 30)->default('mock'); // misa, vnpt, viettel, mock
            $table->string('provider_ref_id', 100)->nullable(); // Transaction ID phía nhà cung cấp
            $table->string('status', 30)->default('issued'); // draft, processing, issued, rejected, cancelled, replaced
            $table->string('cqt_status', 50)->default('CQT_ACCEPTED'); // CQT_ACCEPTED, CQT_REJECTED, CQT_PENDING
            $table->text('cqt_message')->nullable(); // Thông báo phản hồi từ CQT / Cổng HĐĐT

            // Thông tin bên bán (Seller)
            $table->string('seller_tax_code', 30);
            $table->string('seller_name', 255);
            $table->string('seller_address', 255)->nullable();
            $table->string('seller_phone', 50)->nullable();
            $table->string('seller_bank_account', 100)->nullable();

            // Thông tin bên mua (Buyer - Khách thuê)
            $table->string('buyer_name', 255);
            $table->string('buyer_legal_name', 255)->nullable(); // Tên công ty xuất hóa đơn (nếu có)
            $table->string('buyer_tax_code', 30)->nullable();
            $table->string('buyer_id_card', 30)->nullable(); // CCCD / CMND
            $table->string('buyer_address', 255)->nullable();
            $table->string('buyer_phone', 50)->nullable();
            $table->string('buyer_email', 100)->nullable();

            // Tài chính & Thuế GTGT
            $table->unsignedBigInteger('subtotal_amount')->default(0); // Tổng tiền trước thuế
            $table->decimal('tax_rate', 5, 2)->default(8.00); // Thuế suất (%) hoặc 0 nếu KCT
            $table->unsignedBigInteger('tax_amount')->default(0); // Tiền thuế GTGT
            $table->unsignedBigInteger('total_amount')->default(0); // Tổng tiền thanh toán (bao gồm thuế)
            $table->string('total_amount_in_words', 255)->nullable(); // Bằng chữ tiếng Việt

            // Dòng hàng hóa dịch vụ chi tiết (JSON)
            $table->json('items')->nullable();

            // Chữ ký số và dữ liệu XML
            $table->timestamp('issue_date')->useCurrent();
            $table->timestamp('signed_at')->nullable();
            $table->text('digital_signature')->nullable(); // Chuỗi băm/token chữ ký số SHA256
            $table->longText('xml_content')->nullable(); // Dữ liệu XML ký số chuẩn QĐ 1450/QĐ-TCT
            $table->string('pdf_path', 255)->nullable();

            // Nhật ký gửi thông báo khách thuê
            $table->timestamp('sent_email_at')->nullable();
            $table->timestamp('sent_zalo_at')->nullable();

            $table->timestamps();

            // Index tối ưu tra cứu
            $table->index(['tenant_id', 'status']);
            $table->index(['invoice_series', 'invoice_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electronic_invoices');

        if (Schema::hasTable('tenants') && Schema::hasColumn('tenants', 'einvoice_config')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->dropColumn('einvoice_config');
            });
        }
    }
};
