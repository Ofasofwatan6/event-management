<?php

namespace App\Observers;

use App\Models\Event;
use App\Models\EventHistory;
use Illuminate\Support\Arr;

class EventHistoryObserver
{
    public function updated(Event $event): void
    {
        $tracked = [
            'title', 'description', 'image', 'location', 'quota',
            'start_date', 'end_date', 'type', 'price',
            'event_category_id', 'event_type_id',
        ];

        if ($event->wasChanged($tracked)) {
            $lastVersion = EventHistory::where('event_id', $event->id)->max('version') ?? 0;

            EventHistory::where('event_id', $event->id)
                ->whereNull('valid_to')
                ->update(['valid_to' => now()]);

            EventHistory::create([
                'event_id' => $event->id,
                'version' => $lastVersion + 1,
                'snapshot' => Arr::only($event->getOriginal(), $tracked),
                'valid_from' => now(),
            ]);
        }
    }
}
