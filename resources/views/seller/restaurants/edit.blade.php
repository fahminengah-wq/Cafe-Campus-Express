@extends('app')

@section('title', 'Edit Restaurant - Seller Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('seller.restaurants') }}" class="inline-flex items-center text-green-600 hover:text-green-700 mb-4">
                <i class="fas fa-mug-hot mr-2"></i> Back to Restaurants
            </a>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Edit Restaurant</h1>
            <p class="text-gray-600">Update restaurant information</p>
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

        <form action="{{ route('seller.restaurants.update', $restaurant) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-lg p-8">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Restaurant Name *</label>
                <input type="text" name="name" value="{{ old('name', $restaurant->name) }}" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Cuisine Type *</label>
                <select name="cuisine_type" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Select cuisine type</option>
                    <option value="Malay" {{ old('cuisine_type', $restaurant->cuisine_type) == 'Malay' ? 'selected' : '' }}>Malay</option>
                    <option value="Chinese" {{ old('cuisine_type', $restaurant->cuisine_type) == 'Chinese' ? 'selected' : '' }}>Chinese</option>
                    <option value="Indian" {{ old('cuisine_type', $restaurant->cuisine_type) == 'Indian' ? 'selected' : '' }}>Indian</option>
                    <option value="Western" {{ old('cuisine_type', $restaurant->cuisine_type) == 'Western' ? 'selected' : '' }}>Western</option>
                    <option value="Japanese" {{ old('cuisine_type', $restaurant->cuisine_type) == 'Japanese' ? 'selected' : '' }}>Japanese</option>
                    <option value="Korean" {{ old('cuisine_type', $restaurant->cuisine_type) == 'Korean' ? 'selected' : '' }}>Korean</option>
                    <option value="Thai" {{ old('cuisine_type', $restaurant->cuisine_type) == 'Thai' ? 'selected' : '' }}>Thai</option>
                    <option value="Italian" {{ old('cuisine_type', $restaurant->cuisine_type) == 'Italian' ? 'selected' : '' }}>Italian</option>
                    <option value="Other" {{ old('cuisine_type', $restaurant->cuisine_type) == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Description</label>
                <textarea name="description" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('description', $restaurant->description) }}</textarea>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Operating Hours</label>
                <input type="text" name="operating_hours" value="{{ old('operating_hours', $restaurant->operating_hours) }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Current Restaurant Image</label>
                @if($restaurant->image_url)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $restaurant->image_url) }}" alt="{{ $restaurant->name }}" class="w-32 h-32 object-cover rounded-lg">
                    </div>
                @endif
                <label class="block text-gray-700 font-medium mb-2">Update Restaurant Image</label>
                <input type="file" name="image" accept="image/*"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                <p class="text-sm text-gray-500 mt-1">Leave empty to keep current image</p>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('seller.restaurants') }}" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600">
                    Cancel
                </a>
                <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700">
                    <i class="fas fa-save mr-2"></i> Update Restaurant
                </button>
            </div>
        </form>
    </div>
</div>
@endsection