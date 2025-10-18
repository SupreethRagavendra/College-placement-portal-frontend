<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $studentName;
    public $status;
    public $rejectionReason;
    public $collegeName;

    /**
     * Create a new message instance.
     */
    public function __construct(string $studentName, string $status, ?string $rejectionReason = null, string $collegeName = 'College Placement Portal')
    {
        $this->studentName = $studentName;
        $this->status = $status;
        $this->rejectionReason = $rejectionReason;
        $this->collegeName = $collegeName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->status === 'approved' 
            ? "🎉 Account Approved - Welcome to {$this->collegeName}!"
            : "Application Status Update - {$this->collegeName}";

        return new Envelope(
            subject: $subject,
            from: new \Illuminate\Mail\Mailables\Address(
                config('mail.from.address'),
                config('mail.from.name', $this->collegeName)
            ),
            replyTo: [
                new \Illuminate\Mail\Mailables\Address(
                    config('mail.from.address'),
                    config('mail.from.name', $this->collegeName)
                )
            ]
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $view = $this->status === 'approved'
            ? 'emails.student-approved'
            : 'emails.student-rejected';

        return new Content(
            view: $view,
            with: [
                'studentName' => $this->studentName,
                'status' => $this->status,
                'rejectionReason' => $this->rejectionReason,
                'collegeName' => $this->collegeName,
                'portalUrl' => config('app.url', 'http://localhost:8000')
            ]
        );
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $view = $this->status === 'approved'
            ? 'emails.student-approved'
            : 'emails.student-rejected';

        return $this->view($view)
                    ->with([
                        'studentName' => $this->studentName,
                        'status' => $this->status,
                        'rejectionReason' => $this->rejectionReason,
                        'collegeName' => $this->collegeName,
                        'portalUrl' => config('app.url', 'http://localhost:8000')
                    ])
                    ->subject($this->envelope()->subject);
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
