@extends('app')
@section('title', 'Order History - Campus Cafe Express')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Order History</h1>

    @if($orders->isEmpty())
        <div class="text-center py-12">
            <i class="fas fa-history text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg mb-4">No orders yet</p>
            <a href="{{ route('restaurants.index') }}" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700">
                Order Now
            </a>
        </div>
    @else
        <div class="space-y-6">
            @foreach($orders as $order)
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between">
                    <div>
                        <div class="flex items-center mb-2">
                            <h3 class="text-lg font-bold mr-4">{{ $order->order_number }}</h3>
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                @if($order->status == 'completed') bg-green-100 text-green-800
                                @elseif($order->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                @elseif($order->status == 'confirmed') bg-blue-100 text-blue-800
                                @elseif($order->status == 'ready') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800 @endif">
                                @if($order->status == 'confirmed') Preparing
                                @elseif($order->status == 'ready') Ready for {{ $order->order_type == 'delivery' ? 'Delivery' : 'Pickup' }}
                                @else {{ ucfirst($order->status) }}
                                @endif
                            </span>
                        </div>
                        <p class="text-gray-600 mb-2">
                            <i class="far fa-calendar mr-2"></i> {{ $order->created_at->format('M d, Y - h:i A') }}
                        </p>
                        <p class="text-gray-800">
                            <strong>Type:</strong> {{ ucfirst($order->order_type) }}
                            @if($order->order_type == 'delivery')
                                - {{ $order->delivery_address }}
                            @endif
                        </p>
                    </div>
                    
                    <div class="mt-4 md:mt-0 text-center md:text-right">
                        <p class="text-2xl font-bold text-green-600 mb-2">RM{{ number_format($order->total, 2) }}</p>
                        <a href="{{ route('order.confirmation', $order->id) }}" 
                           class="bg-gray-100 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-200">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection