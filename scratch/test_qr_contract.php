<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();
app()->instance('request', Illuminate\Http\Request::create('/'));

echo "--- KIEM TRA SINH MA VIETQR HOA DON ---\n";
$resident = \App\Models\Resident::with('user', 'room')->first();
$bill = \App\Models\UtilityRecord::where('room_id', $resident->room_id)->first();
if ($bill) {
    \Illuminate\Support\Facades\Auth::setUser($resident->user);
    $ctrl = app(\App\Http\Controllers\ResidentPortalController::class);
    $resp = $ctrl->billQr($bill->id);
    echo "QR Modal View Rendered: " . strlen($resp->render()) . " bytes [PASS]\n";
}

echo "\n--- KIEM TRA XUAT PDF HOP DONG ---\n";
$contract = \App\Models\Contract::first();
if ($contract) {
    $dashboardCtrl = app(\App\Http\Controllers\AdminDashboardController::class);
    $pdfResp = $dashboardCtrl->printContractPdf($contract->id);
    echo "Contract PDF Status: " . $pdfResp->getStatusCode() . " | Header Content-Type: " . $pdfResp->headers->get('Content-Type') . "\n";
}

echo "\n--- KIEM TRA XUAT TO KHAI TAM TRU CT01 (BO CONG AN) ---\n";
$resident = \App\Models\Resident::first();
if ($resident) {
    $dashboardCtrl = app(\App\Http\Controllers\AdminDashboardController::class);
    $ct01Resp = $dashboardCtrl->exportCt01($resident->id);
    echo "CT01 Form View Rendered: " . strlen($ct01Resp->render()) . " bytes [PASS]\n";
}
