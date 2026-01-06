@extends('app')

@section('title', 'Add Menu Item - Seller Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('seller.food-items') }}" class="inline-flex items-center text-green-600 hover:text-green-700 mb-4">
                <i class="fas fa-mug-hot mr-2"></i> Back to Menu Items
            </a>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Add New Menu Item</h1>
            <p class="text-gray-600">Add a new food item to your restaurant</p>
        </div>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('seller.food-items.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-lg p-8">
            @csrf

            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Restaurant *</label>
                <select name="restaurant_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Select restaurant</option>
                    @foreach($restaurants as $restaurant)
                        <option value="{{ $restaurant->id }}" {{ old('restaurant_id') == $restaurant->id ? 'selected' : '' }}>
                            {{ $restaurant->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Item Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Description</label>
                <textarea name="description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('description') }}</textarea>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Price (RM) *</label>
                <input type="number" name="price" value="{{ old('price') }}" step="0.01" min="0" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Availability</label>
                <div class="flex items-center">
                    <input type="checkbox" name="available" value="1" {{ old('available', true) ? 'checked' : '' }}
                           class="mr-2 h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                    <span class="text-gray-700">Item is available for ordering</span>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Food Image</label>
                <input type="file" name="image" accept="image/*"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                <p class="text-sm text-gray-500 mt-1">Upload a high-quality image of the food item (optional)</p>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('seller.food-items') }}" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600">
                    Cancel
                </a>
                <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700">
                    <i class="fas fa-save mr-2"></i> Add Menu Item
                </button>
            </div>
        </form>
    </div>
</div>
@endsection