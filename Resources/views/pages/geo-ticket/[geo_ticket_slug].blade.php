<?php

use Modules\Ticket\Models\GeoTicket;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use function Laravel\Folio\{withTrashed,middleware, name,render};

withTrashed();
name('geo_ticket_slug.show');
//middleware(['auth', 'verified']);

render(function (View $view, string $geo_ticket_slug) {
    $ticket = GeoTicket::firstWhere(['slug' => $geo_ticket_slug]);
    return $view->with('ticket', $ticket);
});


?>
<x-layouts.marketing>
    
    @component("ui::components.blocks.title.v2",['text' => $ticket->name, 'level' => 'h6']) @endcomponent


    @php
        $data = [
            'title' => 'my title',
            'subtitle' => null
        ];
    @endphp 

    @component("ui::components.blocks.images_gallery.v1", ['data' => $data]) @endcomponent


</x-layouts.marketing>