<?php

namespace App\Mail;

use App\Models\SpmbRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SpmbStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $registration;
    public $schoolName;
    public $status;

    /**
     * Create a new message instance.
     */
    public function __construct(SpmbRegistration $registration, $status)
    {
        $this->registration = $registration;
        $this->schoolName = \App\Models\Setting::get('school_name', 'School');
        $this->status = $status;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->status) {
            'accepted' => 'Selamat! Pendaftaran Diterima - ' . $this->schoolName,
            'rejected' => 'Informasi Pendaftaran - ' . $this->schoolName,
            'verified' => 'Pendaftaran Terverifikasi - ' . $this->schoolName,
            default => 'Update Status Pendaftaran - ' . $this->schoolName,
        };

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.spmb.status',
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
