<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Artel\Support\Traits\ModelTrait;

class OrderItem extends Model
{
    use HasFactory;
    use ModelTrait;

    protected $fillable = [
        "order_id",
        "product_title",
        "product_artist",
        "product_height",
        "product_width",
        "product_depth",
        "product_weight",
        "product_colour",
        "quantity",
        "price",
        'product_id',
        'shipping',
        'vat',

    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
