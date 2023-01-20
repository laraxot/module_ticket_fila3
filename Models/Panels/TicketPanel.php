<?php

declare(strict_types=1);

namespace Modules\Ticket\Models\Panels;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Cms\Models\Panels\XotBasePanel;
// --- Services --

use Modules\Ticket\Models\Ticket;
use Modules\Xot\Contracts\RowsContract;

class TicketPanel extends XotBasePanel {
    /**
     * The model the resource corresponds to.
     */
    public static string $model = 'Ticket';

    /**
     * The single value that should be used to represent the resource when being displayed.
     */
    public static string $title = 'title';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
    ];

    /**
     * The relationships that should be eager loaded on index queries.
     */
    public function with(): array {
        return [];
    }

    public function search(): array {
        return [];
    }

    /**
     * on select the option id.
     *
     * quando aggiungi un campo select, è il numero della chiave
     * che viene messo come valore su value="id"
     *
     * @param Ticket $row
     *
     * @return int|string|mixed|null
     */
    public function optionId($row) {
        return $row->getKey();
    }

    /**
     * on select the option label.
     *
     * @param Ticket $row
     */
    public function optionLabel($row): string {
        return (string) $row->title;
    }

    /**
     * index navigation.
     */
    public function indexNav(): ?\Illuminate\Contracts\Support\Renderable {
        return null;
    }

    /**
     * Build an "index" query for the given resource.
     *
     * @param RowsContract $query
     *
     * @return RowsContract
     */
    public static function indexQuery(array $data, $query) {
        return $query->where('user_id', Auth::id());
    }

    /**
     * Get the fields displayed by the resource.
        'value'=>'..',
     */
    public function fields(): array {
        return [
            (object) [
                'type' => 'Text',
                'name' => 'url',
                'comment' => 'Title',
            ],
        ];
    }

    /**
     * Get the tabs available.
     */
    public function tabs(): array {
        $tabs_name = ['ticket_threads'];

        return $tabs_name;
    }

    /**
     * Get the cards available for the request.
     */
    public function cards(Request $request): array {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function filters(Request $request = null): array {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     */
    public function lenses(Request $request): array {
        return [];
    }

    /**
     * Get the actions available for the resource.
     */
    public function actions(): array {
        return [
            new Actions\CreateAction(),
        ];
    }
}
