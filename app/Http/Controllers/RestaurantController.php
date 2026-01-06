<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\FoodItem;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function index()
    {
        $restaurants = Restaurant::with('foodItems')->get();
        return view('restaurants.index', compact('restaurants'));
    }

    public function show($id)
    {
        $restaurant = Restaurant::with('foodItems')->findOrFail($id);
        return view('restaurants.show', compact('restaurant'));
    }
}