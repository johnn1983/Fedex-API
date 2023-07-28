<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\Request;
use App\Models\Product;
use App\Models\Role;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CreateUserRequest extends Request
{
    public function authorize(): bool
    {
        return $this->user()->role_id == Role::ADMIN;
    }

    public function rules(): array
    {
        $visibilityLevels = join(',', Product::VISIBILITY_LEVELS);

        return [
            'username' => 'string|required|unique:users',
            'tagname' => 'string|required',
            'email' => 'required|email|unique:users,email',
            'password' => 'string|required',
            'role_id' => 'integer|exists:roles,id',
            'description' => 'string|nullable',
            'country' => 'string|nullable',
            'external_link' => 'string|nullable',
            'background_image_id' => 'integer|exists:media,id|nullable',
            'avatar_image_id' => 'integer|exists:media,id|nullable',
            'product_visibility_level' => "integer|in:{$visibilityLevels}"
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        if ($this->has('role_id') && $this->user()->role_id !== Role::ADMIN) {
            throw new AccessDeniedHttpException('User does not exist');
        }
    }
}