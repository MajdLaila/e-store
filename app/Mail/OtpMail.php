<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otpCode;
    public string $purpose;

    /**
     * Create a new message instance.
     */
    public function __construct(string $otpCode, string $purpose = 'verify')
    {
        $this->otpCode = $otpCode;
        $this->purpose = $purpose;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->purpose) {
            'register' => 'Email Verification Code - Complete Your Registration',
            'login' => 'Login Verification Code',
            'reset_password' => 'Password Reset Verification Code',
            'verify_email' => 'Email Verification Code',
            default => 'Verification Code'
        };

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.otp',
            with: [
                'otpCode' => $this->otpCode,
                'purpose' => $this->purpose,
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
