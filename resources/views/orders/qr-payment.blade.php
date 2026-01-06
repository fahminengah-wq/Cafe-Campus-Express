@extends('app')
@section('title', 'Complete Payment - Campus Cafe Express')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-lg p-8 text-center">
        <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-qrcode text-4xl text-blue-600"></i>
        </div>

        <h1 class="text-3xl font-bold text-gray-800 mb-4">Complete Your Payment</h1>
        <p class="text-xl text-gray-600 mb-8">Scan the QR code below to complete your payment</p>

        <div class="bg-gray-50 rounded-xl p-6 mb-8">
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="text-left">
                    <p class="text-gray-600">Order ID</p>
                    <p class="font-bold text-lg">{{ $order->order_number }}</p>
                </div>
                <div class="text-left">
                    <p class="text-gray-600">Total Amount</p>
                    <p class="font-bold text-lg text-green-600">RM{{ number_format($order->total, 2) }}</p>
                </div>
            </div>
        </div>

        @if($admin && $admin->qr_code)
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-8">
            <div class="text-center">
                <h3 class="font-bold text-blue-800 mb-4">Scan QR Code to Pay</h3>
                <img src="{{ asset('storage/' . $admin->qr_code) }}" alt="Payment QR Code" class="mx-auto max-w-xs mb-4">
                <p class="text-sm text-blue-600 mb-4">Please scan this QR code with your banking app to complete the payment</p>
                <p class="text-xs text-blue-500">Amount: RM{{ number_format($order->total, 2) }}</p>
            </div>
        </div>
        @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-8">
            <div class="text-center">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl mb-4"></i>
                <h3 class="font-bold text-yellow-800 mb-2">QR Code Not Available</h3>
                <p class="text-sm text-yellow-600">The payment QR code is not set up yet. Please contact support.</p>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <form method="POST" action="{{ route('order.complete-qr-payment', $order->id) }}">
                @csrf
                <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg font-medium hover:bg-green-700">
                    <i class="fas fa-check mr-2"></i> I've Completed the Payment
                </button>
            </form>
            <a href="{{ route('orders') }}" class="w-full bg-gray-200 text-gray-800 py-3 rounded-lg font-medium hover:bg-gray-300 text-center">
                <i class="fas fa-history mr-2"></i> View Order History
            </a>
        </div>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-500">
                After completing the payment, click "I've Completed the Payment" to confirm your order.
            </p>
        </div>
    </div>
</div>
@endsection