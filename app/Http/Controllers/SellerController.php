<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Restaurant;
use App\Models\FoodItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerController extends Controller
{
    public function __construct()
    {
        // Middleware is applied at route level
    }

    public function dashboard()
    {
        $seller = Auth::user();
        $restaurants = $seller->restaurants;

        $totalOrders = Order::whereHas('items.foodItem.restaurant', function($query) use ($seller) {
            $query->where('seller_id', $seller->id);
        })->count();

        $pendingOrders = Order::whereHas('items.foodItem.restaurant', function($query) use ($seller) {
            $query->where('seller_id', $seller->id);
        })->where('seller_status', 'pending')->count();

        $preparingOrders = Order::whereHas('items.foodItem.restaurant', function($query) use ($seller) {
            $query->where('seller_id', $seller->id);
        })->where('seller_status', 'preparing')->count();

        $readyOrders = Order::whereHas('items.foodItem.restaurant', function($query) use ($seller) {
            $query->where('seller_id', $seller->id);
        })->where('seller_status', 'ready')->count();

        $totalRevenue = Order::whereHas('items.foodItem.restaurant', function($query) use ($seller) {
            $query->where('seller_id', $seller->id);
        })->where('status', 'completed')->sum('total');

        $orders = Order::with(['student', 'items.foodItem'])
            ->whereHas('items.foodItem.restaurant', function($query) use ($seller) {
                $query->where('seller_id', $seller->id);
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('seller.dashboard', compact(
            'restaurants',
            'totalOrders',
            'pendingOrders',
            'preparingOrders',
            'readyOrders',
            'totalRevenue',
            'orders'
        ));
    }

    public function orders()
    {
        $seller = Auth::user();
        $orders = Order::with(['student', 'items.foodItem'])
            ->whereHas('items.foodItem.restaurant', function($query) use ($seller) {
                $query->where('seller_id', $seller->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('seller.orders.index', compact('orders'));
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'seller_status' => 'required|in:pending,preparing,ready,completed',
            'seller_notes' => 'nullable|string|max:500',
        ]);

        $seller = Auth::user();

        // Check if the order belongs to this seller
        $belongsToSeller = $order->items()->whereHas('foodItem.restaurant', function($query) use ($seller) {
            $query->where('seller_id', $seller->id);
        })->exists();

        if (!$belongsToSeller) {
            return redirect()->back()->with('error', 'You can only update orders for your restaurants.');
        }

        $order->update([
            'seller_status' => $request->seller_status,
            'seller_notes' => $request->seller_notes,
        ]);

        // Update the general order status based on seller status
        $statusMapping = [
            'pending' => 'pending',
            'preparing' => 'confirmed',
            'ready' => 'ready',
            'completed' => 'completed',
        ];

        if (isset($statusMapping[$request->seller_status])) {
            $order->update(['status' => $statusMapping[$request->seller_status]]);
        }

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }

    public function restaurants()
    {
        $seller = Auth::user();
        $restaurants = $seller->restaurants()->with('foodItems')->get();

        return view('seller.restaurants.index', compact('restaurants'));
    }

    public function createRestaurant()
    {
        return view('seller.restaurants.create');
    }

    public function storeRestaurant(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'cuisine_type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'operating_hours' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $seller = Auth::user();

        $data = [
            'name' => $request->name,
            'cuisine_type' => $request->cuisine_type,
            'description' => $request->description,
            'operating_hours' => $request->operating_hours,
            'seller_id' => $seller->id,
        ];

        if ($request->hasFile('image')) {
            $data['image_url'] = $request->file('image')->store('restaurants', 'public');
        }

        Restaurant::create($data);

        return redirect()->route('seller.restaurants')->with('success', 'Restaurant created successfully.');
    }

    public function editRestaurant(Restaurant $restaurant)
    {
        $seller = Auth::user();

        if ($restaurant->seller_id !== $seller->id) {
            return redirect()->route('seller.dashboard')->with('error', 'You can only edit your own restaurants.');
        }

        return view('seller.restaurants.edit', compact('restaurant'));
    }

    public function updateRestaurant(Request $request, Restaurant $restaurant)
    {
        $seller = Auth::user();

        if ($restaurant->seller_id !== $seller->id) {
            return redirect()->route('seller.dashboard')->with('error', 'You can only edit your own restaurants.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'cuisine_type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'operating_hours' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $data = [
            'name' => $request->name,
            'cuisine_type' => $request->cuisine_type,
            'description' => $request->description,
            'operating_hours' => $request->operating_hours,
        ];

        if ($request->hasFile('image')) {
            $data['image_url'] = $request->file('image')->store('restaurants', 'public');
        }

        $restaurant->update($data);

        return redirect()->route('seller.restaurants')->with('success', 'Restaurant updated successfully.');
    }

    public function foodItems(Request $request)
    {
        $seller = Auth::user();
        
        $query = FoodItem::whereHas('restaurant', function($query) use ($seller) {
            $query->where('seller_id', $seller->id);
        })->with('restaurant');

        if ($request->has('restaurant_id') && $request->restaurant_id) {
            $query->where('restaurant_id', $request->restaurant_id);
        }

        $foodItems = $query->get();
        $restaurants = $seller->restaurants;

        return view('seller.food-items.index', compact('foodItems', 'restaurants'));
    }

    public function createFoodItem()
    {
        $seller = Auth::user();
        $restaurants = $seller->restaurants;

        return view('seller.food-items.create', compact('restaurants'));
    }

    public function storeFoodItem(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'available' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $seller = Auth::user();

        // Check if the restaurant belongs to this seller
        $restaurant = Restaurant::find($request->restaurant_id);
        if ($restaurant->seller_id !== $seller->id) {
            return redirect()->back()->with('error', 'You can only add food items to your own restaurants.');
        }

        $data = [
            'restaurant_id' => $request->restaurant_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'available' => $request->has('available') ? true : false,
        ];

        if ($request->hasFile('image')) {
            $data['image_url'] = $request->file('image')->store('food-items', 'public');
        }

        FoodItem::create($data);

        return redirect()->route('seller.food-items')->with('success', 'Food item created successfully.');
    }

    public function editFoodItem(FoodItem $foodItem)
    {
        $seller = Auth::user();

        if ($foodItem->restaurant->seller_id !== $seller->id) {
            return redirect()->route('seller.dashboard')->with('error', 'You can only edit food items from your own restaurants.');
        }

        $restaurants = $seller->restaurants;

        return view('seller.food-items.edit', compact('foodItem', 'restaurants'));
    }

    public function updateFoodItem(Request $request, FoodItem $foodItem)
    {
        $seller = Auth::user();

        if ($foodItem->restaurant->seller_id !== $seller->id) {
            return redirect()->route('seller.dashboard')->with('error', 'You can only edit food items from your own restaurants.');
        }

        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'available' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // Check if the new restaurant belongs to this seller
        $restaurant = Restaurant::find($request->restaurant_id);
        if ($restaurant->seller_id !== $seller->id) {
            return redirect()->back()->with('error', 'You can only move food items to your own restaurants.');
        }

        $data = [
            'restaurant_id' => $request->restaurant_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'available' => $request->has('available') ? true : false,
        ];

        if ($request->hasFile('image')) {
            $data['image_url'] = $request->file('image')->store('food-items', 'public');
        }

        $foodItem->update($data);

        return redirect()->route('seller.food-items')->with('success', 'Food item updated successfully.');
    }
}
