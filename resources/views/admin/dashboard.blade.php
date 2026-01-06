@extends('layouts.admin')

@section('title', 'Admin Dashboard - Campus Cafe Express')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Admin Dashboard</h1>
        <p class="text-gray-600">Manage restaurants and menu items</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-utensils text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">{{ $totalRestaurants }}</h3>
                    <p class="text-gray-600">Total Restaurants</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-hamburger text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">{{ $totalFoodItems }}</h3>
                    <p class="text-gray-600">Total Menu Items</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-shopping-cart text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">{{ $totalOrders }}</h3>
                    <p class="text-gray-600">Total Orders</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">{{ $paidOrders }}</h3>
                    <p class="text-gray-600">Paid Orders</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <a href="{{ route('admin.restaurants.create') }}" class="flex items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                <i class="fas fa-plus-circle text-blue-600 mr-3"></i>
                <span class="font-medium text-blue-800">Add Restaurant</span>
            </a>
            <a href="{{ route('admin.food-items.create') }}" class="flex items-center p-4 bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                <i class="fas fa-plus-circle text-green-600 mr-3"></i>
                <span class="font-medium text-green-800">Add Menu Item</span>
            </a>
            <a href="{{ route('admin.restaurants') }}" class="flex items-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors">
                <i class="fas fa-utensils text-purple-600 mr-3"></i>
                <span class="font-medium text-purple-800">Manage Restaurants</span>
            </a>
            <a href="{{ route('admin.food-items') }}" class="flex items-center p-4 bg-orange-50 hover:bg-orange-100 rounded-lg transition-colors">
                <i class="fas fa-hamburger text-orange-600 mr-3"></i>
                <span class="font-medium text-orange-800">Manage Menu</span>
            </a>
            <a href="{{ route('admin.orders') }}" class="flex items-center p-4 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                <i class="fas fa-shopping-cart text-red-600 mr-3"></i>
                <span class="font-medium text-red-800">View Orders</span>
            </a>
        </div>
    </div>

    <!-- QR Code Management -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Payment QR Code</h2>
        <div class="flex items-center justify-between">
            <div class="flex-1">
                @if(Auth::user()->qr_code)
                    <div class="flex items-center space-x-4">
                        <img src="{{ asset('storage/' . Auth::user()->qr_code) }}" alt="Payment QR Code" class="w-24 h-24 object-cover rounded-lg">
                        <div>
                            <p class="text-sm text-gray-600">Current QR Code</p>
                            <p class="text-xs text-gray-500">Customers will see this QR code for payments</p>
                        </div>
                    </div>
                @else
                    <div class="text-gray-500">
                        <i class="fas fa-qrcode text-3xl mb-2"></i>
                        <p>No QR code uploaded yet</p>
                    </div>
                @endif
            </div>
            <div class="ml-4">
                <form action="{{ route('admin.update-qr-code') }}" method="POST" enctype="multipart/form-data" class="flex items-center space-x-2">
                    @csrf
                    @method('PUT')
                    <input type="file" name="qr_code" accept="image/*" class="hidden" id="qr_code_input">
                    <label for="qr_code_input" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 cursor-pointer">
                        <i class="fas fa-upload mr-2"></i> {{ Auth::user()->qr_code ? 'Update' : 'Upload' }} QR Code
                    </label>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                        <i class="fas fa-save mr-2"></i> Save
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Recent Restaurants -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Restaurants</h2>
            <a href="{{ route('admin.restaurants') }}" class="text-blue-600 hover:text-blue-800 font-medium">View All</a>
        </div>

        @if($restaurants->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($restaurants->take(6) as $restaurant)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        @if($restaurant->image_url)
                            <img src="{{ asset('storage/' . $restaurant->image_url) }}" alt="{{ $restaurant->name }}" class="w-full h-32 object-cover rounded-lg mb-3">
                        @else
                            <div class="w-full h-32 bg-gray-200 rounded-lg mb-3 flex items-center justify-center">
                                <i class="fas fa-utensils text-gray-400 text-2xl"></i>
                            </div>
                        @endif
                        <h3 class="font-semibold text-gray-800 mb-1">{{ $restaurant->name }}</h3>
                        <p class="text-sm text-gray-600 mb-2">{{ $restaurant->cuisine_type }}</p>
                        <p class="text-sm text-gray-500">{{ $restaurant->foodItems->count() }} items</p>
                        <div class="mt-3 flex space-x-2">
                            <a href="{{ route('admin.restaurants.edit', $restaurant->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Edit</a>
                            <a href="{{ route('admin.restaurant.food-items', $restaurant->id) }}" class="text-green-600 hover:text-green-800 text-sm font-medium">Menu</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <i class="fas fa-utensils text-gray-300 text-4xl mb-4"></i>
                <p class="text-gray-500 mb-4">No restaurants found</p>
                <a href="{{ route('admin.restaurants.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Add First Restaurant</a>
            </div>
        @endif
    </div>
</div>
@endsection