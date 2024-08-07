<?php

declare(strict_types=1);

namespace Modules\Ticket\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Ticket\Models\Sprint;
use Modules\Xot\Contracts\UserContract;

class SprintPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(UserContract $user)
    {
        return $user->can('List sprints');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(UserContract $user, Sprint $sprint)
    {
        return true;
        /*
        return $user->can('View sprint')
            && (
                $sprint->project->owner_id === $user->id
                || $sprint->project->users()->where('users.id', $user->id)->count()
            );
        */
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(UserContract $user)
    {
        return $user->can('Create sprint');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(UserContract $user, Sprint $sprint)
    {
        return true;
        /*
        return $user->can('Update sprint')
            && (
                $sprint->project->owner_id === $user->id
                || $sprint->project->users()->where('users.id', $user->id)
                    ->where('role', config('system.projects.affectations.roles.can_manage'))
                    ->count()
            );
        */
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(UserContract $user, Sprint $sprint)
    {
        return $user->can('Delete sprint');
    }
}
