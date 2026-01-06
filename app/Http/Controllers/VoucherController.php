<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\UserVoucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoucherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $userVouchers = $user->userVouchers()->with('voucher')->get();

        return view('vouchers.index', compact('userVouchers'));
    }

    public function claimFirstOrderVoucher()
    {
        $user = Auth::user();

        // Check if user already has this voucher
        $existingVoucher = UserVoucher::where('student_id', $user->id)
            ->whereHas('voucher', function($query) {
                $query->where('code', 'FIRSTORDER50');
            })->first();

        if ($existingVoucher) {
            return redirect()->back()->with('error', 'You have already claimed this voucher.');
        }

        // Find or create the first order voucher
        $voucher = Voucher::firstOrCreate(
            ['code' => 'FIRSTORDER50'],
            [
                'name' => '50% Off First Order',
                'description' => 'Get 50% off on your first order',
                'type' => 'percentage',
                'value' => 50,
                'min_order_amount' => 10,
                'is_active' => true,
            ]
        );

        // Create user voucher
        UserVoucher::create([
            'student_id' => $user->id,
            'voucher_id' => $voucher->id,
        ]);

        return redirect()->route('vouchers.index')->with('success', 'First order voucher claimed successfully!');
    }

    public function applyVoucher(Request $request)
    {
        $request->validate([
            'voucher_code' => 'required|string',
            'order_total' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();
        $code = strtoupper($request->voucher_code);

        // Find the voucher
        $voucher = Voucher::where('code', $code)->first();

        if (!$voucher) {
            return response()->json(['error' => 'Invalid voucher code.'], 400);
        }

        // Check if user has this voucher and hasn't used it
        $userVoucher = UserVoucher::where('student_id', $user->id)
            ->where('voucher_id', $voucher->id)
            ->where('is_used', false)
            ->first();

        if (!$userVoucher) {
            return response()->json(['error' => 'You do not have this voucher or it has already been used.'], 400);
        }

        // Check if voucher is valid
        if (!$voucher->isValid()) {
            return response()->json(['error' => 'This voucher is no longer valid.'], 400);
        }

        // Check minimum order amount
        if ($voucher->min_order_amount && $request->order_total < $voucher->min_order_amount) {
            return response()->json(['error' => "Minimum order amount of $" . $voucher->min_order_amount . " required."], 400);
        }

        // Calculate discount
        $discount = $voucher->calculateDiscount($request->order_total);
        $newTotal = $request->order_total - $discount;

        return response()->json([
            'success' => true,
            'discount' => $discount,
            'new_total' => $newTotal,
            'voucher_id' => $voucher->id,
        ]);
    }

    public function useVoucher(Request $request)
    {
        $request->validate([
            'voucher_id' => 'required|exists:vouchers,id',
        ]);

        $user = Auth::user();

        $userVoucher = UserVoucher::where('student_id', $user->id)
            ->where('voucher_id', $request->voucher_id)
            ->where('is_used', false)
            ->first();

        if (!$userVoucher) {
            return response()->json(['error' => 'Voucher not found or already used.'], 400);
        }

        $userVoucher->markAsUsed();

        return response()->json(['success' => true]);
    }
}
