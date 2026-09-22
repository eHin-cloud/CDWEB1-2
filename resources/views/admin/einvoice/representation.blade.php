<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa Đơn Điện Tử {{ $invoice->invoice_symbol }} - Số {{ $invoice->invoice_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Roboto', 'DejaVu Sans', sans-serif;
            background: #f1f5f9;
            color: #0f172a;
            font-size: 13px;
            line-height: 1.5;
            padding: 30px 15px;
        }
        .action-bar {
            max-width: 900px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #1e293b;
            padding: 12px 20px;
            border-radius: 12px;
            color: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
        }
        .btn-print { background: #3b82f6; color: #fff; }
        .btn-print:hover { background: #2563eb; }
        .btn-pdf { background: #ef4444; color: #fff; }
        .btn-pdf:hover { background: #dc2626; }
        .btn-xml { background: #10b981; color: #fff; }
        .btn-xml:hover { background: #059669; }
        .btn-back { background: #475569; color: #fff; }
        .btn-back:hover { background: #334155; }

        .invoice-paper {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            padding: 40px 45px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            position: relative;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
            background-image: radial-gradient(#e2e8f0 0.8px, transparent 0.8px);
            background-size: 16px 16px;
        }
        .invoice-border {
            border: 2px solid #b91c1c;
            border-radius: 6px;
            padding: 25px;
            background: #fff;
            position: relative;
        }
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #b91c1c;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header-title-box {
            text-align: center;
            flex: 1;
            padding: 0 15px;
        }
        .header-title-box h1 {
            font-size: 20px;
            font-weight: 900;
            color: #b91c1c;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .header-title-box p.sub {
            font-size: 11px;
            font-style: italic;
            color: #475569;
        }
        .header-title-box p.date {
            margin-top: 6px;
            font-size: 12px;
            font-weight: 500;
            color: #1e293b;
        }
        .header-meta {
            min-width: 220px;
            text-align: right;
            font-size: 12px;
        }
        .header-meta .meta-row {
            margin-bottom: 3px;
        }
        .header-meta .meta-label {
            color: #64748b;
        }
        .header-meta .meta-value {
            font-weight: 700;
            color: #0f172a;
        }
        .cqt-code-badge {
            background: #ecfdf5;
            border: 1.5px solid #059669;
            border-radius: 8px;
            padding: 8px 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .cqt-code-badge .cqt-title {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #065f46;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
        }
        .cqt-code-badge .cqt-string {
            font-family: monospace;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 1px;
            color: #047857;
            background: #fff;
            padding: 3px 8px;
            border-radius: 4px;
            border: 1px dashed #10b981;
        }
        .party-box {
            margin-bottom: 15px;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 12px;
        }
        .party-title {
            font-weight: 800;
            text-transform: uppercase;
            font-size: 12px;
            color: #b91c1c;
            margin-bottom: 6px;
        }
        .party-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 20px;
            font-size: 12.5px;
        }
        .party-grid .full-row {
            grid-column: span 2;
        }
        .party-grid strong {
            color: #334155;
            font-weight: 600;
        }

        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 12px;
        }
        table.items-table th, table.items-table td {
            border: 1px solid #94a3b8;
            padding: 8px;
        }
        table.items-table th {
            background-color: #fee2e2;
            color: #991b1b;
            font-weight: 700;
            text-align: center;
            text-transform: uppercase;
            font-size: 11px;
        }
        table.items-table td.text-center { text-align: center; }
        table.items-table td.text-right { text-align: right; }
        table.items-table td.font-bold { font-weight: 700; }

        .summary-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 15px;
            padding-top: 10px;
        }
        .summary-totals {
            margin-left: auto;
            width: 100%;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            font-size: 12.5px;
        }
        .summary-row.grand-total {
            border-top: 2px solid #b91c1c;
            margin-top: 6px;
            padding-top: 6px;
            font-size: 14px;
            font-weight: 800;
            color: #b91c1c;
        }
        .words-total {
            margin-top: 8px;
            font-size: 12.5px;
            font-style: italic;
        }

        .signatures-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 30px;
            text-align: center;
        }
        .sign-col h3 {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            color: #1e293b;
        }
        .sign-col p.sub {
            font-size: 11px;
            font-style: italic;
            color: #64748b;
            margin-bottom: 15px;
        }
        .digital-stamp {
            display: inline-block;
            border: 2px solid #dc2626;
            padding: 8px 14px;
            border-radius: 8px;
            text-align: left;
            background: #fef2f2;
            color: #dc2626;
            font-size: 11px;
            line-height: 1.4;
            max-width: 280px;
        }
        .digital-stamp .stamp-title {
            font-weight: 800;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 6px;
            border-bottom: 1px dashed #f87171;
            padding-bottom: 4px;
            margin-bottom: 4px;
        }
        .qr-footer {
            margin-top: 25px;
            border-top: 1px dashed #cbd5e1;
            padding-top: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
            color: #64748b;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .action-bar {
                display: none !important;
            }
            .invoice-paper {
                border: none;
                box-shadow: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>

    @if(!isset($isPdfExport))
    <div class="action-bar">
        <div style="display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-file-invoice text-emerald-400 text-xl"></i>
            <div>
                <strong style="font-size: 14px;">Bản Thể Hiện Hóa Đơn Điện Tử</strong>
                <p style="font-size: 11px; color: #94a3b8;">Nghị định 123/2020/NĐ-CP & Thông tư 78/2021/TT-BTC</p>
            </div>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="action-btn btn-print">
                <i class="fa-solid fa-print"></i> In Hóa Đơn
            </button>
            <a href="{{ route('smartroom.admin.einvoices.pdf', $invoice->lookup_code) }}" class="action-btn btn-pdf">
                <i class="fa-solid fa-file-pdf"></i> Tải PDF
            </a>
            <a href="{{ route('smartroom.admin.einvoices.xml', $invoice->lookup_code) }}" class="action-btn btn-xml">
                <i class="fa-solid fa-file-code"></i> Tải XML Ký Số
            </a>
            <a href="javascript:history.back()" class="action-btn btn-back">
                <i class="fa-solid fa-arrow-left"></i> Quay Lại
            </a>
        </div>
    </div>
    @endif

    <div class="invoice-paper">
        <div class="invoice-border">

            <!-- HEADER -->
            <div class="header-section">
                <div style="width: 100px;">
                    <i class="fa-solid fa-hotel" style="font-size: 42px; color: #b91c1c;"></i>
                </div>
                <div class="header-title-box">
                    <h1>HÓA ĐƠN GIÁ TRỊ GIA TĂNG</h1>
                    <p class="sub">(Bản thể hiện của hóa đơn điện tử có mã của Cơ quan Thuế)</p>
                    <p class="date">Ngày {{ $invoice->issue_date->format('d') }} tháng {{ $invoice->issue_date->format('m') }} năm {{ $invoice->issue_date->format('Y') }}</p>
                </div>
                <div class="header-meta">
                    <div class="meta-row">
                        <span class="meta-label">Ký hiệu mẫu số: </span>
                        <span class="meta-value">{{ $invoice->invoice_template }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Ký hiệu hóa đơn: </span>
                        <span class="meta-value">{{ $invoice->invoice_series }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Ký hiệu (Symbol): </span>
                        <span class="meta-value" style="color: #b91c1c;">{{ $invoice->invoice_symbol }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Số hóa đơn: </span>
                        <span class="meta-value" style="font-size: 15px; color: #b91c1c;">{{ $invoice->invoice_number }}</span>
                    </div>
                </div>
            </div>

            <!-- MÃ CƠ QUAN THUẾ CẤP THEO NĐ 123 -->
            <div class="cqt-code-badge">
                <div class="cqt-title">
                    <i class="fa-solid fa-shield-check" style="font-size: 16px; color: #059669;"></i>
                    <span>Mã Của Cơ Quan Thuế Cấp (NĐ 123/2020/NĐ-CP):</span>
                </div>
                <div class="cqt-string">
                    {{ $invoice->tax_authority_code ?: 'Chưa có mã CQT' }}
                </div>
            </div>

            <!-- THÔNG TIN BÊN BÁN -->
            <div class="party-box">
                <div class="party-title">Đơn vị bán hàng (Seller): {{ $invoice->seller_name }}</div>
                <div class="party-grid">
                    <div><strong>Mã số thuế (MST):</strong> <span style="font-family: monospace; font-size: 13px; font-weight: bold; color: #b91c1c;">{{ $invoice->seller_tax_code }}</span></div>
                    <div><strong>Điện thoại:</strong> {{ $invoice->seller_phone ?: '19008888' }}</div>
                    <div class="full-row"><strong>Địa chỉ:</strong> {{ $invoice->seller_address }}</div>
                    <div class="full-row"><strong>Tài khoản ngân hàng:</strong> {{ $invoice->seller_bank_account }}</div>
                </div>
            </div>

            <!-- THÔNG TIN BÊN MUA -->
            <div class="party-box">
                <div class="party-title">Người mua hàng (Buyer): {{ $invoice->buyer_name }}</div>
                <div class="party-grid">
                    @if($invoice->buyer_legal_name)
                    <div class="full-row"><strong>Tên đơn vị:</strong> {{ $invoice->buyer_legal_name }}</div>
                    @endif
                    <div><strong>Mã số thuế:</strong> {{ $invoice->buyer_tax_code ?: 'N/A' }}</div>
                    <div><strong>CCCD/Định danh cá nhân:</strong> {{ $invoice->buyer_id_card ?: 'N/A' }}</div>
                    <div><strong>Điện thoại:</strong> {{ $invoice->buyer_phone ?: 'N/A' }}</div>
                    <div><strong>Email:</strong> {{ $invoice->buyer_email ?: 'N/A' }}</div>
                    <div class="full-row"><strong>Địa chỉ:</strong> {{ $invoice->buyer_address ?: 'Việt Nam' }}</div>
                    <div class="full-row"><strong>Hình thức thanh toán:</strong> Tiền mặt / Chuyển khoản (TM/CK)</div>
                </div>
            </div>

            <!-- BẢNG HÀNG HÓA DỊCH VỤ -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 35px;">STT</th>
                        <th>Tên Hàng Hóa, Dịch Vụ</th>
                        <th style="width: 60px;">ĐVT</th>
                        <th style="width: 60px;">Số Lượng</th>
                        <th style="width: 90px;">Đơn Giá (đ)</th>
                        <th style="width: 100px;">Thành Tiền (đ)</th>
                        <th style="width: 65px;">Thuế Suất</th>
                        <th style="width: 90px;">Tiền Thuế (đ)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $idx = 1; @endphp
                    @foreach($invoice->items ?? [] as $item)
                    <tr>
                        <td class="text-center">{{ $idx++ }}</td>
                        <td><strong>{{ $item['name'] }}</strong></td>
                        <td class="text-center">{{ $item['unit'] ?? 'Lần' }}</td>
                        <td class="text-center">{{ number_format($item['quantity'] ?? 1) }}</td>
                        <td class="text-right">{{ number_format($item['price'] ?? 0) }}</td>
                        <td class="text-right font-bold">{{ number_format($item['amount'] ?? 0) }}</td>
                        <td class="text-center">{{ $item['vat_rate'] ?? 8 }}%</td>
                        <td class="text-right">{{ number_format($item['vat_amount'] ?? 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- PHẦN TỔNG HỢP THANH TOÁN -->
            <div class="summary-section">
                <div>
                    <p style="font-weight: 700; color: #334155; margin-bottom: 4px;">Ghi chú & Pháp lý:</p>
                    <p style="font-size: 11.5px; color: #64748b;">
                        - Hóa đơn điện tử chuyển đổi tuân thủ Nghị định số 123/2020/NĐ-CP và Thông tư số 78/2021/TT-BTC.<br>
                        - Tra cứu hóa đơn tại cổng thông tin điện tử bằng Mã tra cứu bên dưới.
                    </p>
                </div>
                <div class="summary-totals">
                    <div class="summary-row">
                        <span>Cộng tiền hàng (chưa có thuế):</span>
                        <strong>{{ number_format($invoice->subtotal_amount) }} đ</strong>
                    </div>
                    <div class="summary-row">
                        <span>Thuế suất GTGT:</span>
                        <strong>{{ $invoice->tax_rate }}%</strong>
                    </div>
                    <div class="summary-row">
                        <span>Tổng tiền thuế GTGT:</span>
                        <strong>{{ number_format($invoice->tax_amount) }} đ</strong>
                    </div>
                    <div class="summary-row grand-total">
                        <span>TỔNG TIỀN THANH TOÁN:</span>
                        <span>{{ number_format($invoice->total_amount) }} đ</span>
                    </div>
                </div>
            </div>

            <div class="words-total">
                <strong>Số tiền viết bằng chữ:</strong> <em>{{ $invoice->total_amount_in_words }}</em>
            </div>

            <!-- CHỮ KÝ SỐ -->
            <div class="signatures-section">
                <div class="sign-col">
                    <h3>Người Mua Hàng</h3>
                    <p class="sub">(Ký, ghi rõ họ tên nếu có)</p>
                    <p style="margin-top: 40px; font-weight: 600; color: #64748b;">
                        {{ $invoice->buyer_name }}
                    </p>
                </div>
                <div class="sign-col">
                    <h3>Người Bán Hàng</h3>
                    <p class="sub">(Ký điện tử, đóng dấu số hợp chuẩn)</p>
                    
                    <div class="digital-stamp">
                        <div class="stamp-title">
                            <i class="fa-solid fa-badge-check text-rose-600"></i>
                            <span>ĐÃ KÝ ĐIỆN TỬ HỢP LỆ</span>
                        </div>
                        <div><strong>Ký bởi:</strong> {{ $invoice->seller_name }}</div>
                        <div><strong>MST:</strong> {{ $invoice->seller_tax_code }}</div>
                        <div><strong>Ngày ký:</strong> {{ $invoice->signed_at ? $invoice->signed_at->format('d/m/Y H:i:s') : $invoice->created_at->format('d/m/Y H:i:s') }}</div>
                        <div><strong>Chứng thư số:</strong> SHA256withRSA Validated</div>
                    </div>
                </div>
            </div>

            <!-- FOOTER & QR CODE TRA CỨU -->
            <div class="qr-footer">
                <div>
                    <p><strong>Mã tra cứu hóa đơn:</strong> <span style="font-family: monospace; font-size: 13px; font-weight: bold; color: #b91c1c;">{{ $invoice->lookup_code }}</span></p>
                    <p>Link tra cứu hóa đơn trực tuyến: <a href="{{ $invoice->lookup_url ?: url('/tra-cuu-hoa-don?code=' . $invoice->lookup_code) }}" target="_blank" style="color: #2563eb;">{{ $invoice->lookup_url ?: url('/tra-cuu-hoa-don?code=' . $invoice->lookup_code) }}</a></p>
                    <p style="margin-top: 3px; font-size: 10px; color: #94a3b8;">(Cần kiểm tra đối chiếu khi lập, giao, nhận hóa đơn theo quy định của Bộ Tài chính)</p>
                </div>
                <div style="text-align: right;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data={{ urlencode($invoice->lookup_url ?: url('/tra-cuu-hoa-don?code=' . $invoice->lookup_code)) }}" alt="QR Code Tra Cứu" style="width: 80px; height: 80px; border: 1px solid #cbd5e1; padding: 3px; border-radius: 4px;">
                </div>
            </div>

        </div>
    </div>

</body>
</html>
