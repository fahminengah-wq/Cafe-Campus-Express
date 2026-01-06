<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'total',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function updateTotal()
{
    $this->total = $this->items->sum(function($item) {
        return $item->price * $item->quantity;
    });
    $this->save();
    return $this->total;
}
}