<?php

namespace App\Services\EInvoice\Drivers;

use App\Services\EInvoice\Contracts\EInvoiceDriverInterface;
use Illuminate\Support\Str;

class MockEInvoiceDriver implements EInvoiceDriverInterface
{
    public function issueInvoice(array $payload, array $config = []): array
    {
        $template = $config['invoice_template'] ?? '1'; // 1: Hóa đơn GTGT
        $year = date('y'); // "26" cho 2026
        $seriesSuffix = $config['invoice_series_suffix'] ?? 'TAA';
        $series = 'C' . $year . $seriesSuffix; // "C26TAA"
        $symbol = $template . $series; // "1C26TAA"

        // Sinh số hóa đơn dạng 8 chữ số
        $rawNumber = $payload['invoice_number'] ?? mt_rand(1, 999999);
        $invoiceNumber = str_pad((string) $rawNumber, 8, '0', STR_PAD_LEFT);

        // Sinh mã Cơ quan Thuế cấp: 34 ký tự (Chuẩn Tổng cục Thuế NĐ 123)
        // Cấu trúc: 00 (loại) + 79 (mã tỉnh TP.HCM/Hà Nội) + 26 (năm) + 28 ký tự ngẫu nhiên Hex/Upper
        $provinceCode = '79'; // Mã Cục thuế
        $randomHash = strtoupper(Str::random(28));
        $taxAuthorityCode = '00' . $provinceCode . $year . $randomHash; // 34 ký tự

        // Sinh mã tra cứu hóa đơn bí mật
        $lookupCode = 'SRM-' . strtoupper(Str::random(8));
        $lookupUrl = url('/tra-cuu-hoa-don?code=' . $lookupCode);

        // Sinh chữ ký số mô phỏng chuẩn X.509
        $signature = 'MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQC6Yx' . Str::random(40) . '==';

        // Tạo nội dung XML hợp chuẩn Quyết định 1450/QĐ-TCT của Tổng Cục Thuế
        $xmlContent = $this->generateTctXml($payload, [
            'symbol' => $symbol,
            'template' => $template,
            'series' => $series,
            'invoice_number' => $invoiceNumber,
            'tax_authority_code' => $taxAuthorityCode,
            'lookup_code' => $lookupCode,
            'signature' => $signature,
        ]);

        return [
            'success' => true,
            'provider' => 'mock',
            'provider_ref_id' => 'MOCK-' . Str::uuid(),
            'invoice_template' => $template,
            'invoice_series' => $series,
            'invoice_symbol' => $symbol,
            'invoice_number' => $invoiceNumber,
            'tax_authority_code' => $taxAuthorityCode,
            'lookup_code' => $lookupCode,
            'lookup_url' => $lookupUrl,
            'status' => 'issued',
            'cqt_status' => 'CQT_ACCEPTED',
            'cqt_message' => 'Cơ quan Thuế chấp nhận và cấp mã hóa đơn thành công theo Nghị định 123/2020/NĐ-CP',
            'digital_signature' => $signature,
            'xml_content' => $xmlContent,
            'pdf_url' => url("/smartroom/admin/einvoices/{$lookupCode}/pdf"),
            'error' => null,
        ];
    }

    public function cancelInvoice(string $invoiceNumber, string $reason, array $config = []): array
    {
        return [
            'success' => true,
            'message' => "Đã gửi thông báo hủy hóa đơn số {$invoiceNumber} tới Cơ quan Thuế. Lý do: {$reason}",
            'cqt_status' => 'CQT_CANCEL_ACCEPTED',
        ];
    }

    public function checkStatus(string $transactionId, array $config = []): array
    {
        return [
            'success' => true,
            'cqt_status' => 'CQT_ACCEPTED',
            'message' => 'Hóa đơn hợp lệ và đã có mã Cơ quan Thuế.',
        ];
    }

    public function getInvoiceXml(string $invoiceNumber, array $config = []): ?string
    {
        return null;
    }

    public function getInvoicePdf(string $invoiceNumber, array $config = []): ?string
    {
        return null;
    }

