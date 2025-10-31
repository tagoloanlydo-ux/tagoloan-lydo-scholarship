<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $applicationPersonnel;
    public $applicationPersonnelId;

    /**
     * Create a new message instance.
     */
    public function __construct($applicationPersonnel)
    {
        $this->applicationPersonnel = $applicationPersonnel;
        $this->applicationPersonnelId = $applicationPersonnel->application_personnel_id;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Application Approved - LYDO Scholarship',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.application-approved',
            with: [
                'applicant_fname' => $this->applicationPersonnel->applicant_fname,
                'applicant_lname' => $this->applicationPersonnel->applicant_lname,
                'applicationPersonnelId' => $this->applicationPersonnelId,
            ],
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
