<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Restaurant;
use App\Models\FoodItem;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only seed if no restaurants exist
        if (Restaurant::count() > 0) {
            return;
        }

        // Create sample restaurants
        $restaurants = [
            [
                'name' => 'Campus Cafe Main',
                'cuisine_type' => 'Asian Fusion',
                'operating_hours' => '7:00 AM - 10:00 PM',
                'description' => 'Main cafeteria serving delicious meals for students',
                'location' => 'Main Building, Ground Floor',
                'phone' => '+60123456789',
                'opening_hours' => '7:00 AM - 10:00 PM',
                'is_open' => true,
            ],
            [
                'name' => 'Quick Bites',
                'cuisine_type' => 'Fast Food',
                'operating_hours' => '8:00 AM - 8:00 PM',
                'description' => 'Fast food and snacks for busy students',
                'location' => 'Library Building, Level 2',
                'phone' => '+60123456790',
                'opening_hours' => '8:00 AM - 8:00 PM',
                'is_open' => true,
            ],
            [
                'name' => 'Healthy Corner',
                'cuisine_type' => 'Healthy',
                'operating_hours' => '6:00 AM - 9:00 PM',
                'description' => 'Healthy and nutritious food options',
                'location' => 'Sports Complex',
                'phone' => '+60123456791',
                'opening_hours' => '6:00 AM - 9:00 PM',
                'is_open' => true,
            ],
        ];

        foreach ($restaurants as $restaurantData) {
            $restaurant = Restaurant::create($restaurantData);

            // Create sample food items for each restaurant
            $foodItems = [
                [
                    'name' => 'Chicken Rice',
                    'description' => 'Steamed chicken with fragrant rice',
                    'price' => 8.50,
                    'available' => true,
                ],
                [
                    'name' => 'Nasi Lemak',
                    'description' => 'Coconut rice with sambal and fried chicken',
                    'price' => 7.00,
                    'available' => true,
                ],
                [
                    'name' => 'Burger',
                    'description' => 'Beef burger with fries',
                    'price' => 12.00,
                    'available' => true,
                ],
                [
                    'name' => 'Pizza Slice',
                    'description' => 'Cheese pizza slice',
                    'price' => 6.50,
                    'available' => true,
                ],
                [
                    'name' => 'Salad Bowl',
                    'description' => 'Fresh mixed salad with dressing',
                    'price' => 9.00,
                    'available' => true,
                ],
                [
                    'name' => 'Fruit Smoothie',
                    'description' => 'Mixed fruit smoothie',
                    'price' => 5.50,
                    'available' => true,
                ],
            ];

            foreach ($foodItems as $foodItemData) {
                $restaurant->foodItems()->create($foodItemData);
            }
        }
    }
}
