<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AthleteAccountMail extends Mailable
{
    use Queueable, SerializesModels;

    public $athleteName;

    public $email;

    public $username;

    public $athleteCode;

    public $profileUrl;

    public $loginUrl;

    public $profileEditUrl;

    public function __construct($athleteName, $email, $username, $athleteCode, $profileUrl, $loginUrl, $profileEditUrl)
    {
        $this->athleteName = $athleteName;
        $this->email = $email;
        $this->username = $username;
        $this->athleteCode = $athleteCode;
        $this->profileUrl = $profileUrl;
        $this->loginUrl = $loginUrl;
        $this->profileEditUrl = $profileEditUrl;
    }

    public function build()
    {
        return $this->subject('Thông tin tài khoản VĐV - Liên Đoàn Cử Tạ - Thể Hình HCM')
            ->view('emails.athlete-account')
            ->with([
                'athleteName' => $this->athleteName,
                'email' => $this->email,
                'username' => $this->username,
                'athleteCode' => $this->athleteCode,
                'profileUrl' => $this->profileUrl,
                'loginUrl' => $this->loginUrl,
                'profileEditUrl' => $this->profileEditUrl,
            ]);
    }
}
