@extends('app')

@section('title', 'Orders Management - Seller Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Orders Management</h1>
        <p class="text-gray-600">Manage orders for your restaurants</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">All Orders</h2>
        </div>

        <div class="overflow-x-auto">
            @if($orders->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($orders as $order)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $order->order_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $order->student->name }}
                                <br>
                                <span class="text-gray-500">{{ $order->student->email }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                @foreach($order->items as $item)
                                    {{ $item->quantity }}x {{ $item->foodItem->name }}<br>
                                @endforeach
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${{ number_format($order->total, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form method="POST" action="{{ route('seller.orders.update-status', $order) }}" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <select name="seller_status" onchange="this.form.submit()" class="text-xs px-2 py-1 border rounded
                                        @if($order->seller_status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->seller_status == 'preparing') bg-orange-100 text-orange-800
                                        @elseif($order->seller_status == 'ready') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        <option value="pending" {{ $order->seller_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="preparing" {{ $order->seller_status == 'preparing' ? 'selected' : '' }}>Preparing</option>
                                        <option value="ready" {{ $order->seller_status == 'ready' ? 'selected' : '' }}>Ready</option>
                                        <option value="completed" {{ $order->seller_status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                </form>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $order->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button onclick="showOrderDetails({{ $order->id }})" class="text-blue-600 hover:text-blue-900 mr-2">Details</button>
                                @if($order->seller_notes)
                                    <button onclick="showNotes('{{ $order->seller_notes }}')" class="text-gray-600 hover:text-gray-900">Notes</button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="px-6 py-8 text-center">
                    <p class="text-gray-500">No orders found for your restaurants.</p>
                </div>
            @endif
        </div>

        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Order Details Modal -->
<div id="orderDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Order Details</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="orderDetailsContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function showOrderDetails(orderId) {
    // For now, just show a simple alert. In a real app, you'd fetch order details via AJAX
    alert('Order details for order ID: ' + orderId);
}

function showNotes(notes) {
    alert('Seller Notes: ' + notes);
}

function closeModal() {
    document.getElementById('orderDetailsModal').classList.add('hidden');
}
</script>
@endsection