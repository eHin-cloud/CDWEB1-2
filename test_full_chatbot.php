<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$req = new \Illuminate\Http\Request();
$req->merge(['message' => 'Tìm giúp mình phòng trọ ở Thủ Đức dưới 3 triệu']);

$ctrl = app(\App\Http\Controllers\ChatbotController::class);

echo "Bắt đầu test toàn diện Chatbot Renty...\n";
$start = microtime(true);
$res = $ctrl->chat($req);
$time = round(microtime(true) - $start, 2);

$data = json_decode($res->getContent(), true);
echo "Thời gian xử lý: {$time}s\n";
echo "Thành công: " . ($data['success'] ? 'CÓ' : 'KHÔNG') . "\n";
echo "Dùng AI: " . ($data['used_ai'] ? 'CÓ (Gemini 4 Carbon)' : 'KHÔNG') . "\n";
echo "Số phòng gợi ý: " . count($data['rooms'] ?? []) . "\n";
echo "Nội dung AI trả lời:\n";
echo "----------------------------------------\n";
echo strip_tags(str_replace('<br>', "\n", $data['response'] ?? '')) . "\n";
echo "----------------------------------------\n";

