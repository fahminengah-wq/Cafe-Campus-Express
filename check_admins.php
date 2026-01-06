<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Student;

try {
    $admins = Student::where('is_admin', true)->get();
    echo "Admin users: " . $admins->count() . "\n";
    foreach($admins as $admin) {
        echo $admin->name . ' - ' . $admin->email . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}