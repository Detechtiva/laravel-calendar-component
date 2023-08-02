<?php

namespace Detechtiva\VueCalendarForLaravel;

use Detechtiva\VueCalendarForLaravel\Models\Event;

interface HasEvents
{
    public function createEvent(array $attributes): Event;
    public function updateEvent(int $id, array $attributes): void;
    public function deleteEvent(int $id): void;
}