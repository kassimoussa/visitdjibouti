<?php

namespace App\Mail;

use App\Models\ActivityRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ActivityRegistrationConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public ActivityRegistration $registration;

    /**
     * Create a new message instance.
     */
    public function __construct(ActivityRegistration $registration)
    {
        $this->registration = $registration;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre inscription à '.$this->registration->activity->title.' a été confirmée',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.activity-registration-confirmed',
            with: [
                'registration' => $this->registration,
                'activity' => $this->registration->activity,
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
