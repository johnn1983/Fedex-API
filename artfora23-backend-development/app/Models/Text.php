<?php

namespace App\Models;

use Artel\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class Text extends Model
{
    use ModelTrait;

    protected $fillable = [
        'text_name',
        'text_content',
        'text_colour'
    ];
    
    const SEARCH_QUERY_FIELDS = [
        'text_name'
    ];

}
