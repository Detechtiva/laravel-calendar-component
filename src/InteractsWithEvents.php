<?php

namespace Detechtiva\VueCalendarForLaravel;

use Detechtiva\VueCalendarForLaravel\Models\Event;

trait InteractsWithEvents
{
    public function events()
    {
        return $this->morphMany(Event::class, 'model');
    }

    public function rescheduleEvent()
    {
        
    }
    
//    public function createEvent(array $attributes): Event
//    {
//        return $this->events()->create($attributes);
//    }
//
//    public function updateEvent(int $id, array $attributes): void
//    {
//        $event = $this->events()->find($id);
//
//        $event->update($attributes);
//    }
//
//    public function deleteEvent(int $id): void
//    {
//        $event = $this->events()->find($id);
//
//        $event->delete();
//    }
}