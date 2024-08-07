<?php

declare(strict_types=1);

namespace Modules\Ticket\Models;

use Illuminate\Support\Str;
use Modules\Ticket\Enums\GeoTicketStatusEnum;
use Modules\Ticket\Enums\GeoTicketTypeEnum;
use Modules\Ticket\Enums\TicketPriorityEnum;
use Modules\Xot\Services\FileService;

/**
 * @property int                                                                                                        $id
 * @property string                                                                                                     $name
 * @property string                                                                                                     $content
 * @property int                                                                                                        $owner_id
 * @property int|null                                                                                                   $responsible_id
 * @property int                                                                                                        $status_id
 * @property string|null                                                                                                $code
 * @property string|null                                                                                                $ticket_prefix
 * @property int                                                                                                        $order
 * @property int                                                                                                        $priority_id
 * @property int|null                                                                                                   $project_id
 * @property float|null                                                                                                 $estimation
 * @property int|null                                                                                                   $epic_id
 * @property int|null                                                                                                   $sprint_id
 * @property \Illuminate\Support\Carbon|null                                                                            $deleted_at
 * @property \Illuminate\Support\Carbon|null                                                                            $created_at
 * @property \Illuminate\Support\Carbon|null                                                                            $updated_at
 * @property int|null                                                                                                   $type_id
 * @property string|null                                                                                                $latitude
 * @property string|null                                                                                                $longitude
 * @property string|null                                                                                                $updated_by
 * @property string|null                                                                                                $created_by
 * @property string|null                                                                                                $deleted_by
 * @property GeoTicketStatusEnum                                                                                        $status
 * @property TicketPriority|null                                                                                        $priority
 * @property TicketType|null                                                                                            $type
 * @property \Illuminate\Database\Eloquent\Collection<int, \Modules\Ticket\Models\TicketActivity>                       $activities
 * @property int|null                                                                                                   $activities_count
 * @property \Illuminate\Database\Eloquent\Collection<int, \Modules\Ticket\Models\TicketComment>                        $comments
 * @property int|null                                                                                                   $comments_count
 * @property mixed                                                                                                      $completude_percentage
 * @property Epic|null                                                                                                  $epic
 * @property mixed                                                                                                      $estimation_for_humans
 * @property int|null                                                                                                   $estimation_in_seconds
 * @property float                                                                                                      $estimation_progress
 * @property \Illuminate\Database\Eloquent\Collection<int, \Modules\Ticket\Models\TicketHour>                           $hours
 * @property int|null                                                                                                   $hours_count
 * @property \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Modules\Media\Models\Media> $media
 * @property int|null                                                                                                   $media_count
 * @property \Modules\User\Models\User|null                                                                             $owner
 * @property Project|null                                                                                               $project
 * @property \Illuminate\Database\Eloquent\Collection<int, \Modules\Ticket\Models\TicketRelation>                       $relations
 * @property int|null                                                                                                   $relations_count
 * @property \Modules\User\Models\User|null                                                                             $responsible
 * @property Sprint|null                                                                                                $sprint
 * @property Sprint|null                                                                                                $sprints
 * @property \Illuminate\Database\Eloquent\Collection<int, \Spatie\ModelStatus\Status>                                  $statuses
 * @property int|null                                                                                                   $statuses_count
 * @property \Illuminate\Database\Eloquent\Collection<int, \Modules\User\Models\User>                                   $subscribers
 * @property int|null                                                                                                   $subscribers_count
 * @property mixed                                                                                                      $total_logged_hours
 * @property mixed                                                                                                      $total_logged_in_hours
 * @property mixed                                                                                                      $total_logged_seconds
 *
 * @method static \Illuminate\Database\Eloquent\Builder|GeoTicket     currentStatus(...$names)
 * @method static \Modules\Ticket\Database\Factories\GeoTicketFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|GeoTicket     newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GeoTicket     newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GeoTicket     onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|GeoTicket     otherCurrentStatus(...$names)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoTicket     query()
 * @method static \Illuminate\Database\Eloquent\Builder|GeoTicket     whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoTicket     whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoTicket     whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoTicket     whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoTicket     whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoTicket     whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoTicket     whereEpicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoTicket     whereEstimation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoTicket     whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoTicket     whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoTicket     whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoTicket     whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoTicket     whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoTicket     whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoTicket     wherePriorityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoTicket     whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoTicket     whereResponsibleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoTicket     whereSprintId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoTicket     whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoTicket     whereTicketPrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoTicket     whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoTicket     whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoTicket     whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoTicket     withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|GeoTicket     withoutTrashed()
 *
 * @mixin \Eloquent
 */
class GeoTicket extends Ticket
{
    protected $table = 'tickets';

    public function casts(): array
    {
        $data = parent::casts();
        $my = [
            'status' => GeoTicketStatusEnum::class,
            'priority' => TicketPriorityEnum::class,
            'type' => GeoTicketTypeEnum::class,
        ];

        $res = array_merge($data, $my);

        return $res;
    }

    public function getIconData(): array
    {
        $url = $this->type->getIcon();
        $url = Str::of($url)->after('heroicon-o-')->append('.svg')->toString();
        $url = FileService::asset('ui::svg/'.$url);

        return [
            'url' => $url,
            'type' => 'svg',
            'scale' => [35, 35],
        ];
    }

    public static function getLatLngAttributes(): array
    {
        return [
            'lat' => 'latitude',
            'lng' => 'longitude',
        ];
    }
}
