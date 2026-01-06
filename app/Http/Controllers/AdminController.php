<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\FoodItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // Add at the beginning of AdminController class

public function showAdminLogin()
{
    return view('admin.auth.login');
}

public function adminLogin(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $credentials = $request->only('email', 'password');
    
    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard')->with('success', 'Welcome back, Admin!');
        } else {
            Auth::logout();
            return back()->with('error', 'You are not authorized as admin.');
        }
    }
    
    return back()->with('error', 'Invalid admin credentials.');
}

// Add this method for image upload
public function uploadFoodItemImage(Request $request, $id)
{
    $request->validate([
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
    ]);

    $foodItem = FoodItem::findOrFail($id);
    
    if ($request->hasFile('image')) {
        // Delete old image if exists
        if ($foodItem->image_url) {
            $oldImage = str_replace('/storage/', 'public/', $foodItem->image_url);
            Storage::delete($oldImage);
        }
        
        // Store new image
        $imagePath = $request->file('image')->store('food-items', 'public');
        $foodItem->image_url = Storage::url($imagePath);
        $foodItem->save();
        
        return back()->with('success', 'Image uploaded successfully!');
    }
    
    return back()->with('error', 'Failed to upload image.');
}

    // Dashboard
    public function dashboard()
    {
        $restaurants = Restaurant::with('foodItems')->get();
        $totalRestaurants = $restaurants->count();
        $totalFoodItems = FoodItem::count();
        $totalOrders = \App\Models\Order::count();
        $paidOrders = \App\Models\Order::whereHas('payment', function($query) {
            $query->where('status', 'completed');
        })->count();

        return view('admin.dashboard', compact('restaurants', 'totalRestaurants', 'totalFoodItems', 'totalOrders', 'paidOrders'));
    }

    // Restaurant Management
    public function restaurants()
    {
        $restaurants = Restaurant::with('foodItems')->get();
        return view('admin.restaurants.index', compact('restaurants'));
    }

    public function createRestaurant()
    {
        return view('admin.restaurants.create');
    }

    public function storeRestaurant(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'cuisine_type' => 'required|string|max:255',
            'operating_hours' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['name', 'cuisine_type', 'operating_hours', 'description']);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('restaurants', 'public');
            $data['image_url'] = $imagePath;
        }

        Restaurant::create($data);

        return redirect()->route('admin.restaurants')->with('success', 'Restaurant created successfully!');
    }

    public function editRestaurant($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        return view('admin.restaurants.edit', compact('restaurant'));
    }

    public function updateRestaurant(Request $request, $id)
    {
        $restaurant = Restaurant::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'cuisine_type' => 'required|string|max:255',
            'operating_hours' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['name', 'cuisine_type', 'operating_hours', 'description']);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($restaurant->image_url) {
                Storage::delete($restaurant->image_url);
            }
            $imagePath = $request->file('image')->store('restaurants', 'public');
            $data['image_url'] = $imagePath;
        }

        $restaurant->update($data);

        return redirect()->route('admin.restaurants')->with('success', 'Restaurant updated successfully!');
    }

    public function deleteRestaurant($id)
    {
        $restaurant = Restaurant::findOrFail($id);

        // Delete associated food items and their images
        foreach ($restaurant->foodItems as $foodItem) {
            if ($foodItem->image_url) {
                Storage::delete($foodItem->image_url);
            }
            $foodItem->delete();
        }

        // Delete restaurant image
        if ($restaurant->image_url) {
            Storage::delete($restaurant->image_url);
        }

        $restaurant->delete();

        return redirect()->route('admin.restaurants')->with('success', 'Restaurant deleted successfully!');
    }

    // Order Management
    public function orders(Request $request)
    {
        $query = \App\Models\Order::with(['student', 'payment', 'items.foodItem.restaurant']);

        // Filter by payment status
        if ($request->has('payment_status') && $request->payment_status !== '') {
            $query->whereHas('payment', function($q) use ($request) {
                $q->where('status', $request->payment_status);
            });
        }

        // Sort by date and time
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $query->orderBy($sortBy, $sortDirection);

        $orders = $query->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function updatePaymentStatus(Request $request, $orderId)
    {
        $request->validate([
            'status' => 'required|in:pending,completed,failed'
        ]);

        $order = \App\Models\Order::findOrFail($orderId);
        
        if ($order->payment) {
            $order->payment->update(['status' => $request->status]);
            return redirect()->back()->with('success', 'Payment status updated successfully!');
        } else {
            return redirect()->back()->with('error', 'Order has no payment record.');
        }
    }

    // Food Item Management
    public function foodItems($restaurantId = null)
    {
        if ($restaurantId) {
            $restaurant = Restaurant::findOrFail($restaurantId);
            $foodItems = $restaurant->foodItems;
        } else {
            $foodItems = FoodItem::with('restaurant')->get();
        }

        $restaurants = Restaurant::all();
        return view('admin.food-items.index', compact('foodItems', 'restaurants', 'restaurantId'));
    }

    public function createFoodItem($restaurantId = null)
    {
        $restaurants = Restaurant::all();
        return view('admin.food-items.create', compact('restaurants', 'restaurantId'));
    }

    public function storeFoodItem(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'available' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['restaurant_id', 'name', 'description', 'price', 'available']);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('food-items', 'public');
            $data['image_url'] = $imagePath;
        }

        FoodItem::create($data);

        return redirect()->route('admin.food-items', $request->restaurant_id)->with('success', 'Food item created successfully!');
    }

    public function editFoodItem($id)
    {
        $foodItem = FoodItem::findOrFail($id);
        $restaurants = Restaurant::all();
        return view('admin.food-items.edit', compact('foodItem', 'restaurants'));
    }

    public function updateFoodItem(Request $request, $id)
    {
        $foodItem = FoodItem::findOrFail($id);

        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'available' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['restaurant_id', 'name', 'description', 'price', 'available']);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($foodItem->image_url) {
                Storage::delete($foodItem->image_url);
            }
            $imagePath = $request->file('image')->store('food-items', 'public');
            $data['image_url'] = $imagePath;
        }

        $foodItem->update($data);

        return redirect()->route('admin.food-items', $request->restaurant_id)->with('success', 'Food item updated successfully!');
    }

    public function deleteFoodItem($id)
    {
        $foodItem = FoodItem::findOrFail($id);

        // Delete image if exists
        if ($foodItem->image_url) {
            Storage::delete($foodItem->image_url);
        }

        $foodItem->delete();

        return redirect()->back()->with('success', 'Food item deleted successfully!');
    }

    public function updateQrCode(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $admin = Auth::user();

        if ($request->hasFile('qr_code')) {
            // Delete old QR code if exists
            if ($admin->qr_code) {
                Storage::delete($admin->qr_code);
            }
            $qrPath = $request->file('qr_code')->store('qr_codes', 'public');
            $admin->update(['qr_code' => $qrPath]);
        }

        return redirect()->back()->with('success', 'QR code updated successfully!');
    }
}
