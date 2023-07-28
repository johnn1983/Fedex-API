<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Artel\Support\Traits\ModelTrait;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, ModelTrait, SoftDeletes;

    /**
     * The functionality of 2fa is copy-pasted from the Remmesa project, that's why sms and otp is here
     * but there were no requirements to implement it so this functionality is kinda works but won't be
     * presented in the design and I bet soon it will be erased from the project.
     * I decided to still keep it because it will allow us to easy scale types of 2fa in future.
     */
    const EMAIL_2FA_TYPE = 'email';
    const SMS_2FA_TYPE = 'sms';
    const OTP_2FA_TYPE = 'otp';

    const TWO_FACTOR_AUTHORIZATION_TYPES = [
        self::EMAIL_2FA_TYPE,
        self::SMS_2FA_TYPE,
        self::OTP_2FA_TYPE,
    ];

    protected $fillable = [
        'username',
        'tagname',
        'email',
        'password',
        'role_id',
        'description',
        'country',
        'external_link',
        'data',
        'background_image_id',
        'avatar_image_id',
        '2fa_type',
        'is_2fa_enabled',
        'otp_secret',
        'product_visibility_level',
        'more_external_link',
        'can_add_product',
        'stripe_account_id',
        'stripe_status',
        'inv_name',
        "inv_address",
        "inv_address2",
        "inv_postal",
        "inv_city",
        "inv_state",
        "inv_country",
        "inv_phone",
        "inv_email",
        "inv_att",
        'dev_name',
        "dev_address",
        'dev_address2',
        "dev_postal",
        'dev_city',
        "dev_state",
        "dev_country",
        "dev_phone",
        "dev_email",
        "dev_att",
        'sel_name',
        "sel_address",
        "sel_address2",
        "sel_postal",
        "sel_city",
        "sel_state",
        "sel_country",
        "sel_phone",
        "sel_email",
        "sel_att",
        "seller_support",
        "buyer_support",
        "stripe_customer_id"
    ];

    protected $guarded = [
        'reset_password_hash',
        'email_verified_at',
        'email_verification_token',
        'email_verification_token_sent_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'reset_password_hash',
        '2fa_type',
        'otp_secret',
        'email_verified_at'
    ];

    protected $casts = [
        'data' => 'array',
        'more_external_link' => 'array'
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function background_image()
    {
        return $this->belongsTo(Media::class);
    }

    public function avatar_image()
    {
        return $this->belongsTo(Media::class);
    }

    public function product()
    {
        return $this->hasOne(Product::class);
    }
}
