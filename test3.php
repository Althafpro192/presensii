<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::where('role', 'admin_sekolah')->first();
if (!$user) {
    echo "No admin user found\n";
    exit;
}
auth()->login($user);

$request = Illuminate\Http\Request::create('/admin/students', 'GET');
$response = app()->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
if ($response->getStatusCode() != 200) {
    echo $response->getContent();
}
