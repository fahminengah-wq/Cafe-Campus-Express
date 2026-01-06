<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

// Simulate admin login
$admin = Student::where('is_admin', true)->first();
if (!$admin) {
    echo "No admin user found!\n";
    exit;
}

echo "Found admin: {$admin->name}\n";

// Simulate authentication
Auth::login($admin);

echo "Admin logged in\n";

// Test the admin dashboard route
try {
    $request = Request::create('/admin/dashboard', 'GET');
    $response = Route::dispatch($request);

    echo "Dashboard response status: " . $response->getStatusCode() . "\n";

    if ($response->getStatusCode() == 200) {
        echo "Dashboard loads successfully!\n";
    } else {
        echo "Dashboard failed to load\n";
        echo "Response: " . $response->getContent() . "\n";
    }
} catch (Exception $e) {
    echo "Error accessing dashboard: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}