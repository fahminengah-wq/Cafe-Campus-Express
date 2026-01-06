<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect('/');
        }
        
        return back()->with('error', 'Invalid credentials');
    }

  public function register(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:students',
        'phone' => 'nullable|string|max:20',
        'password' => 'required|string|min:6|confirmed',
        'role' => 'required|in:customer,seller',
    ]);
    
    try {
        $student = Student::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);
        
        $student->cart()->create(['total' => 0]);
        Auth::login($student);
        
        return redirect('/');
        
    } catch (\Exception $e) {
        return back()->with('error', 'Registration failed: ' . $e->getMessage());
    }
}
    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}