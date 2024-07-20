<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BorrowedRoomReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pdfPath;
    public $pdfFileName;
    public $recipientName;

    /**
     * Create a new message instance.
     */
    public function __construct($pdfPath, $pdfFileName, $recipientName)
    {
        $this->pdfPath = $pdfPath;
        $this->pdfFileName = $pdfFileName;
        $this->recipientName = $recipientName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Borrowed Room Report',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $data = [
            'recipient' => $this->recipientName,
        ];

        return new Content(
            view: 'mail.booking-report',
            with: $data,
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath(storage_path('app/') . $this->pdfPath)
                ->as($this->pdfFileName)
                ->withMime('application/pdf')
        ];
    }
}
