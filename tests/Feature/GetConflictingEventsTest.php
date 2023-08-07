<?php

namespace Detechtiva\VueCalendarForLaravel\Tests\Feature;

use Detechtiva\VueCalendarForLaravel\Tests\Feature\database\factories\EventFactory;
use Detechtiva\VueCalendarForLaravel\Tests\Feature\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetConflictingEventsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_get_conflicting_events()
    {
        // Arrange
        $event = EventFactory::new()
            ->create(['starts_at' => today()->setTime(12, 0), 'ends_at' => today()->setTime(14, 0)]);

        // Conflicting events
        $conflictingEventOne = EventFactory::new()
            ->create(['starts_at' => today()->setTime(13, 0), 'ends_at' => today()->setTime(15, 0)]);
        $conflictingEventTwo = EventFactory::new()
            ->create(['starts_at' => today()->setTime(11, 0), 'ends_at' => today()->setTime(13, 0)]);

        // Non-conflicting events
        EventFactory::new()
            ->create(['starts_at' => today()->setTime(10, 0), 'ends_at' => today()->setTime(12, 0)]);
        EventFactory::new()
            ->create(['starts_at' => today()->setTime(14, 0), 'ends_at' => today()->setTime(16, 0)]);

        // Act
        $events = $event->getConflictingEvents();

        // Assert
        $this->assertCount(2, $events);

        $this->assertEquals($conflictingEventOne->id, $events[0]->id);
        $this->assertEquals($conflictingEventTwo->id, $events[1]->id);
    }
}
