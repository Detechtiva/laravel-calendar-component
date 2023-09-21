<?php

namespace Detechtiva\VueCalendarForLaravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class EventParticipant extends Model
{
    protected $table = 'event_participants';

    protected $guarded = [];

    public function participant(): MorphTo
    {
        return $this->morphTo();
    }
}