<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\FoodItem;
use App\Models\UserVoucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $cart = $user->cart;

        // Ensure user has a cart
        if (!$cart) {
            $cart = $user->cart()->create(['total' => 0]);
        }

        $cartItems = $cart->items()->with('foodItem')->get();

        $subtotal = $cartItems->sum('subtotal');
        $tax = $subtotal * 0.08;
        $deliveryFee = 2.99;
        $total = $subtotal + $tax + $deliveryFee;

        // Get user's available vouchers
        $availableVouchers = $user->userVouchers()
            ->with('voucher')
            ->where('is_used', false)
            ->whereHas('voucher', function($query) {
                $query->where('is_active', true)
                      ->where(function($q) {
                          $q->whereNull('expires_at')
                            ->orWhere('expires_at', '>', now());
                      });
            })
            ->get();

        $appliedVoucher = null;
        $discount = 0;
        $finalTotal = $total;

        // Check if a voucher is applied in session
        if (session('applied_voucher_id')) {
            $userVoucher = UserVoucher::with('voucher')
                ->where('id', session('applied_voucher_id'))
                ->where('student_id', $user->id)
                ->where('is_used', false)
                ->first();

            if ($userVoucher && $userVoucher->voucher->isValid()) {
                $appliedVoucher = $userVoucher;
                $discount = $userVoucher->voucher->calculateDiscount($total);
                $finalTotal = $total - $discount;
            } else {
                // Clear invalid voucher from session
                session()->forget('applied_voucher_id');
            }
        }

        return view('cart.index', compact(
            'cartItems',
            'subtotal',
            'tax',
            'deliveryFee',
            'total',
            'availableVouchers',
            'appliedVoucher',
            'discount',
            'finalTotal'
        ));
    }

    public function add(Request $request)
    {
        // Simple validation - FIXED
        $validated = $request->validate([
            'food_item_id' => 'required|exists:food_items,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $student = Auth::user();
        $cart = $student->cart ?? $student->cart()->create(['total' => 0]);
        
        $foodItem = FoodItem::findOrFail($request->food_item_id);
        
        // Check if item already in cart
        $cartItem = $cart->items()->where('food_item_id', $foodItem->id)->first();
        
        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->subtotal = $cartItem->quantity * $cartItem->price;
            $cartItem->save();
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'food_item_id' => $foodItem->id,
                'quantity' => $request->quantity,
                'price' => $foodItem->price,
                'subtotal' => $foodItem->price * $request->quantity,
            ]);
        }
        
        $cart->updateTotal();

        // Check if this is an AJAX request
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Item added to cart!',
                'cart_count' => $cart->items()->sum('quantity')
            ]);
        }
        
        return back()->with('success', 'Item added to cart!');
    }

    public function update(Request $request, $id)
    {
        // Simple validation - FIXED
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = CartItem::findOrFail($id);
        $cartItem->quantity = $request->quantity;
        $cartItem->save();
        
        $cartItem->cart->updateTotal();
        
        return back()->with('success', 'Cart updated!');
    }

    public function remove($id)
    {
        $cartItem = CartItem::findOrFail($id);
        $cartItem->delete();
        
        $cartItem->cart->updateTotal();
        
        return back()->with('success', 'Item removed from cart!');
    }

    public function applyVoucher(Request $request)
    {
        $request->validate([
            'voucher_id' => 'required|exists:user_vouchers,id',
        ]);

        $user = Auth::user();

        $userVoucher = UserVoucher::with('voucher')
            ->where('id', $request->voucher_id)
            ->where('student_id', $user->id)
            ->where('is_used', false)
            ->first();

        if (!$userVoucher) {
            return back()->with('error', 'Voucher not found or already used.');
        }

        if (!$userVoucher->voucher->isValid()) {
            return back()->with('error', 'This voucher is no longer valid.');
        }

        // Store voucher in session
        session(['applied_voucher_id' => $userVoucher->id]);

        return back()->with('success', 'Voucher applied successfully!');
    }

    public function removeVoucher()
    {
        session()->forget('applied_voucher_id');
        return back()->with('success', 'Voucher removed successfully!');
    }
}