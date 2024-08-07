<?php

declare(strict_types=1);

namespace Modules\Ticket\Listeners;

use Modules\Ticket\Enums\GeoTicketStatusEnum;
use Modules\Ticket\Events\TicketCreatedEvent;

class TicketCreatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(TicketCreatedEvent $event): void
    {
        $status = GeoTicketStatusEnum::PENDING;
        $ticket = $event->ticket;
        $ticket->setStatus($status->value, 'creazione nuovo ticket');
        $ticket->save();
        dddx($ticket);
    }
}
