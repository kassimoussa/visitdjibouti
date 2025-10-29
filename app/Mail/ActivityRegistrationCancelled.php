<?php

namespace App\Mail;

use App\Models\ActivityRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ActivityRegistrationCancelled extends Mailable
{
    use Queueable, SerializesModels;

    public ActivityRegistration $registration;

    public string $cancelledBy;

    /**
     * Create a new message instance.
     */
    public function __construct(ActivityRegistration $registration, string $cancelledBy = 'user')
    {
        $this->registration = $registration;
        $this->cancelledBy = $cancelledBy;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Annulation d\'inscription - '.$this->registration->activity->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.activity-registration-cancelled',
            with: [
                'registration' => $this->registration,
                'activity' => $this->registration->activity,
                'cancelledBy' => $this->cancelledBy,
                'operator' => $this->registration->activity->tourOperator,
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
