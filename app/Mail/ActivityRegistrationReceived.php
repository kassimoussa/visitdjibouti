<?php

namespace App\Mail;

use App\Models\ActivityRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ActivityRegistrationReceived extends Mailable
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
            subject: 'Nouvelle inscription à votre activité - '.$this->registration->activity->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.activity-registration-received',
            with: [
                'registration' => $this->registration,
                'activity' => $this->registration->activity,
                'customer' => $this->registration->customer_name,
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
