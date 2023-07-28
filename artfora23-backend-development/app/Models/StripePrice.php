<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Artel\Support\Traits\ModelTrait;


class StripePrice extends Model
{
    use ModelTrait;
    use HasFactory;
    protected $fillable = [
        'price_id',
        'price',
        'type'
    ];
}
