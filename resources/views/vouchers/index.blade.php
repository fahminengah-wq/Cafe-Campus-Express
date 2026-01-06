@extends('app')

@section('title', 'My Vouchers - Campus Cafe Express')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">My Vouchers</h1>
        <p class="text-gray-600">Manage your discount vouchers</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($userVouchers as $userVoucher)
        <div class="bg-white rounded-xl shadow-lg overflow-hidden {{ $userVoucher->is_used ? 'opacity-60' : '' }}">
            <div class="bg-gradient-to-r from-yellow-400 to-orange-500 p-6 text-white">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-xl font-bold">{{ $userVoucher->voucher->name }}</h3>
                        <p class="text-yellow-100 mt-1">{{ $userVoucher->voucher->description }}</p>
                    </div>
                    <div class="text-right">
                        @if($userVoucher->voucher->type === 'percentage')
                            <span class="text-3xl font-bold">{{ $userVoucher->voucher->value }}%</span>
                            <p class="text-yellow-100">OFF</p>
                        @else
                            <span class="text-3xl font-bold">${{ $userVoucher->voucher->value }}</span>
                            <p class="text-yellow-100">OFF</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <p class="text-sm text-gray-600">Code:</p>
                        <p class="font-mono font-bold text-gray-800">{{ $userVoucher->voucher->code }}</p>
                    </div>
                    <div class="text-right">
                        @if($userVoucher->voucher->min_order_amount)
                            <p class="text-sm text-gray-600">Min. Order:</p>
                            <p class="font-semibold text-gray-800">${{ number_format($userVoucher->voucher->min_order_amount, 2) }}</p>
                        @endif
                    </div>
                </div>

                @if($userVoucher->voucher->expires_at)
                    <div class="mb-4">
                        <p class="text-sm text-gray-600">Expires:</p>
                        <p class="font-semibold text-gray-800">{{ $userVoucher->voucher->expires_at->format('M d, Y') }}</p>
                    </div>
                @endif

                <div class="flex justify-between items-center">
                    @if($userVoucher->is_used)
                        <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-sm font-medium">
                            Used {{ $userVoucher->used_at ? $userVoucher->used_at->format('M d') : '' }}
                        </span>
                    @else
                        <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-sm font-medium">
                            Available
                        </span>
                    @endif

                    @if(!$userVoucher->is_used)
                        <button onclick="copyCode('{{ $userVoucher->voucher->code }}')"
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm">
                            Copy Code
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <i class="fas fa-ticket-alt text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">No vouchers yet</h3>
            <p class="text-gray-500 mb-6">Claim your first order voucher to get started!</p>
            <a href="{{ route('home') }}" class="bg-yellow-500 text-white px-6 py-3 rounded-lg hover:bg-yellow-700">
                Claim First Order Voucher
            </a>
        </div>
        @endforelse
    </div>

    <!-- How to use vouchers -->
    <div class="mt-12 bg-blue-50 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">How to use your vouchers:</h3>
        <ol class="list-decimal list-inside space-y-2 text-gray-700">
            <li>Go to your cart and click "Apply Voucher"</li>
            <li>Enter your voucher code</li>
            <li>The discount will be applied automatically</li>
            <li>Vouchers can only be used once and expire after the specified date</li>
        </ol>
    </div>
</div>

<script>
function copyCode(code) {
    navigator.clipboard.writeText(code).then(function() {
        // Show success message
        showNotification('Voucher code copied to clipboard!', 'success');
    }, function(err) {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = code;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showNotification('Voucher code copied to clipboard!', 'success');
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 ${
        type === 'success' ? 'bg-green-600' : 'bg-red-600'
    }`;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endsection