<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;
use Illuminate\Support\Facades\Hash;

$admin = Student::create([
    'name' => 'Admin User',
    'email' => 'admin@campuscafe.com',
    'password' => Hash::make('admin123'),
    'phone' => '0123456789',
    'is_admin' => true
]);

$admin->cart()->create(['total' => 0]);

echo 'Admin user created with ID: ' . $admin->id . PHP_EOL;
echo 'Email: admin@campuscafe.com' . PHP_EOL;
echo 'Password: admin123' . PHP_EOL;