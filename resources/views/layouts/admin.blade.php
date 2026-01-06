<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - Campus Cafe Express')</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom Styles -->
    <style>
        body { font-family: 'Segoe UI', system-ui, sans-serif; }
        .container { max-width: 1200px; }
    </style>

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-6">
                    <a href="/admin/dashboard" class="text-2xl font-bold text-green-600">
                        <i class="fas fa-mug-hot mr-2"></i>Admin Panel
                    </a>
                    <div class="hidden md:flex space-x-4">
                        <a href="/admin/dashboard" class="text-gray-700 hover:text-green-600 font-medium">Dashboard</a>
                        <a href="/admin/restaurants" class="text-gray-700 hover:text-green-600 font-medium">Restaurants</a>
                        <a href="/admin/food-items" class="text-gray-700 hover:text-green-600 font-medium">Menu Items</a>
                        <a href="/admin/orders" class="text-gray-700 hover:text-green-600 font-medium">Orders</a>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">Welcome, {{ Auth::user()->name }}</span>
                    <a href="/" class="text-gray-700 hover:text-green-600">← Back to Site</a>
                    <form action="/logout" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-green-600">Logout</button>
                    </form>
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
        <div class="container mx-auto px-4 py-6 text-center">
            <p>&copy; {{ date('Y') }} Campus Café Express - Admin Panel</p>
        </div>
    </footer>
</body>
</html>