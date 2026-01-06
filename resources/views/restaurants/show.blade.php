@extends('app')
@section('title', $restaurant->name . ' - Campus Cafe Express')

@section('content')
<div class="container mx-auto px-4 py-8">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif
    <a href="{{ route('restaurants.index') }}" class="inline-flex items-center text-green-600 hover:text-green-700 mb-6">
        <i class="fas fa-mug-hot mr-2"></i> Back to Restaurants
    </a>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
        <div class="p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $restaurant->name }}</h1>
            <div class="flex items-center space-x-4 mb-4">
                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                    {{ $restaurant->cuisine_type }}
                </span>
                <span class="text-gray-600">
                    <i class="far fa-clock mr-1"></i> {{ $restaurant->operating_hours }}
                </span>
            </div>
            
            @if($restaurant->description)
                <p class="text-gray-700 mb-6">{{ $restaurant->description }}</p>
            @endif
        </div>
    </div>

    <h2 class="text-2xl font-bold text-gray-800 mb-6">Menu</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($restaurant->foodItems as $foodItem)
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            @if($foodItem->image_url)
                <img src="{{ asset('storage/' . $foodItem->image_url) }}" alt="{{ $foodItem->name }}" class="w-full h-48 object-cover">
            @endif
            
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-bold">{{ $foodItem->name }}</h3>
                        <p class="text-green-600 font-semibold">RM{{ number_format($foodItem->price, 2) }}</p>
                    </div>
                    @if(!$foodItem->available)
                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Out of Stock</span>
                    @endif
                </div>
                
                @if($foodItem->description)
                    <p class="text-gray-600 mb-4">{{ $foodItem->description }}</p>
                @endif
                
                @if($foodItem->available)
                    <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="food_item_id" value="{{ $foodItem->id }}">
                        <div class="flex items-center space-x-2">
                            <input type="number" name="quantity" value="1" min="1" 
                                   class="w-20 px-3 py-2 border rounded">
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                                Add to Cart
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    
    @if($restaurant->foodItems->isEmpty())
        <div class="text-center py-12">
            <i class="fas fa-utensils text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-500">No menu items available at the moment.</p>
        </div>
    @endif
</div>
@endsection