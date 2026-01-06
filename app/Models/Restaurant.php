<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cuisine_type',
        'operating_hours',
        'description',
        'image_url',
        'location',
        'phone',
        'opening_hours',
        'is_open',
        'seller_id',
    ];

    public function foodItems()
    {
        return $this->hasMany(FoodItem::class);
    }

    public function seller()
    {
        return $this->belongsTo(Student::class, 'seller_id');
    }
}