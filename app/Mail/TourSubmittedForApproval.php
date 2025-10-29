<?php

namespace App\Mail;

use App\Models\Tour;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TourSubmittedForApproval extends Mailable
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
            subject: 'Nouveau tour en attente d\'approbation - ' . $this->tour->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.tour-submitted-for-approval',
            with: [
                'tour' => $this->tour,
                'operator' => $this->tour->tourOperator,
                'creator' => $this->tour->createdBy,
                'approvalUrl' => route('admin.tours.show', $this->tour->id),
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
