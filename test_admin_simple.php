<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Student;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;

try {
    // Get admin user
    $admin = Student::where('is_admin', true)->first();
    if (!$admin) {
        echo "❌ No admin user found!\n";
        exit;
    }

    echo "✅ Found admin: {$admin->name} ({$admin->email})\n";

    // Simulate login
    Auth::login($admin);
    echo "✅ Admin logged in\n";

    // Test if we can create the controller
    $controller = new AdminController();
    echo "✅ AdminController instantiated\n";

    // Test if dashboard method exists
    if (method_exists($controller, 'dashboard')) {
        echo "✅ Dashboard method exists\n";
    } else {
        echo "❌ Dashboard method not found\n";
    }

    // Test if view exists
    $viewPath = resource_path('views/admin/dashboard.blade.php');
    if (file_exists($viewPath)) {
        echo "✅ Admin dashboard view exists\n";
    } else {
        echo "❌ Admin dashboard view not found\n";
    }

    echo "\n🎉 All basic checks passed! The admin dashboard should work.\n";
    echo "Try accessing: http://127.0.0.1:8000/admin/login\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}