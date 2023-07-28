<?php

namespace App\Mails;

class TwoFactorAuthenticationMail extends BaseMail
{
    public function __construct($to, array $data)
    {
        parent::__construct(
            $to,
            $data,
            'ARTfora. 2FA code',
            'emails.two_factor_authentication'
        );
    }
}