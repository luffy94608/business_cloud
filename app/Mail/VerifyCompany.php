<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerifyCompany extends Mailable
{
    use Queueable, SerializesModels;

    protected $param;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($p)
    {
        $this->param = $p;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('哈罗同行－企业用户邮箱验证')->from('noreply@hollo.cn')->view('verify_company', $this->param);
    }
}
