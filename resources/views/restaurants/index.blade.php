@extends('app')

@section('title', 'Restaurants - Campus Cafe Express')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Browse Restaurants</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($restaurants as $restaurant)
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            @if($restaurant->image_url)
                <img src="{{ asset('storage/' . $restaurant->image_url) }}" 
                     alt="{{ $restaurant->name }}" class="w-full h-48 object-cover">
            @else
                <img src="https://via.placeholder.com/600x300/4CAF50/FFFFFF?text={{ urlencode($restaurant->name) }}" 
                     alt="{{ $restaurant->name }}" class="w-full h-48 object-cover">
            @endif
            <div class="p-6">
                <h3 class="text-xl font-bold mb-2">{{ $restaurant->name }}</h3>
                <p class="text-gray-600 mb-3">Cuisine: {{ $restaurant->cuisine_type }}</p>
                <p class="text-gray-600 mb-4">
                    <i class="far fa-clock mr-1"></i> {{ $restaurant->operating_hours }}
                </p>
                @if($restaurant->description)
                    <p class="text-gray-600 mb-4 text-sm">{{ \Illuminate\Support\Str::limit($restaurant->description, 100) }}</p>
                @endif
                <a href="{{ route('restaurants.show', $restaurant->id) }}" 
                   class="block bg-green-600 text-white text-center py-2 rounded-lg hover:bg-green-700">
                    View Menu
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection