<?php

namespace Foundation\Listeners;

use Foundation\Lib\TicketEvent;
use Foundation\Lib\TicketEvent as TicketEventLib;

use Modules\Email\Libs\Mailer;

class TicketActivity
{
    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * Create the event listener.
     *
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Handle the event.
     *
     * @param $event
     * @return void
     */
    public function handle($event)
    {
        $ticketEvent = $event->ticketEvent;
        $content = '';

        if($ticketEvent->type == TicketEvent::TYPE_COMMENT)
            $content = ' commented: ' . $ticketEvent->content;
        elseif ($ticketEvent->type == TicketEvent::TYPE_ASSIGN)
            $content = 'assigned the ticket to <a href="' . route('admin.user.show', $ticketEvent->assigned_to) . '">' . $ticketEvent->assignee->getFullName() . '</a>';
        elseif ($ticketEvent->type == TicketEvent::TYPE_REVOKE)
            $content = ' revoked ticket assignment';
        elseif ($ticketEvent->type == TicketEvent::TYPE_RESOLVED)
            $content = ' marked the ticket as resolved';
        elseif ($ticketEvent->type == TicketEvent::TYPE_CLOSE)
            $content = ' closed the ticket';

        $fields = [
            '{TICKET_TITLE}' => $ticketEvent->ticket->title,
            '{AUTHOR}' => '<a href="' . route('admin.user.show', $ticketEvent->author_id) . '">' . $ticketEvent->author->getFullName() . '</a>',
            '{TICKET_EVENT_CONTENT}' =>  $content,
            '{TICKET_LINK}' => route('admin.ticket.show', $ticketEvent->ticket->id),
            '{CREATED_AT}' => $ticketEvent->created_at->format('M d, Y H:i A')
        ];

        try {
            $this->mailer
                ->setSubject('New Ticket Activity | Ticket ' . ucfirst(TicketEventLib::$type[$ticketEvent->type]))
                ->setView('email.template')
                ->send('ticket_activity_template', $fields, [
                    'receiver' => config('mail.reply_to.address'),
                    'sender'   => config('mail.from.address'),
                ]);
        } catch (\Exception $exception) {
            \Log::debug($exception->getMessage(), [
                'subject'  => 'New Ticket Activity | Ticket ' . ucfirst(TicketEventLib::$type[$ticketEvent->type]),
                'receiver' => config('mail.reply_to.address'),
                'sender'   => config('mail.from.address'),
            ]);
        }
    }
}
