protected $routeMiddleware = [
    // ... other middleware
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
    'seller' => \App\Http\Middleware\SellerMiddleware::class,
];