<?php

declare(strict_types=1);

namespace Modules\Ticket\Models\Policies;

use Modules\Ticket\Models\TicketPriority;
use Modules\User\Models\Policies\UserBasePolicy;
use Modules\Xot\Contracts\UserContract;

class TicketPriorityPolicy extends UserBasePolicy
{
    /**
     * Determine whether the user can view any models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(UserContract $user)
    {
        return true;
        // return $user->can('List ticket priorities');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(UserContract $user, TicketPriority $ticketPriority)
    {
        return $user->can('View ticket priority');
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(UserContract $user)
    {
        return $user->can('Create ticket priority');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(UserContract $user, TicketPriority $ticketPriority)
    {
        return $user->can('Update ticket priority');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(UserContract $user, TicketPriority $ticketPriority)
    {
        return $user->can('Delete ticket priority');
    }
}
