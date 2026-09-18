<?php

namespace App\Mail;

use App\Models\SpmbRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SpmbRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $registration;
    public $schoolName;

    /**
     * Create a new message instance.
     */
    public function __construct(SpmbRegistration $registration)
    {
        $this->registration = $registration;
        $this->schoolName = \App\Models\Setting::get('school_name', 'School');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Konfirmasi Pendaftaran - ' . $this->schoolName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.spmb.registration',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
