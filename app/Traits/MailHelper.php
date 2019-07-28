<?php

namespace App\Traits;
use Mail;

trait MailHelper
{
    public function accountVerificationMail(array $params = [])
    {
        $email = preg_replace($toReplace, $fromReplace, $params[]);
        $mailboxParams = [
            'mail_content' => $email,
            'email_to' => $params['to_email'],
            'name_to' => $params['to_name'],
            'email_from' => env('MAIL_FROM_ADDRESS'),
            'name_from' => env('MAIL_FROM_NAME'),
        ];

        $mailbox = Mail::send('account_activation_mail', $mailboxParams, function($message) use ($mailboxParams) {
            $message->from($mailboxParams['email_from'], $mailboxParams['name_from']);
            $message->to($mailboxParams['email_to'], $mailboxParams['name_to'])
                ->subject(__('messages.verification_email_subject'));
        });

        return $mailbox;
    }
}