<?php

namespace App\Http\Requests\ContactUs;

use App\Facades\MtCaptcha;
use App\Http\Requests\Request;
use App\Services\UserService;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CommissionRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'string|required',
            'email' => 'string|email|required',
            'text' => 'string|required',
            'mtcaptcha_token' => 'string|required'
        ];
    }

    public function validateResolved()
    {
        $service = app(UserService::class);

        if (!$service->exists($this->route('id'))) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'Product']));
        }

        parent::validateResolved();

        if (!MtCaptcha::verify($this->input('mtcaptcha_token'))) {
            throw new BadRequestHttpException('MTCaptcha verification failed');
        }
    }
}
