<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;
use Illuminate\Support\Facades\Hash;

$seller = Student::create([
    'name' => 'Seller User',
    'email' => 'seller@campuscafe.com',
    'password' => Hash::make('seller123'),
    'phone' => '0123456789',
    'role' => 'seller'
]);

$seller->cart()->create(['total' => 0]);

echo 'Seller user created with ID: ' . $seller->id . PHP_EOL;
echo 'Email: seller@campuscafe.com' . PHP_EOL;
echo 'Password: seller123' . PHP_EOL;