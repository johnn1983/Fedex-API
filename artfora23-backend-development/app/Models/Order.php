<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        "user_id",
        "total", 
        "vat",
        "currency",
        "shipping",
        "inv_address",
        "inv_address2", 
        "inv_postal", 
        "inv_city", 
        "inv_state", 
        "inv_country",
        "inv_phone",
        "inv_email",
        "inv_att",
        "dev_address", 
        "dev_address2", 
        "dev_postal", 
        "dev_city", 
        "dev_state", 
        "dev_country", 
        "dev_phone",
        "dev_email",
        "dev_att",
        "order_status", 
        "transaction_id", 
        "payment_mode", 
        'total'
    ];
}
