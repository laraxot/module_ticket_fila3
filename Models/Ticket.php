<?php

declare(strict_types=1);

namespace Modules\Ticket\Models;

use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Modules\Media\Models\Media;
use Modules\Ticket\Database\Factories\TicketFactory;
use Modules\Ticket\Notifications\TicketCreated;
use Modules\Ticket\Notifications\TicketStatusUpdated;
use Modules\User\Models\User;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Webmozart\Assert\Assert;

/**
 * Modules\Ticket\Models\Ticket.
 *
 * @property Collection<int, TicketActivity> $activities
 * @property int|null                        $activities_count
 * @property Collection<int, TicketComment>  $comments
 * @property int|null                        $comments_count
 * @property Epic|null                       $epic
 * @property Collection<int, TicketHour>     $hours
 * @property int|null                        $hours_count
 * @property MediaCollection<int, Media>     $media
 * @property int|null                        $media_count
 * @property User|null                       $owner
 * @property TicketPriority|null             $priority
 * @property Project|null                    $project
 * @property Collection<int, TicketRelation> $relations
 * @property int|null                        $relations_count
 * @property User|null                       $responsible
 * @property Sprint|null                     $sprint
 * @property Sprint|null                     $sprints
 * @property TicketStatus|null               $status
 * @property Collection<int, User>           $subscribers
 * @property Collection<int, User>           $watchers
 * @property int|null                        $subscribers_count
 * @property TicketType|null                 $type
 *
 * @method static TicketFactory  factory($count = null, $state = [])
 * @method static Builder|Ticket newModelQuery()
 * @method static Builder|Ticket newQuery()
 * @method static Builder|Ticket onlyTrashed()
 * @method static Builder|Ticket query()
 * @method static Builder|Ticket withTrashed()
 * @method static Builder|Ticket withoutTrashed()
 *
 * @property int         $id
 * @property string      $name
 * @property string      $content
 * @property int         $owner_id
 * @property int|null    $responsible_id
 * @property int         $status_id
 * @property string|null $code
 * @property string|null $ticket_prefix
 * @property int         $order
 * @property int         $priority_id
 * @property int|null    $project_id
 * @property float|null  $estimation
 * @property int|null    $epic_id
 * @property int|null    $estimationInSeconds
 * @property int|null    $estimationProgress
 * @property Sprint|null $currentSprint
 * @property int|null    $sprint_id
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $updated_by
 * @property string|null $created_by
 * @property string|null $deleted_by
 * @property int|null    $totalLoggedHours
 * @property int|null    $totalLoggedSeconds
 *
 * @method static Builder|Ticket whereCode($value)
 * @method static Builder|Ticket whereContent($value)
 * @method static Builder|Ticket whereCreatedAt($value)
 * @method static Builder|Ticket whereCreatedBy($value)
 * @method static Builder|Ticket whereDeletedAt($value)
 * @method static Builder|Ticket whereDeletedBy($value)
 * @method static Builder|Ticket whereEpicId($value)
 * @method static Builder|Ticket whereEstimation($value)
 * @method static Builder|Ticket whereId($value)
 * @method static Builder|Ticket whereName($value)
 * @method static Builder|Ticket whereOrder($value)
 * @method static Builder|Ticket whereOwnerId($value)
 * @method static Builder|Ticket wherePriorityId($value)
 * @method static Builder|Ticket whereProjectId($value)
 * @method static Builder|Ticket whereResponsibleId($value)
 * @method static Builder|Ticket whereSprintId($value)
 * @method static Builder|Ticket whereStatusId($value)
 * @method static Builder|Ticket whereTicketPrefix($value)
 * @method static Builder|Ticket whereUpdatedAt($value)
 * @method static Builder|Ticket whereUpdatedBy($value)
 *
 * @property mixed      $completude_percentage
 * @property mixed      $estimation_for_humans
 * @property float|null $estimation_in_seconds
 * @property int|float  $estimation_progress
 * @property mixed      $total_logged_hours
 * @property mixed      $total_logged_in_hours
 * @property int|float  $total_logged_seconds
 *
 * @mixin \Eloquent
 */
