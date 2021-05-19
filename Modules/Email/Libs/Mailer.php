<?php

namespace Modules\Email\Libs;

use Exception;
use Modules\Email\EmailHandler;
use Modules\Email\Libs\Mail\Template;
use Illuminate\Mail\Mailer as BaseMailer;

/**
 * Class Mailer
 * @package Modules\Email\Libs
 */
final class Mailer
{

    /**
     * @var BaseMailer
     */
    private $mailer;

    private $emailDataResolver;

    /**
     * The mail view path
     *
     * @var $view
     */
    private $view;

    /**
     * The mail subject
     *
     * @var $subject
     */
    private $subject;

    /**
     * The mail attachments
     *
     * @var $attachments
     */
    private $attachments;

    /**
     * The attachment Name
     *
     * @var $attachmentName
     */
    private $attachmentName;

    /**
     * Mailer constructor.
     * @param BaseMailer $mailer
     * @param EmailHandler $emailHandler
     */
    public function __construct(
        BaseMailer $mailer,
        EmailHandler $emailHandler
    )
    {
        $this->mailer            = $mailer;
        $this->emailDataResolver = $emailHandler;
    }

    /**
     * @param string $view
     * @return $this
     */
    public function setView( string $view )
    {
        $this->view = $view;
        return $this;
    }

    /**
     * The subject for the mail
     *
     * @param string $subject
     * @return $this
     */
    public function setSubject( string $subject )
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * The attachments for the mail
     *
     * @param string $attachments
     * @return $this
     */
    public function setAttachments( string $attachments )
    {
        $this->attachments = $attachments;
        return $this;
    }

    /**
     * The attachment name for the mail
     *
     * @param string $attachmentName
     * @return $this
     */
    public function setAttachmentType( string $attachmentName )
    {
        $this->attachmentName = $attachmentName;
        return $this;
    }

    /**
     * @param string $template_slug
     * @param array $params
     * @param array $extras
     * @param array $options
     * @throws Exception
     */
    public function send( string $template_slug, array $params = [], array $extras = [], $options = [])
    {
        if ( !$this->view ) {
            throw new Exception($this->view . " doesn\'t exists !");
        }

        if ( !$this->subject ) {
            throw new Exception("Subject is required !");
        }

        if ( $this->attachments && !$this->attachmentName ) {
            throw new Exception("Attachment Name is required !");
        }

        $resolvedContent = $this->emailDataResolver->handle( $template_slug, $params, $extras, $options );
        $receiver        = $resolvedContent['receiver'] ?? '';

        $this->mailer
            ->to($receiver)
            ->send(new Template([
                'subject'         => $this->subject,
                'attachment'      => $this->attachments,
                'attachment_name' => $this->attachmentName,
                'email_view_path' => $this->view,
                'content'         => $resolvedContent['content'] ?? '',
                'sender'          => $resolvedContent['sender'] ?? '',
                'receiver'        => $resolvedContent['receiver'] ?? '',
                'url'             => $resolvedContent['url'] ?? '',
                'name'            => $resolvedContent['user_initials'] ?? '',
                'data'            => $options['data'] ?? null,
            ]));
    }

}
