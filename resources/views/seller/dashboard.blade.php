@extends('app')

@section('title', 'Seller Dashboard - Campus Cafe Express')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Seller Dashboard</h1>
        <p class="text-gray-600">Manage your restaurants and orders</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-utensils text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">{{ $restaurants->count() }}</h3>
                    <p class="text-gray-600">My Restaurants</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">{{ $pendingOrders }}</h3>
                    <p class="text-gray-600">Pending Orders</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 rounded-full">
                    <i class="fas fa-fire text-orange-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">{{ $preparingOrders }}</h3>
                    <p class="text-gray-600">Preparing</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">{{ $readyOrders }}</h3>
                    <p class="text-gray-600">Ready Orders</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-dollar-sign text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">${{ number_format($totalRevenue, 2) }}</h3>
                    <p class="text-gray-600">Total Revenue</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Recent Orders</h2>
            <a href="{{ route('seller.orders') }}" class="text-blue-600 hover:text-blue-800">View All</a>
        </div>

        @if($orders->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Order #</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($orders->take(5) as $order)
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $order->order_number }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $order->student->name }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($order->seller_status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->seller_status == 'preparing') bg-orange-100 text-orange-800
                                    @elseif($order->seller_status == 'ready') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($order->seller_status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">${{ number_format($order->total, 2) }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm">
                                <a href="{{ route('seller.orders') }}" class="text-blue-600 hover:text-blue-900">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 text-center py-4">No orders yet.</p>
        @endif
    </div>

    <!-- My Restaurants -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">My Restaurants</h2>
            <a href="{{ route('seller.restaurants.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Restaurant</a>
        </div>

        @if($restaurants->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($restaurants as $restaurant)
                <div class="border rounded-lg p-4">
                    @if($restaurant->image_url)
                        <img src="{{ asset('storage/' . $restaurant->image_url) }}" alt="{{ $restaurant->name }}" class="w-full h-32 object-cover rounded mb-2">
                    @endif
                    <h3 class="font-semibold text-lg">{{ $restaurant->name }}</h3>
                    <p class="text-gray-600 text-sm">{{ $restaurant->cuisine_type }}</p>
                    <p class="text-gray-500 text-sm">{{ $restaurant->location }}</p>
                    <div class="mt-2 flex justify-between">
                        <a href="{{ route('seller.restaurants.edit', $restaurant) }}" class="text-blue-600 hover:text-blue-800 text-sm">Edit</a>
                        <a href="{{ route('seller.food-items') }}?restaurant={{ $restaurant->id }}" class="text-green-600 hover:text-green-800 text-sm">Menu</a>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-gray-500 mb-4">You haven't created any restaurants yet.</p>
                <a href="{{ route('seller.restaurants.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Create Your First Restaurant</a>
            </div>
        @endif
    </div>
</div>
@endsection