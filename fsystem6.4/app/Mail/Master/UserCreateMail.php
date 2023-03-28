<?php

namespace App\Mail\Master;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Cake\Chronos\Chronos;

class UserCreateMail extends Mailable
{
    use Queueable, SerializesModels;

    private $params;
    private $title;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($params)
    {
        $this->params = $params;
        $this->title = 'パスワード通知';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject($this->title)
            ->view('mails.createuser')
            ->with(['params' => $this->params]);
    }
}
