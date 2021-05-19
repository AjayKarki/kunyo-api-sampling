<?php

namespace App\Http\Controllers\Admin\Actions;

use Foundation\Events\TicketActivity;
use Foundation\Lib\Ticket as TicketLib;
use Foundation\Lib\TicketEvent;
use Foundation\Models\Ticket;
use Foundation\Services\TicketEventService;
use Foundation\Services\TicketService;
use Neputer\Config\Status;
use Illuminate\Http\Request;
use Neputer\Supports\BaseController;
use Illuminate\Support\Facades\Schema;

/**
 * Class TicketAction
 * @package App\Http\Controllers\Admin\Actions
 */
class TicketAction extends BaseController
{
    /**
     * @var TicketService
     */
    private $ticketService;
    /**
     * @var TicketEventService
     */
    private $eventService;

    public function __construct(TicketService $ticketService, TicketEventService $eventService)
    {
        $this->ticketService = $ticketService;
        $this->eventService = $eventService;
    }

    /**
     * Pick or Assign a Ticket to user
     *
     * @param Ticket $ticket
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assign(Ticket $ticket, Request $request)
    {
        if($ticket->isOpen()){
            if($ticket->isAssigned()){
                flash('error', 'Ticket Already Assigned');
            } else{
                $this->ticketService->update(['assigned_to' => $request->get('assignee_id') ?? auth()->user()->id], $ticket);
                $event = $this->eventService->new([
                    'type' => TicketEvent::TYPE_ASSIGN,
                    'author_id' => auth()->user()->id,
                    'ticket_id' => $ticket->id,
                    'assigned_to' => $request->get('assignee_id') ?? auth()->user()->id
                ]);
                flash('success', 'Ticket Picked/Assigned');
                event(new TicketActivity($event->load('ticket', 'author', 'assignee')));
            }
        }
        else{
            flash('error', 'Cannot Perform Action on Closed Ticket!');
        }
        return redirect()->back();
    }

    /**
     * Revoke a assigned ticket
     *
     * @param Ticket $ticket
     * @return \Illuminate\Http\RedirectResponse
     */
    public function revoke(Ticket $ticket)
    {
        if($ticket->isOpen()) {
            if ($ticket->isAssigned()) {
                $this->ticketService->update(['assigned_to' => null, 'status' => TicketLib::STATUS_OPEN], $ticket);
                $event = $this->eventService->new(['type' => TicketEvent::TYPE_REVOKE, 'author_id' => auth()->user()->id, 'ticket_id' => $ticket->id]);
                flash('success', 'Ticket Assignment Revoked');
                event(new TicketActivity($event->load('ticket', 'author', 'assignee')));
            } else {
                flash('error', 'Cannot Revoke! Ticket Not Assigned');
            }
        }
        else {
            flash('error', 'Cannot Perform Action on Closed Ticket!');
        }
        return redirect()->back();
    }

    /**
     * Mark a ticket as resolved
     *
     * @param Ticket $ticket
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resolve(Ticket $ticket)
    {
        if($ticket->isOpen()){
            if(!$ticket->isResolved()){
                $this->ticketService->update(['status' => TicketLib::STATUS_RESOLVED], $ticket);
                $event = $this->eventService->new(['type' => TicketEvent::TYPE_RESOLVED, 'author_id' => auth()->user()->id, 'ticket_id' => $ticket->id]);
                flash('success', 'Ticket Marked as Resolved');
                event(new TicketActivity($event->load('ticket', 'author', 'assignee')));
            }else {
                flash('error', 'Ticket is already Resolved');
            }
        } else{
            flash('error', 'Cannot Preform Action. Ticket is Closed.');
        }
        return redirect()->back();
    }

    /**
     * Close a ticket
     *
     * @param Ticket $ticket
     * @return \Illuminate\Http\RedirectResponse
     */
    public function close(Ticket $ticket)
    {
        if($ticket->isOpen()){
            $this->ticketService->update(['status' => TicketLib::STATUS_CLOSED], $ticket);
            $event = $this->eventService->new(['type' => TicketEvent::TYPE_CLOSE, 'author_id' => auth()->user()->id, 'ticket_id' => $ticket->id]);
            flash('success', 'Ticked Closed');
            event(new TicketActivity($event->load('ticket', 'author', 'assignee')));
        }else {
            flash('error', 'Ticked is already Closed');
        }
        return redirect()->back();
    }

}
