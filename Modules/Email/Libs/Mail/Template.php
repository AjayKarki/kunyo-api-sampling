<?php

namespace Modules\Email\Libs\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\HtmlString;

/**
 * Class Template
 * @package Modules\Email\Libs\Mail
 */
final class Template extends Mailable
{

    use Queueable, SerializesModels;

    private $data;

    /**
     * Create a new message instance.
     *
     * @param $data
     */
    public function __construct( $data )
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->replyTo(config('mail.reply_to.address'), config('mail.reply_to.name'))
            ->markdown($this->data['email_view_path'])
            ->subject($this->data['subject'])
            ->with([
                'name'    => $this->data['receiver'],
                'content' => new HtmlString($this->data['content']),
                'url'     => $this->data['url'],
                'data'    => $this->data['data'],
            ]);
    }

}
