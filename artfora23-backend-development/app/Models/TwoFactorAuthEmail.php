<?php

namespace App\Models;

use Artel\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class TwoFactorAuthEmail extends Model
{
    use ModelTrait;

    protected $fillable = [
        'email',
        'code',
    ];

    protected $hidden = ['pivot'];
}
