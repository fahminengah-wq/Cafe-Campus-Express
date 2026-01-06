@extends('app')
@section('title', 'Shopping Cart - Campus Cafe Express')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Shopping Cart</h1>

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

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($cartItems->isEmpty())
        <div class="text-center py-12">
            <i class="fas fa-shopping-cart text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg mb-4">Your cart is empty</p>
            <a href="{{ route('restaurants.index') }}" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700">
                Browse Restaurants
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    @foreach($cartItems as $item)
                    @if($item->foodItem)
                    <div class="flex items-center justify-between border-b border-gray-200 pb-6 mb-6">
                        <div class="flex items-center">
                            @if($item->foodItem->image_url)
                                <img src="{{ asset('storage/' . $item->foodItem->image_url) }}" 
                                     alt="{{ $item->foodItem->name }}" 
                                     class="w-16 h-16 object-cover rounded-lg">
                            @else
                                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-utensils text-gray-400"></i>
                                </div>
                            @endif
                            <div class="ml-4">
                                <h3 class="font-bold text-lg">{{ $item->foodItem->name }}</h3>
                                <p class="text-gray-600">RM{{ number_format($item->price, 2) }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center">
                                @csrf
                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" 
                                       class="w-20 px-3 py-1 border rounded">
                                <button type="submit" class="ml-2 text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </form>
                            
                            <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @else
                        <!-- Handle orphaned cart item -->
                        <div class="flex items-center justify-between border-b border-red-200 pb-6 mb-6 bg-red-50 p-4 rounded">
                            <div class="flex items-center">
                                <div class="w-16 h-16 bg-red-200 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="font-bold text-lg text-red-800">Item Unavailable</h3>
                                    <p class="text-red-600">This item is no longer available and will be removed from your cart.</p>
                                </div>
                            </div>
                            <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    @endif
                    @endforeach
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <!-- Voucher Section -->
                @if($availableVouchers->count() > 0)
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Apply Voucher</h2>

                    @if($appliedVoucher)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-semibold text-green-800">{{ $appliedVoucher->voucher->name }}</p>
                                    <p class="text-sm text-green-600">Discount: ${{ number_format($discount, 2) }}</p>
                                </div>
                                <form action="{{ route('cart.remove-voucher') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <form action="{{ route('cart.apply-voucher') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">Select Voucher</label>
                                <select name="voucher_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <option value="">Choose a voucher...</option>
                                    @foreach($availableVouchers as $voucher)
                                    <option value="{{ $voucher->id }}">
                                        {{ $voucher->voucher->name }} - {{ $voucher->voucher->code }}
                                        @if($voucher->voucher->type === 'percentage')
                                            ({{ $voucher->voucher->value }}% off)
                                        @else
                                            (${{ $voucher->voucher->value }} off)
                                        @endif
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="w-full bg-yellow-500 text-white py-2 rounded-lg hover:bg-yellow-600">
                                Apply Voucher
                            </button>
                        </form>
                    @endif

                    <div class="mt-4 text-sm text-gray-600">
                        <p><strong>How vouchers work:</strong></p>
                        <ul class="list-disc list-inside mt-1">
                            <li>Each voucher can only be used once</li>
                            <li>Minimum order requirements may apply</li>
                            <li>Vouchers expire on their expiry date</li>
                        </ul>
                    </div>
                </div>
                @endif

                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Order Summary</h2>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-semibold">RM{{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Delivery Fee</span>
                            <span class="font-semibold">RM{{ number_format($deliveryFee, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tax (8%)</span>
                            <span class="font-semibold">RM{{ number_format($tax, 2) }}</span>
                        </div>
                        @if($appliedVoucher)
                        <div class="flex justify-between text-green-600">
                            <span>Discount ({{ $appliedVoucher->voucher->name }})</span>
                            <span>-RM{{ number_format($discount, 2) }}</span>
                        </div>
                        @endif
                        <div class="border-t border-gray-200 pt-3">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total Amount</span>
                                <span class="text-green-600">RM{{ number_format($finalTotal, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <button onclick="showCheckoutModal()" 
                            class="w-full bg-green-600 text-white py-3 rounded-lg font-bold hover:bg-green-700 mb-4">
                        <i class="fas fa-shopping-cart mr-2"></i> Proceed to Checkout
                    </button>

                    <a href="{{ route('restaurants.index') }}" 
                       class="block text-center text-green-600 hover:text-green-700 font-medium">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Checkout Modal -->
<div id="checkoutModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-lg p-8 max-w-md w-full mx-4">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Checkout</h2>
        
        <form action="{{ route('checkout') }}" method="POST">
            @csrf
            
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Order Type</label>
                <div class="grid grid-cols-2 gap-4">
                    <label for="delivery" class="border-2 border-green-600 text-green-600 py-3 rounded-lg font-medium hover:bg-green-50 cursor-pointer text-center">
                        <input type="radio" name="order_type" value="delivery" id="delivery" checked>
                        Delivery
                    </label>
                    <label for="pickup" class="border-2 border-gray-300 text-gray-700 py-3 rounded-lg font-medium hover:bg-gray-50 cursor-pointer text-center">
                        <input type="radio" name="order_type" value="pickup" id="pickup">
                        Pickup
                    </label>
                </div>
            </div>
            
            <div class="mb-6" id="deliveryAddressField">
                <label class="block text-gray-700 font-medium mb-2">Delivery Address</label>
                <textarea name="delivery_address" rows="3" 
                          class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                          placeholder="Enter your delivery address"></textarea>
            </div>
            
            <div class="mb-6 hidden" id="pickupTimeField">
                <label class="block text-gray-700 font-medium mb-2">Pickup Time</label>
                <select name="pickup_time" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="ASAP">ASAP (20-30 minutes)</option>
                    <option value="12:00">12:00 PM</option>
                    <option value="12:30">12:30 PM</option>
                    <option value="13:00">1:00 PM</option>
                </select>
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Payment Method</label>
                <select name="payment_method" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="qr">QR Code Payment</option>
                    <option value="cod">Cash on Delivery</option>
                </select>
            </div>
            
            <div class="flex space-x-4">
                <button type="button" onclick="hideCheckoutModal()" 
                        class="flex-1 bg-gray-300 text-gray-800 py-3 rounded-lg font-medium hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit" 
                        class="flex-1 bg-green-600 text-white py-3 rounded-lg font-medium hover:bg-green-700">
                    Place Order
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showCheckoutModal() {
    document.getElementById('checkoutModal').classList.remove('hidden');
}

function hideCheckoutModal() {
    document.getElementById('checkoutModal').classList.add('hidden');
}

// Toggle delivery/pickup fields
document.getElementById('delivery').addEventListener('change', function() {
    if (this.checked) {
        document.getElementById('deliveryAddressField').classList.remove('hidden');
        document.getElementById('pickupTimeField').classList.add('hidden');
    }
});

document.getElementById('pickup').addEventListener('change', function() {
    if (this.checked) {
        document.getElementById('deliveryAddressField').classList.add('hidden');
        document.getElementById('pickupTimeField').classList.remove('hidden');
    }
});

// Close modal when clicking outside
document.getElementById('checkoutModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideCheckoutModal();
    }
});
</script>
@endsection