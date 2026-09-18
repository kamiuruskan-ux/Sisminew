<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $token;
    public $schoolName;

    /**
     * Create a new notification instance.
     */
    public function __construct($token, $schoolName = null)
    {
        $this->token = $token;
        $this->schoolName = $schoolName ?? config('app.name');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = url(route('reset-password', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Reset Password - ' . $this->schoolName)
            ->view('emails.auth.reset-password', [
                'token' => $this->token,
                'user' => $notifiable,
                'schoolName' => $this->schoolName,
                'resetUrl' => $resetUrl,
            ]);
    }
}
