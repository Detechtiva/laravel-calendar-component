<?php

namespace Detechtiva\VueCalendarForLaravel;

use Carbon\Carbon;
use Detechtiva\VueCalendarForLaravel\Models\Event;
use Detechtiva\VueCalendarForLaravel\Models\EventParticipant;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class EventBuilder
{
    protected Model $model;
    protected Carbon $startsAt;
    protected Carbon $endsAt;
    protected string $title;
    protected ?string $description = null;
    protected $participants = [];
    protected ?int $durationInMinutes = null;
    protected ?array $extras = null;

    public static function new(): EventBuilder
    {
        return new static();
    }

    public function for(Model $model): EventBuilder
    {
        $this->model = $model;
        return $this;
    }

    public function startingAt(Carbon $startsAt): EventBuilder
    {
        $this->startsAt = $startsAt;
        return $this;
    }

    public function endingAt(Carbon $endsAt): EventBuilder
    {
        $this->endsAt = $endsAt;
        return $this;
    }

    public function withTitle(string $title): EventBuilder
    {
        $this->title = $title;
        return $this;
    }

    public function withDescription(?string $description = null): EventBuilder
    {
        $this->description = $description;
        return $this;
    }

    public function withParticipants($participants): EventBuilder
    {
        $this->participants = $participants;
        return $this;
    }

    public function withDurationInMinutes(int $durationInMinutes): EventBuilder
    {
        $this->durationInMinutes = $durationInMinutes;
        return $this;
    }

    public function withExtras(array $extras): EventBuilder
    {
        $this->extras = $extras;
        return $this;
    }

    public function create(): Event
    {
        if (empty($this->title)) {
            throw new InvalidArgumentException("Title cannot be empty", 422);
        }

        $event = new Event([
            'model_type' => get_class($this->model),
            'model_id' => $this->model->id,
            'starts_at' => $this->startsAt,
            'ends_at' => $this->endsAt,
            'title' => $this->title,
            'description' => $this->description,
            'duration_in_minutes' => $this->durationInMinutes,
            'extras' => $this->extras,
            'created_by_type' => auth()->check() ? get_class(auth()->user()) : null,
            'created_by_id' => auth()->check() ? auth()->id() : null,
        ]);

        $event->save();

        foreach ($this->participants as $participant) {
            EventParticipant::create([
                'participant_type' => get_class($participant),
                'participant_id' => $participant->id,
                'event_id' => $event->id,
            ]);
        }

        return $event;
    }

    public function rescheduleEvent(Event $oldEvent) : Event
    {
        $event = new Event([
            'model_type' => $oldEvent->model_type,
            'model_id' => $oldEvent->model_id,
            'starts_at' => $this->startsAt,
            'ends_at' => $this->endsAt,
            'title' => $oldEvent->title,
            'description' => $oldEvent->description,
            'duration_in_minutes' => $this->durationInMinutes,
            'extras' => $this->extras,
            'parent_id' => $oldEvent->id,
            'created_by_type' => auth()->check() ? get_class(auth()->user()) : null,
            'created_by_id' => auth()->check() ? auth()->id() : null,
        ]);

        $event->save();

        foreach ($this->participants as $participant) {
            EventParticipant::create([
                'participant_type' => get_class($participant),
                'participant_id' => $participant->id,
                'event_id' => $event->id,
            ]);
        }

        return $event;
    }
}