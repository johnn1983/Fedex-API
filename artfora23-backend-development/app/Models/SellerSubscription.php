<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Artel\Support\Traits\ModelTrait;

class SellerSubscription extends Model
{
    use HasFactory;
    use ModelTrait;
    protected $fillable = [
        'seller_id',
        'subscription_id',
        'price_id',
        'stripe_status',
        'price',
        'type'
    ];
    protected $guarded = [
        'end_date',
        'start_date ',
    ];
}
