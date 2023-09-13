<?php

namespace Detechtiva\VueCalendarForLaravel\Tests\Feature;

use Detechtiva\VueCalendarForLaravel\Models\Event;
use Detechtiva\VueCalendarForLaravel\Models\EventParticipant;
use Detechtiva\VueCalendarForLaravel\Tests\Feature\database\factories\EventFactory;
use Detechtiva\VueCalendarForLaravel\Tests\Feature\database\factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RemoveParticipantTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_remove_a_participant()
    {
        // Arrange
        $participant = UserFactory::new()->create();
        $event = EventFactory::new()->create();
        $event->eventParticipants()->create([
            'participant_type' => get_class($participant),
            'participant_id' => $participant->id,
            'event_id' => $event->id,
        ]);

        $eventParticipant = EventParticipant::first();

        $this->assertCount(1, $event->eventParticipants);

        // Act
        $event->removeParticipant($eventParticipant);

        // Assert
        $this->assertCount(0, $event->fresh()->eventParticipants);
    }
}
