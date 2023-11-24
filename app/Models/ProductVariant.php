<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        "size",
        "color",
        "sku",
        "price",
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
