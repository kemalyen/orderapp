<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'sku',
        'barcode',
        'image',
    ];
    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];
    protected $attributes = [
        'stock' => 0,
    ];
}
