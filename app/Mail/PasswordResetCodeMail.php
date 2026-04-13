<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class PasswordResetCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $code,
        public int $ttlMinutes,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'MyFinance — código para redefinir sua senha',
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'mail.password-reset-code-html',
        );
    }
}
