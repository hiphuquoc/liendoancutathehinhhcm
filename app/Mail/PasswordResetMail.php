<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $email;
    public $language;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token, $email, $language = 'vi')
    {
        $this->token = $token;
        $this->email = $email;
        $this->language = $language;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $resetUrl = url('/reset-password?token=' . $this->token . '&email=' . urlencode($this->email));
        
        return $this->subject('Đặt lại mật khẩu')
                    ->view('emails.password-reset')
                    ->with([
                        'token' => $this->token,
                        'email' => $this->email,
                        'resetUrl' => $resetUrl,
                        'language' => $this->language,
                    ]);
    }
}


