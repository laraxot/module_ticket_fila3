<?php

namespace Modules\Ticket\Scopes;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;

class AgentScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $user = Auth::user();
        if(auth()->check() && request()->is('admin/*') && $user->hasRole(2) /*&& $user->roles->contains(2)*/)
        {
            $builder->where('assigned_to_user_id', $user->id);
        }
    }
}