@extends('app')

@section('title', 'Test Page')

@section('content')
<div class="container" style="padding: 2rem;">
    <h1 style="color: #10B981; margin-bottom: 1rem;">Test Page - Layout is Working!</h1>
    <p>If you can see this, the layout is working correctly.</p>
    
    <div style="margin-top: 2rem; padding: 1rem; background: #F3F4F6; border-radius: 0.5rem;">
        <h2 style="color: #374151; margin-bottom: 0.5rem;">Debug Information:</h2>
        <p><strong>Auth Status:</strong> {{ auth()->check() ? 'Logged In' : 'Not Logged In' }}</p>
        <p><strong>Current URL:</strong> {{ url()->current() }}</p>
        <p><strong>App Name:</strong> {{ config('app.name') }}</p>
    </div>
    
    <div style="margin-top: 2rem;">
        <h3 style="color: #374151; margin-bottom: 1rem;">Test Links:</h3>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <a href="/" style="background: #10B981; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; text-decoration: none;">Home</a>
            <a href="/login" style="background: #3B82F6; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; text-decoration: none;">Login</a>
            <a href="/register" style="background: #8B5CF6; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; text-decoration: none;">Register</a>
            <a href="/restaurants" style="background: #F59E0B; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; text-decoration: none;">Restaurants</a>
        </div>
    </div>
</div>
@endsection