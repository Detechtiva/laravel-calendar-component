<?php

namespace Detechtiva\VueCalendarForLaravel;

use Carbon\Carbon;
use Detechtiva\VueCalendarForLaravel\Models\Event;
use Illuminate\Support\Collection;

class EventManagerService
{
    protected $model = null;
    protected ?Carbon $fromDate = null;
    protected ?Carbon $toDate = null;
    protected $events;
    protected bool $startOnSunday = false;


    public static function new(): self
    {
        return new static();
    }

    public function forModel($model): self
    {
        $this->model = $model;
        return $this;
    }

    public function inMonth(int $year, int $month): self
    {
        $this->fromDate = Carbon::create($year, $month, 1);
        $this->toDate = $this->fromDate->copy()->endOfMonth();
        return $this;
    }

    public function currentMonth(): self
    {
        $today = Carbon::now();
        return $this->inMonth($today->year, $today->month);
    }

    public function forDay(Carbon $date): self
    {
        $this->fromDate = $date->startOfDay();
        $this->toDate = $date->endOfDay();
        return $this;
    }

    public function startOnSunday(): self
    {
        $this->startOnSunday = true;
        return $this;
    }

    public function between(Carbon $from, Carbon $to): self
    {
        if ($from->isSameDay($to)) {
            return $this->forDay($from);
        }

        $this->fromDate = $from;
        $this->toDate = $to;
        return $this;
    }

    public function getEventsByWeek(): Collection
    {
        if (!$this->fromDate || !$this->toDate) {
            throw new \Exception("Define a valid date range first.");
        }

        if ($this->startOnSunday) {
            $this->fromDate->subDay();
            $this->toDate->subDay();
        }

        $weekEvents = collect();

        for ($date = $this->fromDate; $date->lte($this->toDate); $date->addDay()) {
            $dayEvents = Event::query()
                ->when($this->model, function ($query) {
                    $query->where('model_type', $this->model);
                })
                ->whereDate('starts_at', $date)
                ->get();

            $weekEvents->push([
                'date' => $date->toDateString(),
                'dayOfWeek' => "{$date->format('l')} {$date->format('d')}",
                'events' => $dayEvents->map(function (Event $event) use ($date) {
                    return [
                        'id' => $event->id,
                        'model_type' => $event->model_type,
                        'model_id' => $event->model_id,
                        'title' => $event->title,
                        'description' => $event->description,
                        'starts_at' => $event->starts_at,
                        'ends_at' => $event->ends_at,
                        'properties' => [
                            'conflicting_events' => $event->getConflictingEvents(),
                            'style' => [
                                'grid-column-start' => $this->getColumnForEvent($event->starts_at),
                                'grid-row-start' => $this->getRowsForEvent($event->starts_at, $event->ends_at)['startRow'],
                                'grid-row-end' => 'span ' . $this->getRowsForEvent($event->starts_at, $event->ends_at)['span'],
                                'width' => $this->getOverlappingEvents($event)->count() <= 1
                                    ? '100%'
                                    : 100 / $this->getOverlappingEvents($event)->count() . '%',
                                'left' => $this->getLeftPositionForEvent($event),
                            ]
                        ]
                    ];
                }),
            ]);
        }

        return $weekEvents;
    }


    public function get(): Collection
    {
        $query = Event::query();

        if ($this->model) {
            $query->where('model_type', $this->model);
        }

        if ($this->fromDate && $this->toDate) {
            $query->whereDate('starts_at', '>=', $this->fromDate)
                ->whereDate('ends_at', '<=', $this->toDate);
        }

        return $query
            ->get()
            ->map(function (Event $event) {
                return [
                    'id' => $event->id,
                    'model_type' => $event->model_type,
                    'model_id' => $event->model_id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'starts_at' => $event->starts_at,
                    'ends_at' => $event->ends_at,
                    'properties' => [
                        'conflicting_events' => $event->getConflictingEvents(),
                        'style' => [
                            'grid-column-start' => $this->getColumnForEvent($event->starts_at),
                            'grid-row-start' => $this->getRowsForEvent($event->starts_at, $event->ends_at)['startRow'],
                            'grid-row-end' => 'span ' . $this->getRowsForEvent($event->starts_at, $event->ends_at)['span'],
                            'width' => $this->calculateWidthForEvent($event),
                            'left' => $this->getLeftPositionForEvent($event),
                        ]
                    ]
                ];
            });
    }

    private function getColumnForEvent(Carbon $eventDate): int
    {
        $eventDay = $eventDate->dayOfWeek;
        $adjustedIndex = ($eventDay + 6) % 7;
        if ($this->startOnSunday) $adjustedIndex++;
        return $adjustedIndex + 3;
    }

    private function getRowsForEvent(Carbon $starts_at, Carbon $ends_at): array
    {
        $hourly_slots = 4;
        $props_starts_at = 9;

        $startHour = $starts_at->hour;
        $startMinute = $starts_at->minute;
        $startRow =
            ($startHour - $props_starts_at) * $hourly_slots +
            floor(($startMinute * $hourly_slots) / 60) +
            2;

        $endHour = $ends_at->hour;
        $endMinute = $ends_at->minute;
        $endRow =
            ($endHour - $props_starts_at) * $hourly_slots +
            floor(($endMinute * $hourly_slots) / 60) +
            2;

        return ['startRow' => $startRow, 'span' => $endRow - $startRow];
    }

    private function getOverlappingEvents(Event $targetEvent): \Illuminate\Database\Eloquent\Collection
    {
        $targetStart = $targetEvent->starts_at;
        $targetEnd = $targetEvent->ends_at;

        $conflictingEvents = $targetEvent->getConflictingEvents();

        return $conflictingEvents->filter(function ($event) use ($targetStart, $targetEnd) {
            $eventStart = $event->starts_at;
            $eventEnd = $event->ends_at;
            return (
                ($eventStart->greaterThanOrEqualTo($targetStart) && $eventStart->lessThan($targetEnd)) ||
                ($eventEnd->greaterThan($targetStart) && $eventEnd->lessThanOrEqualTo($targetEnd)) ||
                ($eventStart->lessThanOrEqualTo($targetStart) && $eventEnd->greaterThanOrEqualTo($targetEnd))
            );
        });
    }

    private function calculateWidthForEvent($event): string
    {
        $overlappingEvents = $this->getOverlappingEvents($event);
        
        $count = $overlappingEvents->count() + 1;
        
        if ($count <= 1) {
            return '100%';
        }
        
        return (100 / $count) . '%';
    }

    private function getLeftPositionForEvent($targetEvent): string
    {
        $overlappingEvents = $this->getOverlappingEvents($targetEvent);

        $allOverlappingEvents = $overlappingEvents->push($targetEvent)->sortBy('starts_at');
        
        $count = $allOverlappingEvents->count();
        
        if ($count === 0) {
            return '0%';
        }

        $index = $allOverlappingEvents->search(function ($event) use ($targetEvent) {
            return $event->id === $targetEvent->id;
        });

        return ((100 / $count) * $index) . '%';
    }
}