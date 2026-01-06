<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Student;
use App\Models\UserVoucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        try {
            // Validation
            $request->validate([
                'order_type' => 'required|in:delivery,pickup',
                'delivery_address' => 'required_if:order_type,delivery',
                'pickup_time' => 'required_if:order_type,pickup',
                'payment_method' => 'required|in:qr,cod'
            ]);

            $student = Auth::user();
            $cart = $student->cart;
            
            if (!$cart) {
                return back()->with('error', 'Your cart is empty!');
            }
            
            $cart->load('items');
            
            if ($cart->items->isEmpty()) {
                return back()->with('error', 'Your cart is empty!');
            }

            // Calculate totals
            $subtotal = $cart->total;
            $tax = $subtotal * 0.08;
            $deliveryFee = $request->order_type == 'delivery' ? 2.99 : 0;
            $total = $subtotal + $tax + $deliveryFee;

            // Check for applied voucher
            $discount = 0;
            $appliedVoucher = null;
            if (session('applied_voucher_id')) {
                $userVoucher = UserVoucher::with('voucher')
                    ->where('id', session('applied_voucher_id'))
                    ->where('student_id', $student->id)
                    ->where('is_used', false)
                    ->first();

                if ($userVoucher && $userVoucher->voucher->isValid()) {
                    $appliedVoucher = $userVoucher;
                    $discount = $userVoucher->voucher->calculateDiscount($total);
                    $total -= $discount;
                }
            }

            // Create order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'student_id' => $student->id,
                'status' => 'pending',
                'order_type' => $request->order_type,
                'delivery_address' => $request->delivery_address,
                'pickup_time' => $request->pickup_time,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
            ]);

            // Mark voucher as used if applied
            if ($appliedVoucher) {
                $appliedVoucher->markAsUsed();
                session()->forget('applied_voucher_id');
            }

            // Copy cart items to order items
            foreach ($cart->items as $cartItem) {
                $itemSubtotal = $cartItem->price * $cartItem->quantity;
                OrderItem::create([
                    'order_id' => $order->id,
                    'food_item_id' => $cartItem->food_item_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'subtotal' => $itemSubtotal,
                ]);
            }

            // Create payment - all payments start as pending
            $paymentStatus = 'pending';

            Payment::create([
                'order_id' => $order->id,
                'amount' => $total,
                'method' => $request->payment_method,
                'status' => $paymentStatus,
                'transaction_id' => 'TXN' . time() . rand(1000, 9999),
            ]);

            // Clear cart
            $cart->items()->delete();
            $cart->total = 0;
            $cart->save();

            // Redirect based on payment method
            if ($request->payment_method == 'qr') {
                return redirect()->route('order.qr-payment', $order->id)
                                 ->with('success', 'Order placed successfully! Please complete your payment.');
            } else {
                return redirect()->route('order.confirmation', $order->id)
                                 ->with('success', 'Order placed successfully!');
            }
        } catch (\Exception $e) {
            \Log::error('Checkout error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while processing your order. Please try again.');
        }
    }

    public function confirmation($id)
    {
        $order = Order::with(['items.foodItem', 'payment'])->findOrFail($id);
        
        if (Auth::id() != $order->student_id) {
            abort(403);
        }
        
        return view('orders.confirmation', compact('order'));
    }

    public function qrPayment($id)
    {
        $order = Order::with(['items.foodItem', 'payment'])->findOrFail($id);
        
        if (Auth::id() != $order->student_id) {
            abort(403);
        }

        if ($order->payment->method != 'qr') {
            return redirect()->route('order.confirmation', $order->id);
        }

        // Get admin QR code
        $admin = Student::where('is_admin', true)->first();
        
        return view('orders.qr-payment', compact('order', 'admin'));
    }

    public function completeQrPayment($id)
    {
        $order = Order::findOrFail($id);
        
        if (Auth::id() != $order->student_id) {
            abort(403);
        }

        if ($order->payment->method == 'qr' && $order->payment->status == 'pending') {
            $order->payment->update(['status' => 'completed']);
        }

        return redirect()->route('order.confirmation', $order->id)
                         ->with('success', 'Payment completed successfully!');
    }

    public function history()
    {
        $orders = Auth::user()->orders()->with('items.foodItem')->latest()->get();
        return view('orders.history', compact('orders'));
    }
}