<?php

namespace Detechtiva\VueCalendarForLaravel;

use Illuminate\Database\Eloquent\Collection;

class EventWeekGrid
{
    public Collection $events;

    public static function new(): static
    {
        return new static();
    }

    public function withEvents(Collection $events): self
    {
        $this->events = $events;
        return $this;
    }

    public function getColumnsLayoutForWeek()
    {

    }

    public function get()
    {

    }
}