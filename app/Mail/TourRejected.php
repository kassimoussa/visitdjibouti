<?php

namespace App\Mail;

use App\Models\Tour;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TourRejected extends Mailable
{
    use Queueable, SerializesModels;

    public Tour $tour;

    /**
     * Create a new message instance.
     */
    public function __construct(Tour $tour)
    {
        $this->tour = $tour;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre tour a été refusé - ' . $this->tour->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.tour-rejected',
            with: [
                'tour' => $this->tour,
                'operator' => $this->tour->tourOperator,
                'admin' => $this->tour->approvedBy,
                'rejectionReason' => $this->tour->rejection_reason,
                'tourUrl' => route('operator.tours.edit', $this->tour->id),
            ]
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