    /**
     * Tạo dữ liệu XML hóa đơn điện tử chuẩn QĐ 1450/QĐ-TCT
     */
    private function generateTctXml(array $payload, array $meta): string
    {
        $seller = $payload['seller'] ?? [];
        $buyer = $payload['buyer'] ?? [];
        $items = $payload['items'] ?? [];
        $tax = $payload['tax'] ?? [];

        $itemsXml = '';
        $stt = 1;
        foreach ($items as $item) {
            $name = htmlspecialchars($item['name'] ?? '', ENT_XML1);
            $unit = htmlspecialchars($item['unit'] ?? 'Lần', ENT_XML1);
            $qty = $item['quantity'] ?? 1;
            $price = $item['price'] ?? 0;
            $amount = $item['amount'] ?? 0;
            $vatRate = $item['vat_rate'] ?? 8;
            $vatAmount = $item['vat_amount'] ?? 0;

            $itemsXml .= "
        <HHDVu>
            <STT>{$stt}</STT>
            <THHDVu>{$name}</THHDVu>
            <DVTinh>{$unit}</DVTinh>
            <SLuong>{$qty}</SLuong>
            <DGia>{$price}</DGia>
            <ThTien>{$amount}</ThTien>
            <TSuat>{$vatRate}%</TSuat>
            <ThTienThue>{$vatAmount}</ThTienThue>
        </HHDVu>";
            $stt++;
        }

        $nowIso = now()->toIso8601String();
        $dateStr = now()->format('Y-m-d');

        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<HDon xmlns=\"http://laphoadon.gdt.gov.vn/2020/01/nd123\">
    <DLHDon Id=\"HD_{$meta['invoice_number']}\">
        <TTChung>
            <PBan>2.0.0</PBan>
            <THDon>HÓA ĐƠN GIÁ TRỊ GIA TĂNG</THDon>
            <KHMSHDon>{$meta['template']}</KHMSHDon>
            <KHHDon>{$meta['series']}</KHHDon>
            <SHDon>{$meta['invoice_number']}</SHDon>
            <NLap>{$dateStr}</NLap>
            <DVTTe>VND</DVTTe>
            <TGia>1</TGia>
            <HTTToan>TM/CK</HTTToan>
            <MSTCQT>{$meta['tax_authority_code']}</MSTCQT>
            <MTDieu>{$meta['lookup_code']}</MTDieu>
        </TTChung>
        <NBan>
            <Ten>" . htmlspecialchars($seller['name'] ?? '', ENT_XML1) . "</Ten>
            <MST>" . htmlspecialchars($seller['tax_code'] ?? '', ENT_XML1) . "</MST>
            <DChi>" . htmlspecialchars($seller['address'] ?? '', ENT_XML1) . "</DChi>
            <SDThoai>" . htmlspecialchars($seller['phone'] ?? '', ENT_XML1) . "</SDThoai>
            <STKNHang>" . htmlspecialchars($seller['bank_account'] ?? '', ENT_XML1) . "</STKNHang>
        </NBan>
        <NMua>
            <Ten>" . htmlspecialchars($buyer['name'] ?? '', ENT_XML1) . "</Ten>
            <MST>" . htmlspecialchars($buyer['tax_code'] ?? '', ENT_XML1) . "</MST>
            <DChi>" . htmlspecialchars($buyer['address'] ?? '', ENT_XML1) . "</DChi>
            <SDThoai>" . htmlspecialchars($buyer['phone'] ?? '', ENT_XML1) . "</SDThoai>
            <DCTDTu>" . htmlspecialchars($buyer['email'] ?? '', ENT_XML1) . "</DCTDTu>
            <CCCD>" . htmlspecialchars($buyer['id_card'] ?? '', ENT_XML1) . "</CCCD>
        </NMua>
        <DSHHDVu>{$itemsXml}
        </DSHHDVu>
        <TToan>
            <TgTCThue>{$payload['subtotal_amount']}</TgTCThue>
            <TgTThue>{$payload['tax_amount']}</TgTThue>
            <TgTTTBSo>{$payload['total_amount']}</TgTTTBSo>
            <TgTTTBChu>" . htmlspecialchars($payload['total_amount_in_words'] ?? '', ENT_XML1) . "</TgTTTBChu>
        </TToan>
    </DLHDon>
    <DSCKS>
        <NBan>
            <Signature xmlns=\"http://www.w3.org/2000/09/xmldsig#\">
                <SignedInfo>
                    <CanonicalizationMethod Algorithm=\"http://www.w3.org/TR/2001/REC-xml-c14n-20010315\"/>
                    <SignatureMethod Algorithm=\"http://www.w3.org/2001/04/xmldsig-more#rsa-sha256\"/>
                </SignedInfo>
                <SignatureValue>{$meta['signature']}</SignatureValue>
                <SigningTime>{$nowIso}</SigningTime>
            </Signature>
        </NBan>
    </DSCKS>
</HDon>";
    }
}
