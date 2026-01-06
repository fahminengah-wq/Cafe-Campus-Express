<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestSellerDashboard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-seller-dashboard';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Seller Dashboard Queries...');

        // Get a seller
        $seller = \App\Models\Student::where('role', 'seller')->first();
        if (!$seller) {
            $this->error('No seller found');
            return;
        }

        $this->info("Found seller: {$seller->name}");

        // Check if restaurant exists and assign it to seller if not assigned
        $restaurant = \App\Models\Restaurant::first();
        if ($restaurant && !$restaurant->seller_id) {
            $restaurant->seller_id = $seller->id;
            $restaurant->save();
            $this->info('Assigned restaurant to seller');
        }

        // Test restaurants relationship
        try {
            $restaurants = $seller->restaurants;
            $this->info("Seller has {$restaurants->count()} restaurants");
        } catch (\Exception $e) {
            $this->error("Error getting restaurants: {$e->getMessage()}");
        }

        // Test order queries
        try {
            $totalOrders = \App\Models\Order::whereHas('items.foodItem.restaurant', function($query) use ($seller) {
                $query->where('seller_id', $seller->id);
            })->count();
            $this->info("Total orders: {$totalOrders}");
        } catch (\Exception $e) {
            $this->error("Error getting total orders: {$e->getMessage()}");
        }

        $this->info('Test completed');
    }
}
