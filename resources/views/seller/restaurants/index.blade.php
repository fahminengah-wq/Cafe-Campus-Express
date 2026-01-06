@extends('app')

@section('title', 'My Restaurants - Seller Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">My Restaurants</h1>
            <p class="text-gray-600">Manage your restaurant listings</p>
        </div>
        <a href="{{ route('seller.restaurants.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center">
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
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                @if($restaurant->image_url)
                    <img src="{{ asset('storage/' . $restaurant->image_url) }}"
                         alt="{{ $restaurant->name }}"
                         class="w-full h-48 object-cover">
                @else
                    <img src="https://via.placeholder.com/400x200/4CAF50/FFFFFF?text={{ urlencode($restaurant->name) }}"
                         alt="{{ $restaurant->name }}"
                         class="w-full h-48 object-cover">
                @endif

                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $restaurant->name }}</h3>
                    <p class="text-gray-600 mb-2">{{ $restaurant->cuisine_type }}</p>
                    <p class="text-gray-500 text-sm mb-4">{{ Str::limit($restaurant->description, 100) }}</p>

                    <div class="flex justify-between items-center">
                        <a href="{{ route('seller.restaurants.edit', $restaurant) }}"
                           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>
                        <a href="{{ route('seller.food-items', ['restaurant_id' => $restaurant->id]) }}"
                           class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-sm">
                            <i class="fas fa-utensils mr-1"></i> Manage Menu
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <i class="fas fa-store text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">No restaurants yet</h3>
            <p class="text-gray-500 mb-6">Start by adding your first restaurant to begin selling.</p>
            <a href="{{ route('seller.restaurants.create') }}" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700">
                <i class="fas fa-plus mr-2"></i> Add Your First Restaurant
            </a>
        </div>
    @endif
</div>
@endsection