<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\FoodItem;

class HomeController extends Controller
{
    public function index()
    {
        // Get real restaurants from database
        $restaurants = Restaurant::with('foodItems')->get();

        // Get featured food items for carousel (limit to 10 most recent)
        $featuredFoodItems = FoodItem::with('restaurant')
            ->where('available', true)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $promotions = [
            [
                'title' => '50% Off First Order',
                'description' => 'Get amazing discounts on your first order!',
                'color' => 'from-yellow-400 to-orange-500',
            ],
        ];

        return view('home', compact('restaurants', 'promotions', 'featuredFoodItems'));
    }
    
    public function search(Request $request)
    {
        $query = $request->input('q', '');

        if (empty($query)) {
            return view('search', ['results' => collect(), 'query' => $query]);
        }

        // Search in restaurants
        $restaurants = Restaurant::where('name', 'LIKE', "%{$query}%")
            ->orWhere('cuisine_type', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->get();

        // Search in food items
        $foodItems = FoodItem::with('restaurant')
            ->where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->where('available', true)
            ->get();

        $results = [
            'restaurants' => $restaurants,
            'foodItems' => $foodItems,
        ];

        return view('search', compact('results', 'query'));
    }
}