<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Campus Cafe Express')</title>
    
    <!-- Tailwind CSS CDN (QUICK FIX) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <style>
        /* Make sure everything looks good */
        body { font-family: 'Segoe UI', system-ui, sans-serif; }
        .container { max-width: 1200px; }
        
        /* Green theme colors */
        .bg-green-500 { background-color: #10b981 !important; }
        .bg-green-600 { background-color: #059669 !important; }
        .text-green-600 { color: #059669 !important; }
        .hover\:bg-green-700:hover { background-color: #047857 !important; }
        
        /* Yellow/orange for buttons */
        .bg-yellow-500 { background-color: #f59e0b !important; }
        .hover\:bg-yellow-600:hover { background-color: #d97706 !important; }
        
        /* Gradients */
        .bg-gradient-to-r { background-image: linear-gradient(to right, var(--tw-gradient-stops)) !important; }
        .from-green-500 { --tw-gradient-from: #10b981 !important; }
        .to-green-600 { --tw-gradient-to: #059669 !important; }
        
        .from-yellow-400 { --tw-gradient-from: #fbbf24 !important; }
        .to-orange-500 { --tw-gradient-to: #f97316 !important; }
    </style>
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <a href="/" class="text-2xl font-bold text-green-600">
                    <i class="fas fa-mug-hot mr-2"></i>Campus Café Express
                </a>
                
                <div class="hidden md:flex space-x-6">
                    <a href="/" class="text-gray-700 hover:text-green-600 font-medium">Home</a>
                    <a href="/restaurants" class="text-gray-700 hover:text-green-600 font-medium">Restaurants</a>
                    @auth
                        <a href="/cart" class="text-gray-700 hover:text-green-600 font-medium relative">
                            <i class="fas fa-shopping-cart"></i> Cart
                            @if(Auth::user()->cart && Auth::user()->cart->items && Auth::user()->cart->items->count() > 0)
                                <span id="cart-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                    {{ Auth::user()->cart->items->sum('quantity') }}
                                </span>
                            @endif
                        </a>
                        <a href="/orders/history" class="text-gray-700 hover:text-green-600 font-medium">
                            <i class="fas fa-history"></i> Order History
                        </a>
                        @if(Auth::user()->isAdmin())
                            <a href="/admin/dashboard" class="text-gray-700 hover:text-green-600 font-medium">
                                <i class="fas fa-cog"></i> Admin
                            </a>
                        @endif
                        @if(Auth::user()->isSeller())
                            <a href="/seller/dashboard" class="text-gray-700 hover:text-green-600 font-medium">
                                <i class="fas fa-store"></i> Seller Dashboard
                            </a>
                        @endif
                    @endauth
                </div>
                
                <div class="flex items-center space-x-4">
                    @guest
                        <a href="/login" class="text-gray-700 hover:text-green-600">Login</a>
                        <a href="/register" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                            Register
                        </a>
                    @else
                        <span class="text-gray-700">Welcome, {{ Auth::user()->name }}</span>
                        <a href="/profile" class="text-gray-700 hover:text-green-600">Profile</a>
                        <form action="/logout" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-700 hover:text-green-600">Logout</button>
                        </form>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-12">
        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">About</h3>
                    <p class="text-gray-300">We are IIUM Student from KICT doing this website as our group project for course BICS2 2306 Section 6 .Campus Café Express - Your campus food delivery solution.</p>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Contact Us</h3>
                    <p class="text-gray-300">Email: MatchaKopi>@gmail.com</p>
                    <p class="text-gray-300">Phone: +6019-483-8992</p>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Legal</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-300 hover:text-white">Terms & Conditions</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} Campus Café Express. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
@guest
    <a href="/login" class="text-gray-700 hover:text-green-600">Login</a>
    <a href="/admin/login" class="text-gray-700 hover:text-green-600">Admin Login</a>
    <a href="/register" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
        Register
    </a>
@else
    @if(Auth::user()->isAdmin())
        <a href="/admin/dashboard" class="text-gray-700 hover:text-green-600">
            <i class="fas fa-cog mr-1"></i> Admin Panel
        </a>
    @endif
    <span class="text-gray-700">Welcome, {{ Auth::user()->name }}</span>
    <a href="/profile" class="text-gray-700 hover:text-green-600">Profile</a>
    <form action="/logout" method="POST" class="inline">
        @csrf
        <button type="submit" class="text-gray-700 hover:text-green-600">Logout</button>
    </form>
@endguest