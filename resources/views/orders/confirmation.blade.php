@extends('app')
@section('title', 'Order Confirmed - Campus Cafe Express')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-lg p-8 text-center">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-check text-4xl text-green-600"></i>
        </div>

        <h1 class="text-3xl font-bold text-gray-800 mb-4">Order Confirmed!</h1>
        <p class="text-xl text-gray-600 mb-8">Thank you for your order. Your food is being prepared.</p>

        <div class="bg-gray-50 rounded-xl p-6 mb-8">
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="text-left">
                    <p class="text-gray-600">Order ID</p>
                    <p class="font-bold text-lg">{{ $order->order_number }}</p>
                </div>
                <div class="text-left">
                    <p class="text-gray-600">Order Status</p>
                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                        @if($order->status == 'confirmed') Preparing
                        @elseif($order->status == 'ready') Ready for {{ $order->order_type == 'delivery' ? 'Delivery' : 'Pickup' }}
                        @else {{ ucfirst($order->status) }}
                        @endif
                    </span>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h3 class="font-bold text-gray-800 mb-4 text-left">Order Summary</h3>
                @foreach($order->items as $item)
                <div class="flex justify-between mb-2">
                    <span>{{ $item->foodItem->name }} x{{ $item->quantity }}</span>
                    <span class="font-medium">RM{{ number_format($item->subtotal, 2) }}</span>
                </div>
                @endforeach
                
                <div class="border-t border-gray-200 pt-4 mt-4 space-y-2">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span>RM{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Delivery Fee</span>
                        <span>RM{{ number_format($order->delivery_fee, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Tax</span>
                        <span>RM{{ number_format($order->tax, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold pt-2 border-t">
                        <span>Total</span>
                        <span class="text-green-600">RM{{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if($order->order_type == 'delivery')
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-8">
            <div class="flex items-center justify-center">
                <i class="fas fa-clock text-blue-600 text-2xl mr-3"></i>
                <div class="text-left">
                    <p class="font-medium text-blue-800">Estimated Delivery Time</p>
                    <p class="text-2xl font-bold text-blue-900">25-30 minutes</p>
                </div>
            </div>
        </div>
        @endif

        @if($order->payment->method == 'qr' && $order->payment->status == 'pending')
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-8">
            <div class="text-center">
                <i class="fas fa-clock text-yellow-600 text-2xl mb-4"></i>
                <h3 class="font-bold text-yellow-800 mb-2">Payment Pending</h3>
                <p class="text-sm text-yellow-600">Your order is confirmed but payment is still pending. Please complete your payment to proceed.</p>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('home') }}" 
               class="bg-gray-200 text-gray-800 py-3 rounded-lg font-medium hover:bg-gray-300">
                <i class="fas fa-home mr-2"></i> Back to Home
            </a>
            <a href="{{ route('orders') }}" 
               class="bg-green-600 text-white py-3 rounded-lg font-medium hover:bg-green-700">
                <i class="fas fa-history mr-2"></i> View Order History
            </a>
        </div>
    </div>
</div>
@endsection