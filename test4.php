<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::where('role', 'admin_sekolah')->first();
auth()->login($user);

$student = App\Models\Student::first();

$request = Illuminate\Http\Request::create('/admin/students/' . $student->id, 'PUT', [
    'nis' => $student->nis,
    'name' => $student->name . ' Updated',
    'classroom_id' => $student->classroom_id,
    'rfid' => '12345678',
    'parent_phone' => '08123456789'
]);
$request->headers->set('Accept', 'application/json');

$response = app()->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
echo $response->getContent() . "\n";
