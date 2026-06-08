<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$student = App\Models\Student::first();
if (!$student) {
    echo "No student found\n";
    exit;
}
$useCase = app()->make(App\UseCases\Admin\Student\UpdateStudentUseCase::class);
try {
    $useCase->execute($student, ['name' => $student->name . ' Updated']);
    echo "Success\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
