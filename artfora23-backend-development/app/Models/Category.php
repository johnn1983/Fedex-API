<?php

namespace App\Models;

use Artel\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use ModelTrait;

    protected $fillable = [
        'title',
        'parent_id'
    ];

    protected $hidden = ['pivot'];

    public function parent() {
        return $this->belongsTo(Category::class);
    }

    public function children() {
        return $this->hasMany(Category::class, foreignKey: 'parent_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
