@extends('layouts.admin')

@section('title', 'Manage Restaurants - Admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Manage Restaurants</h1>
            <p class="text-gray-600">Add, edit, and manage restaurant listings</p>
        </div>
        <a href="{{ route('admin.restaurants.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
            <i class="fas fa-plus mr-2"></i> Add Restaurant
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if($restaurants->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($restaurants as $restaurant)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    @if($restaurant->image_url)
                        <img src="{{ asset('storage/' . $restaurant->image_url) }}" alt="{{ $restaurant->name }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-utensils text-gray-400 text-4xl"></i>
                        </div>
                    @endif

                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $restaurant->name }}</h3>
                        <p class="text-gray-600 mb-2"><strong>Cuisine:</strong> {{ $restaurant->cuisine_type }}</p>
                        <p class="text-gray-600 mb-2"><strong>Hours:</strong> {{ $restaurant->operating_hours }}</p>
                        <p class="text-gray-600 mb-4">{{ Str::limit($restaurant->description, 100) }}</p>
                        <p class="text-sm text-gray-500 mb-4">{{ $restaurant->foodItems->count() }} menu items</p>

                        <div class="flex space-x-2">
                            <a href="{{ route('admin.restaurant.food-items', $restaurant->id) }}" class="bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700 text-sm">
                                <i class="fas fa-hamburger mr-1"></i> Menu
                            </a>
                            <a href="{{ route('admin.restaurants.edit', $restaurant->id) }}" class="bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 text-sm">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </a>
                            <form action="{{ route('admin.restaurants.delete', $restaurant->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this restaurant?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700 text-sm">
                                    <i class="fas fa-trash mr-1"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <i class="fas fa-utensils text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">No restaurants found</h3>
            <p class="text-gray-600 mb-6">Get started by adding your first restaurant</p>
            <a href="{{ route('admin.restaurants.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i> Add First Restaurant
            </a>
        </div>
    @endif
</div>
@endsection