<?php

namespace App\Mails;

class CommissionRequestMail extends BaseMail
{
    public function __construct($to, array $data)
    {
        parent::__construct(
            $to,
            $data,
            'New commission request',
            'emails.commission_request'
        );
    }
}