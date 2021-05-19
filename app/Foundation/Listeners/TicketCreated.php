<?php

namespace Foundation\Listeners;

use Foundation\Lib\Ticket as TicketLib;

use Modules\Email\Libs\Mailer;

class TicketCreated
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
        $fields = [
            '{TICKET_TITLE}' => $event->ticket->title,
            '{TICKET_CATEGORY}' =>  TicketLib::$category[$event->ticket->category],
            '{TICKET_PRIORITY}' =>  TicketLib::$priority[$event->ticket->priority],
            '{AUTHOR}' => '<a href="' . route('admin.user.show', $event->ticket->created_by) . '">' . $event->ticket->creator->getFullName() . '</a>',
            '{TICKET_LINK}' => route('admin.ticket.show', $event->ticket->id),
            '{CREATED_AT}' => $event->ticket->created_at->format('M d, Y H:i A')
        ];


        try {
            $this->mailer
                ->setSubject('New Ticket Created | ' . TicketLib::$priority[$event->ticket->priority])
                ->setView('email.template')
                ->send('ticket_creation_template', $fields, [
                    'receiver' => config('mail.reply_to.address'),
                    'sender'   => config('mail.from.address'),
                ]);
        } catch (\Exception $exception) {
            \Log::debug($exception->getMessage(), [
                'subject'  => 'New Ticket Created | ' . TicketLib::$priority[$event->ticket->priority],
                'receiver' => config('mail.reply_to.address'),
                'sender'   => config('mail.from.address'),
            ]);
        }
    }
}
