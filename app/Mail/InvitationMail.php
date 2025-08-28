<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invitation;
    public $pdfPath;
    public $recipientName;

    /**
     * Create a new message instance.
     */
    public function __construct($invitation, $pdfPath, $recipientName = null)
    {
        $this->invitation = $invitation;
        $this->pdfPath = $pdfPath;
        $this->recipientName = $recipientName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Undangan Pernikahan ' . $this->invitation->groom_name . ' & ' . $this->invitation->bride_name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invitation',
            with: [
                'invitation' => $this->invitation,
                'recipientName' => $this->recipientName,
                'invitationUrl' => route('user.invitation.preview', $this->invitation->slug)
            ]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        // Only attach PDF if path is provided
        if ($this->pdfPath && file_exists($this->pdfPath)) {
            return [
                Attachment::fromPath($this->pdfPath)
                    ->as('Undangan-' . $this->invitation->groom_name . '-' . $this->invitation->bride_name . '.pdf')
                    ->withMime('application/pdf'),
            ];
        }
        
        return [];
    }
}
