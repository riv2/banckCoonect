<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Password Reset Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match reasons
    | that are given by the password broker for a password update attempt
    | has failed, such as for an invalid token or invalid new password.
    |
    */

    'reset' => 'Your password has been reset!',
    'sent' => 'We have emailed your password reset link!',
    'throttled' => 'Please wait before retrying.',
    'token' => 'This password reset token is invalid.',
    'user' => "We can't find a user with that email address.",

    // auth/passwords/email.blade.php
    'email.app.title' => 'Reset Password',
    'email.email' => 'E-Mail Address',
    'email.send' => 'Send me Password',

    // auth/passwords/reset.blade.php
    'reset.app.title' => 'Reset Password',
    'reset.email' => 'E-Mail Address',
    'reset.password' => 'Password',
    'reset.password-confirm' => 'Confirm Password',
    'reset.save' => 'Reset Password',

    // auth/passwords/confirm.blade.php
    'confirm.app.title' => 'Confirm Password',
    'confirm.message' => 'Please confirm your password before continuing.',
    'confirm.password' => 'Password',
    'confirm.submit' => 'Confirm Password',
    'confirm.password-forgot' => 'Forgot Your Password?',

];
