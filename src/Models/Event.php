<?php

namespace Detechtiva\VueCalendarForLaravel\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_type',
        'model_id',
        'title',
        'description',
        'starts_at',
        'ends_at',
        'canceled_at',
        'duration_in_minutes',
        'extras',
        'is_all_day',
        'created_by_type',
        'created_by_id',
        'parent_id'
    ];

    protected $casts = [
        'is_all_day' => 'bool',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'canceled_at' => 'datetime',
        'duration_in_minutes' => 'integer',
        'extras' => 'json',
    ];

    public function eventParent(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'parent_id');
    }
    
    public function eventChildren(): HasOne
    {
        return $this->hasOne(Event::class, 'parent_id');
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function eventParticipants(): HasMany
    {
        return $this->hasMany(EventParticipant::class);
    }

    public function reschedule(Carbon $startsAt, ?Carbon $endsAt = null): void
    {
        if (empty($endsAt)) {
            $durationInMinutes = $this->starts_at->diffInMinutes($this->ends_at);

            $endsAt = $startsAt->copy()->addMinutes($durationInMinutes);
        }

        $this->starts_at = $startsAt;
        $this->ends_at = $endsAt;

        $this->save();
    }

    public function removeParticipant(int|EventParticipant $data): void
    {
        $participantId = $data instanceof EventParticipant ? $data->id : $data;

        $this->eventParticipants()->where('id', $participantId)->delete();
    }


    public function changeDuration($duration, $unit): void
    {
        // If the unit is not valid, throw an exception
        if (!in_array($unit, ['minute', 'hour', 'day'])) {
            throw new InvalidArgumentException('The unit must be one of the following: minute, hour, day.', 422);
        }

        $this->ends_at = $this->starts_at->copy()->add($duration, $unit);

        $this->save();
    }

    public function getConflictingEvents(): Collection
    {
        return Event::where(function ($query) {
            $query->where('starts_at', '<', $this->ends_at)
                ->where('ends_at', '>', $this->starts_at);
        })->get();
    }

}