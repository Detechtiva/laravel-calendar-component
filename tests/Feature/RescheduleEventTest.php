<?php

namespace Detechtiva\VueCalendarForLaravel\Tests\Feature;

use Detechtiva\VueCalendarForLaravel\Tests\Feature\database\factories\EventFactory;
use http\Exception\InvalidArgumentException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RescheduleEventTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_reschedule_an_event()
    {
        // Arrange
        $startsAt = today()->setTime(12, 0);
        $endsAt = today()->setTime(13, 30);

        $event = EventFactory::new()->create();

        // Act
        $event->reschedule($startsAt, $endsAt);

        // Assert
        $event->refresh();

        $this->assertEquals($startsAt, $event->starts_at);
        $this->assertEquals($endsAt, $event->ends_at);
    }

    /** @test */
    public function it_can_reschedule_an_event_by_passing_only_one_parameter()
    {
        // Arrange
        $startsAt = today()->setTime(15, 0);

        $event = EventFactory::new()->create();

        $minutes = $event->ends_at->diffInMinutes($event->starts_at);

        // Act
        $event->reschedule($startsAt);

        // Assert
        $event->refresh();

        $this->assertEquals($startsAt, $event->starts_at);
        $this->assertEquals($startsAt->addMinutes($minutes), $event->ends_at);
    }
}
