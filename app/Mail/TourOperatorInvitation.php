<?php

namespace App\Mail;

use App\Models\TourOperatorUser;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TourOperatorInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public TourOperatorUser $user,
        public string $password,
        public string $loginUrl
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invitation - Accès Tour Operator Visit Djibouti',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tour-operator-invitation',
            with: [
                'user' => $this->user,
                'password' => $this->password,
                'loginUrl' => $this->loginUrl,
                'tourOperator' => $this->user->tourOperator,
            ],
        );
    }
}
