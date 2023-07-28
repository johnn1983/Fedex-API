<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactUs\CommissionRequest;
use App\Http\Requests\ContactUs\ContactUsRequest;
use App\Mails\CommissionRequestMail;
use App\Mails\ContactUsMail;
use App\Services\SettingService;
use App\Services\UserService;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Mail;

class ContactUsController extends BaseController
{
    public function contactUs(ContactUsRequest $request, SettingService $service)
    {
        $email = $service->get('contact_us.email');

        if ($email) {
            $mail = new ContactUsMail($email, $request->onlyValidated());
            Mail::queue($mail);    
        }

        $mail = new ContactUsMail($request->input('email'), $request->onlyValidated());
        Mail::queue($mail);

        return response()->json([ 'status' => 'Success' ]);
    }

    public function commission(
        CommissionRequest $request,
        UserService $userService,
        SettingService $settingService
    ) {
        $data = $request->onlyValidated();
        $data['user'] = $userService->find($request->route('id'));

        $email = $settingService->get('contact_us.email');

        $mail = new CommissionRequestMail($email, $data);
        $mail->cc($request->input('email'));
        Mail::queue($mail);

        return response()->json([ 'status' => 'Success' ]);
    }
}