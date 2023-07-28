<?php

namespace App\Mails;

class AccountConfirmationMail extends BaseMail
{
    public function __construct($to, array $data)
    {
        parent::__construct(
            $to,
            $data,
            'Account verification',
            'emails.confirm_account'
        );
    }
}