<?php
return [
    /**
     * common headers
     */
    'status_error' => 'failed',
    'status_success' => 'success',
    'header_convo' => 'X-Conversation-ID',
    'convo_id_label' => 'Conversation ID: ',
    'log_error_label' => 'Error Code: ',
    'log_error_message' => 'Error Message: ',

    /**
     * success messages
     */
    'success_slave_table' => 'Slave Table Created',
    'registration_success' => 'User Registered Successfully.',
    'useraccount_update_success' => 'User Account Updated',
    'useraccount_deactivated' => 'User Account Deactivated',
    'useraccount_restored' => 'User Account Restored',
    'security_questions_success' => 'Security Questions Saved Successfully.',

    /**
     * error messages
     */
    'error_default' => 'Service is Down! Please contact the administrator.',
    'registration_failure' => 'Sorry! I cannot register this user',
    'error_processing' => 'I cannot process your request, at this moment.',
    'user_not_found' => 'user not found.',
    'unauthorized_login' => 'Unauthorized',
    'account_not_verified' => 'account is not verified',
    'empty_security_questions' => 'no security questions found',
    'unverified_account' => 'account is not verified',

    /**
     * mail spiel
     */
    'verification_email_subject' => 'Ordery Account Verification',
    'verification_email_body' => 'You\'re nearly there! <strong>:full_name:</strong>. <br>'
        . 'We just need to verify your email address to complete your registration. <br>'
        . '<a href=":activation_link:">:activation_spiel:</a><br>'
        . 'Please click on the link to activate your account. <br>'
        . 'If you have not regstered to Ordery, please ignore this email',
    'verification_email_activation_spiel' => 'Click this link to verify your account.',
];