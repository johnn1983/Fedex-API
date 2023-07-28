<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\Request;
use App\Models\Product;

class UpdateProfileRequest extends Request
{
    public function rules(): array
    {
        $userId = $this->user() ? $this->user()->id : '';
        $visibilityLevels = join(',', Product::VISIBILITY_LEVELS);

        return [
            'username' => "string|unique:users,username,{$userId}",
            'tagname' => "string|unique:users,tagname,{$userId}",
            'email' => "email|unique:users,email,{$userId}",
            'password' => 'string|same:confirm',
            'confirm' => 'string',
            'role_id' => 'integer|exists:roles,id',
            'description' => 'string|nullable',
            'country' => 'string|nullable',
            'external_link' => 'string|nullable',
            'background_image_id' => 'integer|exists:media,id|nullable',
            'avatar_image_id' => 'integer|exists:media,id|nullable',
            'product_visibility_level' => "integer|in:{$visibilityLevels}",
            'data' => 'array',
            'data.media_filters' => 'array',
            'more_external_link' => 'array',
            'inv_name'=>'string',
            "inv_address" =>'string',
            "inv_address2"=>'string|nullable', 
            "inv_postal"=>'string', 
            "inv_city"=>'string', 
            "inv_state"=>'string', 
            "inv_country"=>'string',
            "inv_phone"=>'string|nullable',
            "inv_email"=>'string',
            "inv_att"=>'string|nullable',
            "dev_name"=>'string',
            "dev_address"=>'string', 
            "dev_address2"=>'string|nullable', 
            "dev_postal"=>'string', 
            "dev_city"=>'string', 
            "dev_state"=>'string', 
            "dev_country"=>'string', 
            "dev_phone"=>'string|nullable',
            "dev_email"=>'string',
            "dev_att"=>'string|nullable',
            "sel_name"=>'string',
            "sel_address"=>'string', 
            "sel_address2"=>'string|nullable', 
            "sel_postal"=>'string', 
            "sel_city"=>'string', 
            "sel_state"=>'string', 
            "sel_country"=>'string', 
            "sel_phone"=>'string|nullable',
            "sel_email"=>'string',
            "sel_att"=>'string|nullable',
            "seller_support"=>'boolean'


        ];
    }
}