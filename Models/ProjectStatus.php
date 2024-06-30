<?php

namespace Modules\Ticket\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectStatus extends BaseModel
{
    

    protected $fillable = [
        'name', 'color', 'is_default'
    ];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'status_id', 'id')->withTrashed();
    }
}
