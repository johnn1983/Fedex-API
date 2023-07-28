<?php

namespace App\Models;

use Artel\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class Filter extends Model
{
    use ModelTrait;

    protected $fillable = [
        'filter',
    ];
    
    const SEARCH_QUERY_FIELDS = [
        'filter'
    ];

}
