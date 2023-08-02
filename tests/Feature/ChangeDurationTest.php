<?php

namespace Detechtiva\VueCalendarForLaravel\Tests\Feature;

use Detechtiva\VueCalendarForLaravel\Models\Event;
use Detechtiva\VueCalendarForLaravel\Tests\Feature\database\factories\EventFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;

class ChangeDurationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_change_the_duration_of_the_event()
    {
        // Arrange
        $event = EventFactory::new()->create();

        $startsAt = $event->starts_at;

        // Act
        $event->changeDuration(2, 'hour');

        // Assert
        $event->refresh();

        $this->assertEquals($startsAt->addHours(2), $event->ends_at);
    }

    /** @test */
    public function it_should_throw_an_exception_if_the_unit_is_not_valid()
    {
        // Arrange

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The unit must be one of the following: minute, hour, day.');
        $this->expectExceptionCode(422);

        $event = EventFactory::new()->create();

        // Act
        $event->changeDuration(2, 'invalid');
    }
}
