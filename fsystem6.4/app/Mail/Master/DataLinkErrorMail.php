<?php

namespace App\Mail\Master;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Cake\Chronos\Chronos;

class DataLinkErrorMail extends Mailable
{
    use Queueable, SerializesModels;

    private $title;
    private $text;
    private $validation_error_messages;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($program_name, $error_text = '')
    {
        $this->title   = sprintf('%s %s エラー', Chronos::now()->format('Y/m/d H:i:s'), $program_name);
        $this->text    = (($error_text !== '')?$error_text : config('constant.data_link.global.error_message_system'));
        $this->validation_error_messages = [];
    }
    /**
     * @param array $validation_error_messages
     */
    public function setValidationMessage($validation_error_messages)
    {
        $this->validation_error_messages = $validation_error_messages;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->text('mails.error_mail')
                    ->subject($this->title)
                    ->with([
                        'text' => $this->text,
                        'validation_error_messages' => $this->validation_error_messages
                    ]);
    }
}
