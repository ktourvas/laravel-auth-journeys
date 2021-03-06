<?php

namespace laravel\auth\journeys\Entities;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

trait HasCustomPasswordResetNotification
{
    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $notification = new ResetPasswordNotification($token);

        $notification->toMailUsing( function () use ($token) {
            return (new MailMessage)
                ->subject(Lang::get('Reset Password Notification'))
                ->line(Lang::get('You are receiving this email because we received a password reset request for your account.'))
                ->action(Lang::get('Reset Password'), url(config('app.url').route('password.reset', [
                    'token' => $token,
// Sending the email address of the user over a url variable may be considered as a security risk.
// Uncomment the following in order to return to the normal laravel functionality
//                        'email' => $notifiable->getEmailForPasswordReset()

                    ], false)))
                ->line(Lang::get('This password reset link will expire in :count minutes.', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]))
                ->line(Lang::get('If you did not request a password reset, no further action is required.'));

        });

        $this->notify($notification);

    }

}