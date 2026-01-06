<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    DB::statement('ALTER TABLE cart_items ADD COLUMN cart_id BIGINT UNSIGNED NOT NULL AFTER id');
    echo 'Added cart_id column\n';
} catch (Exception $e) {
    echo 'Error adding cart_id: ' . $e->getMessage() . '\n';
}

try {
    DB::statement('ALTER TABLE cart_items ADD COLUMN food_item_id BIGINT UNSIGNED NOT NULL AFTER cart_id');
    echo 'Added food_item_id column\n';
} catch (Exception $e) {
    echo 'Error adding food_item_id: ' . $e->getMessage() . '\n';
}

try {
    DB::statement('ALTER TABLE cart_items ADD COLUMN quantity INT NOT NULL AFTER food_item_id');
    echo 'Added quantity column\n';
} catch (Exception $e) {
    echo 'Error adding quantity: ' . $e->getMessage() . '\n';
}

try {
    DB::statement('ALTER TABLE cart_items ADD COLUMN price DECIMAL(8,2) NOT NULL AFTER quantity');
    echo 'Added price column\n';
} catch (Exception $e) {
    echo 'Error adding price: ' . $e->getMessage() . '\n';
}

try {
    DB::statement('ALTER TABLE cart_items ADD CONSTRAINT fk_cart_items_cart_id FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE');
    echo 'Added foreign key for cart_id\n';
} catch (Exception $e) {
    echo 'Error adding cart_id FK: ' . $e->getMessage() . '\n';
}

try {
    DB::statement('ALTER TABLE cart_items ADD CONSTRAINT fk_cart_items_food_item_id FOREIGN KEY (food_item_id) REFERENCES food_items(id) ON DELETE CASCADE');
    echo 'Added foreign key for food_item_id\n';
} catch (Exception $e) {
    echo 'Error adding food_item_id FK: ' . $e->getMessage() . '\n';
}