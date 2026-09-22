<?php

namespace App\Services\EInvoice\Contracts;

interface EInvoiceDriverInterface
{
    /**
     * Phát hành hóa đơn điện tử có mã của Cơ quan Thuế (NĐ 123/2020/NĐ-CP & TT 78)
     *
     * @param array $payload Dữ liệu hóa đơn: người bán, người mua, items, thuế, tổng tiền
     * @param array $config Cấu hình tài khoản API của Tenant (chủ trọ)
     * @return array Kết quả trả về gồm số hóa đơn, ký hiệu, mã CQT 34 ký tự, mã tra cứu, XML, chữ ký số
     */
    public function issueInvoice(array $payload, array $config = []): array;

    /**
     * Hủy bỏ hóa đơn điện tử đã phát hành
     *
     * @param string $invoiceNumber Số hóa đơn
     * @param string $reason Lý do hủy
     * @param array $config Cấu hình API
     * @return array Kết quả hủy
     */
    public function cancelInvoice(string $invoiceNumber, string $reason, array $config = []): array;

    /**
     * Tra cứu trạng thái tiếp nhận và cấp mã của Cơ quan Thuế
     *
     * @param string $transactionId Mã giao dịch
     * @param array $config Cấu hình API
     * @return array Trạng thái CQT
     */
    public function checkStatus(string $transactionId, array $config = []): array;

    /**
     * Lấy tệp XML hóa đơn điện tử đã ký số chuẩn QĐ 1450/QĐ-TCT
     *
     * @param string $invoiceNumber Số hóa đơn
     * @param array $config Cấu hình API
     * @return string|null Nội dung XML
     */
    public function getInvoiceXml(string $invoiceNumber, array $config = []): ?string;

    /**
     * Lấy URL hoặc dữ liệu PDF bản thể hiện hóa đơn
     *
     * @param string $invoiceNumber Số hóa đơn
     * @param array $config Cấu hình API
     * @return string|null Đường dẫn hoặc base64 PDF
     */
    public function getInvoicePdf(string $invoiceNumber, array $config = []): ?string;
}
