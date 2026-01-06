<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\VoucherController;

Route::get('/fix-all-tables-properly', function() {
    try {
        $output = [];
        
        // 1. FIRST drop carts table (child table)
        \DB::statement("DROP TABLE IF EXISTS carts");
        $output[] = "✅ Dropped carts table";
        
        // 2. THEN drop students table (parent table)
        \DB::statement("DROP TABLE IF EXISTS students");
        $output[] = "✅ Dropped students table";
        
        // 3. Create students table
        \DB::statement("
            CREATE TABLE students (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                phone VARCHAR(20) NULL,
                password VARCHAR(255) NOT NULL,
                remember_token VARCHAR(100) NULL,
                created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
        $output[] = "✅ Created students table";
        
        // 4. Create carts table
        \DB::statement("
            CREATE TABLE carts (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                student_id BIGINT UNSIGNED NOT NULL,
                total DECIMAL(10,2) DEFAULT 0.00,
                created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
        $output[] = "✅ Created carts table";
        
        return implode("<br>", $output);
        
    } catch (\Exception $e) {
        return "❌ Error: " . $e->getMessage();
    }
});
Route::get('/debug-home', function() {
    // Test what HomeController returns
    $controller = new \App\Http\Controllers\HomeController();
    $response = $controller->index();
    
    // Get the view data
    $data = $response->getData();
    
    echo "<h2>HomeController Data:</h2>";
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    
    echo "<h2>Routes:</h2>";
    echo "<pre>";
    print_r(\Route::getRoutes()->getRoutes());
    echo "</pre>";
});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('search');

Route::get('/check-table-structure', function() {
    try {
        $columns = \DB::select('DESCRIBE students');
        echo "<h3>Students table structure:</h3>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>" . $column->Field . "</td>";
            echo "<td>" . $column->Type . "</td>";
            echo "<td>" . $column->Null . "</td>";
            echo "<td>" . $column->Key . "</td>";
            echo "<td>" . ($column->Default ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage();
    }
});

Route::get('/test-db-connection', function() {
    try {
        // Test connection
        \DB::connection()->getPdo();
        echo "✓ Database connected<br>";
        
        // Test if students table exists
        $tables = \DB::select('SHOW TABLES');
        echo "✓ Tables in database:<br>";
        foreach ($tables as $table) {
            foreach ($table as $value) {
                echo "- " . $value . "<br>";
            }
        }
        
    } catch (\Exception $e) {
        echo "✗ Database error: " . $e->getMessage();
    }
});

Route::get('/create-students-table', function() {
    try {
        \DB::statement("
            CREATE TABLE IF NOT EXISTS students (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                phone VARCHAR(20) NULL,
                password VARCHAR(255) NOT NULL,
                remember_token VARCHAR(100) NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
        
        return "Students table created successfully!";
        
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

Route::get('/create-carts-table', function() {
    try {
        \DB::statement("
            CREATE TABLE IF NOT EXISTS carts (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                student_id BIGINT UNSIGNED NOT NULL,
                total DECIMAL(10,2) DEFAULT 0.00,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
        
        return "Carts table created successfully!";
        
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Restaurant browsing (public)
Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');
Route::get('/restaurants/{id}', [RestaurantController::class, 'show'])->name('restaurants.show');

Route::get('/test-registration-form', function() {
    return view('auth.register-test');
});

// Add to routes/web.php
Route::get('/check-db', function() {
    try {
        // Check database connection
        \DB::connection()->getPdo();
        echo "✓ Database connected<br>";
        
        // Check if students table exists
        if (\Schema::hasTable('students')) {
            echo "✓ Students table exists<br>";
        } else {
            echo "✗ Students table does NOT exist<br>";
            echo "Run: php artisan migrate<br>";
        }
        
        // Check current tables
        $tables = \DB::select('SHOW TABLES');
        echo "<br>Available tables:<br>";
        foreach ($tables as $table) {
            foreach ($table as $value) {
                echo "- " . $value . "<br>";
            }
        }
        
    } catch (\Exception $e) {
        echo "✗ Database error: " . $e->getMessage();
    }
});


Route::get('/test-page', function() {
    return view('test');
});

// Admin auth routes
Route::get('/admin/login', [AdminController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'adminLogin']);

// Protected admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Restaurant management
    Route::get('/restaurants', [AdminController::class, 'restaurants'])->name('restaurants');
    Route::get('/restaurants/create', [AdminController::class, 'createRestaurant'])->name('restaurants.create');
    Route::post('/restaurants', [AdminController::class, 'storeRestaurant'])->name('restaurants.store');
    Route::get('/restaurants/{id}/edit', [AdminController::class, 'editRestaurant'])->name('restaurants.edit');
    Route::put('/restaurants/{id}', [AdminController::class, 'updateRestaurant'])->name('restaurants.update');
    Route::delete('/restaurants/{id}', [AdminController::class, 'deleteRestaurant'])->name('restaurants.delete');
    
    // Food item management with image upload
    Route::get('/food-items', [AdminController::class, 'foodItems'])->name('food-items');
    Route::get('/food-items/create', [AdminController::class, 'createFoodItem'])->name('food-items.create');
    Route::post('/food-items', [AdminController::class, 'storeFoodItem'])->name('food-items.store');
    Route::get('/food-items/{id}/edit', [AdminController::class, 'editFoodItem'])->name('food-items.edit');
    Route::put('/food-items/{id}', [AdminController::class, 'updateFoodItem'])->name('food-items.update');
    Route::delete('/food-items/{id}', [AdminController::class, 'deleteFoodItem'])->name('food-items.delete');
    
    // Image management for food items
    Route::post('/food-items/{id}/upload-image', [AdminController::class, 'uploadFoodItemImage'])->name('food-items.upload-image');
    
    // Order management
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::put('/orders/{order}/payment-status', [AdminController::class, 'updatePaymentStatus'])->name('orders.update-payment-status');
    
    // QR Code management
    Route::put('/update-qr-code', [AdminController::class, 'updateQrCode'])->name('update-qr-code');
});




// Admin routes
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Restaurant management
    Route::get('/restaurants', [AdminController::class, 'restaurants'])->name('restaurants');
    Route::get('/restaurants/create', [AdminController::class, 'createRestaurant'])->name('restaurants.create');
    Route::post('/restaurants', [AdminController::class, 'storeRestaurant'])->name('restaurants.store');
    Route::get('/restaurants/{id}/edit', [AdminController::class, 'editRestaurant'])->name('restaurants.edit');
    Route::put('/restaurants/{id}', [AdminController::class, 'updateRestaurant'])->name('restaurants.update');
    Route::delete('/restaurants/{id}', [AdminController::class, 'deleteRestaurant'])->name('restaurants.delete');
    
    // Food item management
    Route::get('/food-items', [AdminController::class, 'foodItems'])->name('food-items');
    Route::get('/restaurants/{restaurantId}/food-items', [AdminController::class, 'foodItems'])->name('restaurant.food-items');
    Route::get('/food-items/create', [AdminController::class, 'createFoodItem'])->name('food-items.create');
    Route::get('/restaurants/{restaurantId}/food-items/create', [AdminController::class, 'createFoodItem'])->name('restaurant.food-items.create');
    Route::post('/food-items', [AdminController::class, 'storeFoodItem'])->name('food-items.store');
    Route::get('/food-items/{id}/edit', [AdminController::class, 'editFoodItem'])->name('food-items.edit');
    Route::put('/food-items/{id}', [AdminController::class, 'updateFoodItem'])->name('food-items.update');
    Route::delete('/food-items/{id}', [AdminController::class, 'deleteFoodItem'])->name('food-items.delete');
});

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Protected routes
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
    
    // Orders (public confirmation)
    Route::get('/order/confirmation/{id}', [OrderController::class, 'confirmation'])->name('order.confirmation');
});

// Cart and Checkout routes
Route::middleware('auth')->group(function () {
    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/apply-voucher', [CartController::class, 'applyVoucher'])->name('cart.apply-voucher');
    Route::post('/cart/remove-voucher', [CartController::class, 'removeVoucher'])->name('cart.remove-voucher');
    
    // Orders
    Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::get('/order/qr-payment/{id}', [OrderController::class, 'qrPayment'])->name('order.qr-payment');
    Route::post('/order/complete-qr-payment/{id}', [OrderController::class, 'completeQrPayment'])->name('order.complete-qr-payment');
    Route::get('/orders', [OrderController::class, 'history'])->name('orders');
    Route::get('/orders/history', [OrderController::class, 'history'])->name('orders.history');
});

// Seller routes
Route::middleware(['auth'])->prefix('seller')->name('seller.')->group(function () {
    Route::get('/dashboard', [SellerController::class, 'dashboard'])->name('dashboard')->middleware('seller');
    Route::get('/orders', [SellerController::class, 'orders'])->name('orders')->middleware('seller');
    Route::put('/orders/{order}/status', [SellerController::class, 'updateOrderStatus'])->name('orders.update-status')->middleware('seller');

    // Restaurant management
    Route::get('/restaurants', [SellerController::class, 'restaurants'])->name('restaurants')->middleware('seller');
    Route::get('/restaurants/create', [SellerController::class, 'createRestaurant'])->name('restaurants.create')->middleware('seller');
    Route::post('/restaurants', [SellerController::class, 'storeRestaurant'])->name('restaurants.store')->middleware('seller');
    Route::get('/restaurants/{restaurant}/edit', [SellerController::class, 'editRestaurant'])->name('restaurants.edit')->middleware('seller');
    Route::put('/restaurants/{restaurant}', [SellerController::class, 'updateRestaurant'])->name('restaurants.update')->middleware('seller');

    // Food item management
    Route::get('/food-items', [SellerController::class, 'foodItems'])->name('food-items')->middleware('seller');
    Route::get('/food-items/create', [SellerController::class, 'createFoodItem'])->name('food-items.create')->middleware('seller');
    Route::post('/food-items', [SellerController::class, 'storeFoodItem'])->name('food-items.store')->middleware('seller');
    Route::get('/food-items/{foodItem}/edit', [SellerController::class, 'editFoodItem'])->name('food-items.edit')->middleware('seller');
    Route::put('/food-items/{foodItem}', [SellerController::class, 'updateFoodItem'])->name('food-items.update')->middleware('seller');
});

// Voucher routes
Route::middleware('auth')->group(function () {
    Route::get('/vouchers', [VoucherController::class, 'index'])->name('vouchers.index');
    Route::post('/vouchers/claim-first-order', [VoucherController::class, 'claimFirstOrderVoucher'])->name('vouchers.claim-first-order');
    Route::post('/vouchers/apply', [VoucherController::class, 'applyVoucher'])->name('vouchers.apply');
    Route::post('/vouchers/use', [VoucherController::class, 'useVoucher'])->name('vouchers.use');
});

