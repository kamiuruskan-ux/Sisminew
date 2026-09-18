<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $schoolName;
    public $user;
    public $otp;
    public $portalName;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $otp, $portalName = 'Siswa & Staf')
    {
        $this->schoolName = \App\Models\Setting::get('school_name', 'Sekolah');
        $this->user = $user;
        $this->otp = $otp;
        $this->portalName = $portalName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kode OTP Login - ' . $this->schoolName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.auth.otp',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
