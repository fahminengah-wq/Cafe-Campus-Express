@extends('app')

@section('title', 'Register - Campus Cafe Express')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-md mx-auto bg-white rounded-xl shadow-lg p-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-green-600 mb-2">
                <i class="fas fa-mug-hot mr-2"></i>Campus Café Express
            </h1>
            <h2 class="text-xl font-semibold text-gray-700">Create Account</h2>
        </div>

        <!-- Show errors if any -->
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif
        
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Full Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                @error('name') 
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Email Address *</label>
                <input type="email" name="email" value="{{ old('email') }}" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                @error('email') 
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone') }}" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                @error('phone') 
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Account Type *</label>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="radio" name="role" value="customer" {{ old('role', 'customer') == 'customer' ? 'checked' : '' }} required
                               class="mr-2 text-green-600 focus:ring-green-500">
                        <span class="text-gray-700">Customer - Order food from restaurants</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="role" value="seller" {{ old('role') == 'seller' ? 'checked' : '' }} required
                               class="mr-2 text-green-600 focus:ring-green-500">
                        <span class="text-gray-700">Seller - Manage restaurants and orders</span>
                    </label>
                </div>
                @error('role') 
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Password *</label>
                <input type="password" name="password" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                @error('password') 
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Confirm Password *</label>
                <input type="password" name="password_confirmation" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                @error('password_confirmation') 
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                @enderror
            </div>

            <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg font-medium hover:bg-green-700 mb-4">
                Register
            </button>

            <div class="text-center">
                <p class="text-gray-600">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="text-green-600 font-medium hover:text-green-700">Login</a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection