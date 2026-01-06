@extends('app')

@section('title', 'Home - Campus Cafe Express')

@section('content')
<div class="bg-gradient-to-r from-green-500 to-green-600 text-white py-16">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Order Your Favorite Food</h1>
        <p class="text-xl mb-8">Delivered fresh to your dorm or classroom</p>

        <!-- Search Bar -->
        <div class="max-w-2xl mx-auto">
            <form method="GET" action="{{ route('search') }}">
                <div class="flex">
                    <input type="text" name="q" placeholder="Search restaurant or food..." value="{{ request('q') }}"
                           class="flex-grow px-6 py-3 rounded-l-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <button type="submit" class="bg-yellow-500 text-white px-6 py-3 rounded-r-lg hover:bg-yellow-600">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Featured Menu Items Carousel -->
@if($featuredFoodItems->count() > 0)
<div class="bg-white py-12">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-8">Featured Menu Items</h2>

        <div class="relative overflow-hidden">
            <div class="flex space-x-6 overflow-x-auto pb-4 scrollbar-hide" id="menu-carousel">
                @foreach($featuredFoodItems as $foodItem)
                <div class="flex-shrink-0 w-64 bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <div class="relative">
                        @if($foodItem->image_url)
                            <img src="{{ asset('storage/' . $foodItem->image_url) }}"
                                 alt="{{ $foodItem->name }}"
                                 class="w-full h-40 object-cover">
                        @else
                            <img src="https://via.placeholder.com/256x160/4CAF50/FFFFFF?text={{ urlencode($foodItem->name) }}"
                                 alt="{{ $foodItem->name }}"
                                 class="w-full h-40 object-cover">
                        @endif
                        <div class="absolute top-2 right-2 bg-green-600 text-white px-2 py-1 rounded-full text-xs font-bold">
                            RM{{ number_format($foodItem->price, 2) }}
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-lg mb-1">{{ $foodItem->name }}</h3>
                        <p class="text-gray-600 text-sm mb-2">{{ Str::limit($foodItem->description, 50) }}</p>
                        <p class="text-green-600 font-semibold text-sm mb-3">{{ $foodItem->restaurant->name }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Carousel Navigation -->
            <button id="prev-btn" class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-80 hover:bg-opacity-100 rounded-full p-2 shadow-lg transition-all duration-200">
                <i class="fas fa-chevron-left text-gray-600"></i>
            </button>
            <button id="next-btn" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-80 hover:bg-opacity-100 rounded-full p-2 shadow-lg transition-all duration-200">
                <i class="fas fa-chevron-right text-gray-600"></i>
            </button>
        </div>
    </div>
</div>
@endif

<div class="container mx-auto px-4 py-12">
    <h2 class="text-3xl font-bold text-center mb-8">Our Restaurants</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <!-- Restaurant Cards -->
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
                <h3 class="text-xl font-bold mb-2">{{ $restaurant->name }}</h3>
                <p class="text-gray-600 mb-4">{{ $restaurant->description ?? 'Delicious food from our campus cafeteria' }}</p>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500"><i class="fas fa-utensils mr-1"></i> {{ $restaurant->cuisine_type ?? 'Various' }}</span>
                    <span class="text-sm text-gray-500"><i class="far fa-clock mr-1"></i> {{ $restaurant->operating_hours ?? 'Check hours' }}</span>
                </div>
                <a href="{{ route('restaurants.show', $restaurant->id) }}"
                   class="mt-4 w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 inline-block text-center">
                    View Menu
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div class="bg-gray-100 py-12">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12">How It Works</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-search text-2xl text-green-600"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">1. Browse Restaurants</h3>
                <p class="text-gray-600">Choose from various campus cafeterias</p>
            </div>
            <div class="text-center">
                <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shopping-cart text-2xl text-green-600"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">2. Add to Cart</h3>
                <p class="text-gray-600">Select your favorite meals</p>
            </div>
            <div class="text-center">
                <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-motorcycle text-2xl text-green-600"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">3. Get Delivery</h3>
                <p class="text-gray-600">Food delivered to your location</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.getElementById('menu-carousel');
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');

    if (carousel && prevBtn && nextBtn) {
        let scrollAmount = 0;
        const scrollStep = 280; // Width of card + margin

        prevBtn.addEventListener('click', function() {
            scrollAmount = Math.max(0, scrollAmount - scrollStep);
            carousel.scrollTo({
                left: scrollAmount,
                behavior: 'smooth'
            });
        });

        nextBtn.addEventListener('click', function() {
            const maxScroll = carousel.scrollWidth - carousel.clientWidth;
            scrollAmount = Math.min(maxScroll, scrollAmount + scrollStep);
            carousel.scrollTo({
                left: scrollAmount,
                behavior: 'smooth'
            });
        });

        // Auto-scroll every 5 seconds
        setInterval(function() {
            const maxScroll = carousel.scrollWidth - carousel.clientWidth;
            if (scrollAmount >= maxScroll) {
                scrollAmount = 0;
            } else {
                scrollAmount += scrollStep;
            }
            carousel.scrollTo({
                left: scrollAmount,
                behavior: 'smooth'
            });
        }, 5000);
    }
});

// Add to cart function
function addToCart(foodItemId, name, price) {
    console.log('Adding to cart:', foodItemId, name, price);

    @auth
        console.log('User is authenticated, making AJAX request');
        // User is logged in, add to cart via AJAX
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
            console.log('Response status:', response.status);
            if (response.status === 401 || response.status === 302) {
                // User not authenticated, redirect to login
                console.log('User not authenticated, redirecting to login');
                window.location.href = '/login';
                return;
            }
            return response.json();
        })
        .then(data => {
            if (!data) return; // Already handled redirect
            console.log('Response data:', data);
            if (data.success) {
                // Show success message
                showNotification(name + ' added to cart!', 'success');

                // Update cart count if element exists
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
        console.log('User not authenticated, redirecting to login');
        // User not logged in, redirect to login
        window.location.href = '/login';
    @endauth
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

function claimFirstOrderVoucher() {
    @auth
        fetch('/vouchers/claim-first-order', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('First order voucher claimed successfully!', 'success');
                setTimeout(() => {
                    window.location.href = '/vouchers';
                }, 1500);
            } else {
                showNotification(data.message || 'Failed to claim voucher', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to claim voucher', 'error');
        });
    @else
        window.location.href = '/login';
    @endauth
}

// Carousel functionality
document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.getElementById('menu-carousel');
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');

    if (carousel && prevBtn && nextBtn) {
        let scrollAmount = 0;
        const scrollStep = 320; // Width of one card + margin

        nextBtn.addEventListener('click', function() {
            scrollAmount += scrollStep;
            if (scrollAmount > carousel.scrollWidth - carousel.clientWidth) {
                scrollAmount = carousel.scrollWidth - carousel.clientWidth;
            }
            carousel.scrollTo({
                left: scrollAmount,
                behavior: 'smooth'
            });
        });

        prevBtn.addEventListener('click', function() {
            scrollAmount -= scrollStep;
            if (scrollAmount < 0) {
                scrollAmount = 0;
            }
            carousel.scrollTo({
                left: scrollAmount,
                behavior: 'smooth'
            });
        });
    }
});
</script>
@endsection