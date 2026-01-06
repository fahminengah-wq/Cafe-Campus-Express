@extends('app')

@section('title', 'Search Results - Campus Cafe Express')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Search Results</h1>
        @if($query)
            <p class="text-gray-600">Showing results for: <strong>"{{ $query }}"</strong></p>
        @endif
    </div>

    @if($results)
        <!-- Restaurants Section -->
        @if($results['restaurants']->count() > 0)
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Restaurants</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($results['restaurants'] as $restaurant)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
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
                        <p class="text-gray-500 text-sm mb-4"><i class="fas fa-map-marker-alt mr-1"></i>{{ $restaurant->location }}</p>

                        <a href="{{ route('restaurants.show', $restaurant) }}"
                           class="inline-block bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors duration-200">
                            View Restaurant
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Food Items Section -->
        @if($results['foodItems']->count() > 0)
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Food Items</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($results['foodItems'] as $foodItem)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <div class="relative">
                        @if($foodItem->image_url)
                            <img src="{{ asset('storage/' . $foodItem->image_url) }}"
                                 alt="{{ $foodItem->name }}"
                                 class="w-full h-40 object-cover">
                        @else
                            <img src="https://via.placeholder.com/300x150/4CAF50/FFFFFF?text={{ urlencode($foodItem->name) }}"
                                 alt="{{ $foodItem->name }}"
                                 class="w-full h-40 object-cover">
                        @endif
                        <div class="absolute top-2 right-2 bg-green-600 text-white px-2 py-1 rounded-full text-xs font-bold">
                            ${{ number_format($foodItem->price, 2) }}
                        </div>
                    </div>

                    <div class="p-4">
                        <h3 class="font-bold text-lg mb-1">{{ $foodItem->name }}</h3>
                        <p class="text-gray-600 text-sm mb-2">{{ Str::limit($foodItem->description, 50) }}</p>
                        <p class="text-green-600 font-semibold text-sm mb-3">{{ $foodItem->restaurant->name }}</p>

                        @auth
                            <button onclick="addToCart({{ $foodItem->id }}, '{{ $foodItem->name }}', {{ $foodItem->price }})"
                                    class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition-colors duration-200">
                                <i class="fas fa-cart-plus mr-2"></i>Add to Cart
                            </button>
                        @else
                            <a href="{{ route('login') }}"
                               class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition-colors duration-200 text-center block">
                                <i class="fas fa-sign-in-alt mr-2"></i>Login to Order
                            </a>
                        @endauth
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- No Results -->
        @if($results['restaurants']->count() == 0 && $results['foodItems']->count() == 0)
        <div class="text-center py-12">
            <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">No results found</h3>
            <p class="text-gray-500 mb-6">Try searching with different keywords or browse our restaurants.</p>
            <a href="{{ route('home') }}" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700">
                Browse All Restaurants
            </a>
        </div>
        @endif

    @else
        <div class="text-center py-12">
            <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">Search for restaurants and food</h3>
            <p class="text-gray-500 mb-6">Enter a search term above to find restaurants and food items.</p>
            <a href="{{ route('home') }}" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700">
                Back to Home
            </a>
        </div>
    @endif
</div>

<script>
@auth
function addToCart(foodItemId, name, price) {
    @auth
        fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams({
                'food_item_id': foodItemId,
                'quantity': 1
            })
        })
        .then(response => {
            if (response.status === 401 || response.status === 302) {
                window.location.href = '/login';
                return;
            }
            return response.json();
        })
        .then(data => {
            if (!data) return;
            if (data.success) {
                // Show success message
                showNotification(name + ' added to cart!', 'success');

                // Update cart count
                updateCartCount(data.cart_count);
            } else {
                showNotification(data.message || 'Failed to add item to cart', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to add item to cart', 'error');
        });
    @else
        window.location.href = '/login';
    @endauth
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 ${
        type === 'success' ? 'bg-green-600' : 'bg-red-600'
    }`;
    notification.textContent = message;

    document.body.appendChild(notification);

    // Remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

function updateCartCount(count) {
    let cartCount = document.getElementById('cart-count');
    if (!cartCount) {
        // Create cart count element if it doesn't exist
        const cartLink = document.querySelector('a[href="/cart"]');
        if (cartLink) {
            cartCount = document.createElement('span');
            cartCount.id = 'cart-count';
            cartCount.className = 'absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center';
            cartLink.appendChild(cartCount);
        }
    }
    if (cartCount) {
        cartCount.textContent = count || '';
        cartCount.style.display = count > 0 ? 'flex' : 'none';
    }
}
@else
function addToCart() {
    window.location.href = '/login';
}
@endauth
</script>
@endsection