class Ticket extends BaseModel implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'name', 'content', 'owner_id', 'responsible_id',
        'status_id', 'project_id', 'code', 'order', 'type_id',
        'priority_id', 'estimation', 'epic_id', 'sprint_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(static function (Ticket $item): void {
            $project = Project::where('id', $item->project_id)->first();
            Assert::notNull($project);
            $count = Ticket::where('project_id', $project->id)->count();
            $order = $project->tickets->last()->order ?? -1;
            $item->code = $project->ticket_prefix.'-'.($count + 1);
            $item->order = $order + 1;
        });

        static::created(static function (Ticket $item): void {
            Assert::notNull($item->sprint);
            if ($item->sprint_id && $item->sprint->epic_id) {
                Ticket::where('id', $item->id)->update(['epic_id' => $item->sprint->epic_id]);
            }

            foreach ($item->watchers as $user) {
                $user->notify(new TicketCreated($item));
            }
        });

        static::updating(static function (Ticket $item): void {
            $old = Ticket::where('id', $item->id)->first();
            Assert::notNull($old);
            // Ticket activity based on status
            $oldStatus = $old->status_id;
            if ($oldStatus !== $item->status_id) {
                Assert::notNull(auth()->user());
                TicketActivity::create([
                    'ticket_id' => $item->id,
                    'old_status_id' => $oldStatus,
                    'new_status_id' => $item->status_id,
                    'user_id' => auth()->id(),
                ]);
                foreach ($item->watchers as $user) {
                    $user->notify(new TicketStatusUpdated($item));
                }
            }

            // Ticket sprint update
            $oldSprint = $old->sprint_id;
            Assert::notNull($item->sprint);
            if ($oldSprint && ! $item->sprint_id) {
                Ticket::where('id', $item->id)->update(['epic_id' => null]);
            } elseif ($item->sprint_id && $item->sprint->epic_id) {
                Ticket::where('id', $item->id)->update(['epic_id' => $item->sprint->epic_id]);
            }
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_id', 'id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(TicketStatus::class, 'status_id', 'id')->withTrashed();
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id')->withTrashed();
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(TicketType::class, 'type_id', 'id')->withTrashed();
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(TicketPriority::class, 'priority_id', 'id')->withTrashed();
    }

    public function activities(): HasMany
    {
        return $this->hasMany(TicketActivity::class, 'ticket_id', 'id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class, 'ticket_id', 'id');
    }

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'ticket_subscribers', 'ticket_id', 'user_id');
    }

    public function relations(): HasMany
    {
        return $this->hasMany(TicketRelation::class, 'ticket_id', 'id');
    }

    public function hours(): HasMany
    {
        return $this->hasMany(TicketHour::class, 'ticket_id', 'id');
    }

    public function epic(): BelongsTo
    {
        return $this->belongsTo(Epic::class, 'epic_id', 'id');
    }

    public function sprint(): BelongsTo
    {
        return $this->belongsTo(Sprint::class, 'sprint_id', 'id');
    }

    public function sprints(): BelongsTo
    {
        return $this->belongsTo(Sprint::class, 'sprint_id', 'id');
    }

    public function watchers(): Attribute
    {
        return new Attribute(
            get: function () {
                Assert::notNull($this->project);
                $users = $this->project->users;
                Assert::notNull($this->owner);
                $users->push($this->owner);
                if ($this->responsible instanceof User) {
                    $users->push($this->responsible);
                }

                return $users->unique('id');
            }
        );
    }

    public function totalLoggedHours(): Attribute
    {
        return new Attribute(
            get: function () {
                $seconds = $this->hours->sum('value') * 3600;

                return CarbonInterval::seconds($seconds)->cascade()->forHumans();
            }
        );
    }

    public function totalLoggedSeconds(): Attribute
    {
        return new Attribute(
            get: fn (): int|float => $this->hours->sum('value') * 3600
        );
    }

    public function totalLoggedInHours(): Attribute
    {
        return new Attribute(
            get: fn () => $this->hours->sum('value')
        );
    }

    public function estimationForHumans(): Attribute
    {
        return new Attribute(
            get: fn () => CarbonInterval::seconds($this->estimationInSeconds)->cascade()->forHumans()
        );
    }

    public function estimationInSeconds(): Attribute
    {
        return new Attribute(
            get: function (): ?float {
                if (! $this->estimation) {
                    return null;
                }

                return $this->estimation * 3600;
            }
        );
    }

    public function estimationProgress(): Attribute
    {
        return new Attribute(
            get: fn (): int|float =>
                // return (($this->totalLoggedSeconds ?? 0) / ($this->estimationInSeconds ?? 1)) * 100;
                (($this->totalLoggedSeconds ?? 1) / ($this->estimationInSeconds ?? 1)) * 100
        );
    }

    public function completudePercentage(): Attribute
    {
        return new Attribute(
            get: fn () => $this->estimationProgress
        );
    }
}
