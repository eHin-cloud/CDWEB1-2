<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$req = Illuminate\Http\Request::create('/login', 'POST', [
    'username' => 'superadmin',
    'password' => 'password',
]);

$resp = $kernel->handle($req);

echo "Status: " . $resp->getStatusCode() . "\n";
echo "TargetUrl: " . ($resp instanceof Illuminate\Http\RedirectResponse ? $resp->getTargetUrl() : 'N/A') . "\n";
if ($resp instanceof Illuminate\Http\RedirectResponse) {
    $session = $resp->getSession();
    if ($session) {
        echo "Session errors: " . json_encode($session->get('errors')?->all()) . "\n";
        echo "Session error message: " . $session->get('error') . "\n";
    }
}
