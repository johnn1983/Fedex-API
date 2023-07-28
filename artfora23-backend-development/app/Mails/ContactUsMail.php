<?php

namespace App\Mails;

class ContactUsMail extends BaseMail
{
    public function __construct($to, array $data)
    {
        parent::__construct(
            $to,
            $data,
            'New contact us request',
            'emails.contact_us'
        );
    }
}