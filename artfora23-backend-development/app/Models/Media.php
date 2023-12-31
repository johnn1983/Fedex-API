<?php

namespace App\Models;

use Artel\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class Media extends Model
{
    use ModelTrait;

    protected $fillable = [
        'link',
        'name',
        'owner_id',
        'is_public'
    ];

    protected $casts = [
        'is_public' => 'boolean'
    ];

    protected $hidden = ['pivot'];

    public function scopeApplyMediaPermissionRestrictions($query)
    {
        if (!JWTAuth::getToken()) {
            $query->where('is_public', true);

            return;
        }

        $user = JWTAuth::toUser();

        if ($user->role_id !== Role::ADMIN) {
            $query->where(function ($subQuery) use ($user) {
                $subQuery
                    ->where('is_public', true)
                    ->orWhere('owner_id', $user->id);
            });
        }
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

}