<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'min_order_amount',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function userVouchers()
    {
        return $this->hasMany(UserVoucher::class);
    }

    public function isValid()
    {
        return $this->is_active && (!$this->expires_at || $this->expires_at->isFuture());
    }

    public function calculateDiscount($orderTotal)
    {
        if (!$this->isValid() || ($this->min_order_amount && $orderTotal < $this->min_order_amount)) {
            return 0;
        }

        if ($this->type === 'percentage') {
            return $orderTotal * ($this->value / 100);
        }

        return min($this->value, $orderTotal);
    }
}
