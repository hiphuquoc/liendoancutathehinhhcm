<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TrainerAccountMail extends Mailable
{
    use Queueable, SerializesModels;

    public $trainerName;
    public $email;
    public $username;
    public $trainerCode;
    public $profileUrl;
    public $loginUrl;
    public $profileEditUrl;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($trainerName, $email, $username, $trainerCode, $profileUrl, $loginUrl, $profileEditUrl)
    {
        $this->trainerName = $trainerName;
        $this->email = $email;
        $this->username = $username;
        $this->trainerCode = $trainerCode;
        $this->profileUrl = $profileUrl;
        $this->loginUrl = $loginUrl;
        $this->profileEditUrl = $profileEditUrl;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Thông tin tài khoản HLV - Liên Đoàn Cử Tạ - Thể Hình HCM')
                    ->view('emails.trainer-account')
                    ->with([
                        'trainerName' => $this->trainerName,
                        'email' => $this->email,
                        'username' => $this->username,
                        'trainerCode' => $this->trainerCode,
                        'profileUrl' => $this->profileUrl,
                        'loginUrl' => $this->loginUrl,
                        'profileEditUrl' => $this->profileEditUrl,
                    ]);
    }
}

