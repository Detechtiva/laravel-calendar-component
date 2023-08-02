<?php

namespace Detechtiva\VueCalendarForLaravel\Tests\Feature;

use Carbon\Carbon;
use Detechtiva\VueCalendarForLaravel\EventBuilder;
use Detechtiva\VueCalendarForLaravel\Models\Event;
use Detechtiva\VueCalendarForLaravel\Tests\Feature\database\factories\UserFactory;
use Detechtiva\VueCalendarForLaravel\Tests\Feature\database\factories\WorkOrderFactory;
use Detechtiva\VueCalendarForLaravel\Tests\Feature\TestModels\WorkOrder;
use Detechtiva\VueCalendarForLaravel\Tests\Feature\TestModels\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;

class CreateEventTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(today()->setTime(12, 0));
    }

    /** @test */
    public function it_can_create_an_event()
    {
        // Arrange
        $this->actingAs(
            $user = UserFactory::new()->create()
        );

        $workOrder = WorkOrderFactory::new()->create();

        // Act
        EventBuilder::new()
            ->for($workOrder)
            ->withTitle($title = 'My title')
            ->withDescription($description = 'My description')
            ->startingAt($start = now())
            ->endingAt($end = now()->addHour())
            ->create();

        // Assert
        $this->assertCount(1, Event::all());

        $event = Event::first();

        $this->assertEquals($title, $event->title);
        $this->assertEquals($description, $event->description);
        $this->assertEquals($start, $event->starts_at);
        $this->assertEquals($end, $event->ends_at);
        $this->assertEquals($workOrder->id, $event->model_id);
        $this->assertEquals(WorkOrder::class, $event->model_type);
        $this->assertFalse($event->is_all_day);
        $this->assertEquals($user->id, $event->created_by_id);
        $this->assertEquals(User::class, $event->created_by_type);
    }

    /** @test */
    public function title_cannot_be_empty()
    {
        // Arrange
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Title cannot be empty');
        $this->expectExceptionCode(422);

        $workOrder = WorkOrderFactory::new()->create();

        // Act
        EventBuilder::new()
            ->for($workOrder)
            ->withDescription($description = 'My description')
            ->startingAt($start = now())
            ->endingAt($end = now()->addHour())
            ->create();

        // Assert
        $this->assertCount(0, Event::all());
    }
}
