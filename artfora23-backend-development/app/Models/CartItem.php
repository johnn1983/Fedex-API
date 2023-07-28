<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Artel\Support\Traits\ModelTrait;

class CartItem extends Model
{
    use HasFactory;
    use ModelTrait;
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'shipping',
        'vat',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)
            ->with(['avatar_image', 'background_image']);
    }
    public function product()
    {
        return $this->belongsTo(Product::class)
            ->with(['media', 'user']);
    }
}
