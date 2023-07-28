<?php

namespace App\Models;

use Artel\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    use ModelTrait;

    public $timestamps = false;

    protected $fillable = [
        'email',
        'token',
        'created_at'
    ];

    protected $hidden = ['pivot'];
}
