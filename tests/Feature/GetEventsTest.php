<?php

namespace Detechtiva\VueCalendarForLaravel\Tests\Feature;

use Carbon\Carbon;
use Detechtiva\VueCalendarForLaravel\EventManagerService;
use Detechtiva\VueCalendarForLaravel\Models\Event;
use Detechtiva\VueCalendarForLaravel\Tests\Feature\database\factories\EventFactory;
use Detechtiva\VueCalendarForLaravel\Tests\Feature\database\factories\UserFactory;
use Detechtiva\VueCalendarForLaravel\Tests\Feature\TestModels\User;
use Detechtiva\VueCalendarForLaravel\Tests\Feature\TestModels\WorkOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetEventsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(today()->startOfMonth()->addDays(14));
    }

    /** @test */
    public function it_can_get_current_events()
    {
        // Arrange
        $events = EventFactory::new()->count(5)->create();

        // Act
        $queryResults = EventManagerService::new()
            ->currentMonth()
            ->get();

        // Assert
        $this->assertCount(5, $queryResults);
        $this->assertEquals($events->map->id->toArray(), $queryResults->map->id->toArray());
    }

    /** @test */
    public function it_can_get_events_by_model()
    {
        // Arrange
        $workOrder = UserFactory::new()->create();

        $eventForModel = EventFactory::new()
            ->create(['model_id' => $workOrder->id, 'model_type' => WorkOrder::class]);

        EventFactory::new()->count(4)->create();

        // Act
        $queryResults = EventManagerService::new()
            ->forModel(WorkOrder::class)
            ->get();

        // Assert
        $this->assertCount(1, $queryResults);
        $this->assertEquals($eventForModel->id, $queryResults->first()['id']);
    }

    /** @test */
    public function it_can_get_events_in_specific_month()
    {
        // Arrange
        $events = EventFactory::new()
            ->count(4)
            ->create(['starts_at' => now()->addMonth(), 'ends_at' => now()->addMonth()->addMinutes(30)]);

        EventFactory::new()->count(4)->create();

        // Act
        $queryResults = EventManagerService::new()
            ->inMonth(today()->year, today()->addMonth()->month)
            ->get();

        // Assert
        $this->assertCount(4, $queryResults);
        $this->assertEquals($events->map->id->toArray(), $queryResults->map->id->toArray());
    }

    /** @test */
    public function it_can_get_events_in_date_range()
    {
        // Arrange
        $eventInRange = EventFactory::new()
            ->create(['starts_at' => today()->addDay(), 'ends_at' => today()->addDays(5)]);

        EventFactory::new()->count(4)->create(); // Events outside the range

        // Act
        $queryResults = EventManagerService::new()
            ->between(today()->addDay(), today()->addDays(5))
            ->get();

        // Assert
        $this->assertCount(1, $queryResults);
        $this->assertEquals($eventInRange->id, $queryResults->first()['id']);
    }

    /** @test */
    public function it_can_get_events_by_week()
    {
        Carbon::setTestNow();

        // Arrange
        $mondayEvents = EventFactory::new()
            ->count(2)
            ->create(['starts_at' => today()->startOfWeek(), 'ends_at' => today()->startOfWeek()->addHour()]);

        $wednesdayEvents = EventFactory::new()
            ->count(2)
            ->create(['starts_at' => today()->startOfWeek()->addDays(2), 'ends_at' => today()->startOfWeek()->addDays(2)->addHour()]);

        // Act
        $queryResults = EventManagerService::new()
            ->between(today()->startOfWeek(), today()->endOfWeek())
            ->getEventsByWeek();

        // Assert
        $this->assertCount(7, $queryResults);

        // Monday
        $this->assertCount(3, $queryResults->first());
        $this->assertEquals($mondayEvents->map->id->toArray(), $queryResults->first()['events']->map->id->toArray());

        // Tuesday
        $this->assertCount(0, $queryResults->get(1)['events']);

        // Wednesday
        $this->assertCount(3, $queryResults->get(2));
        $this->assertEquals(
            $wednesdayEvents->map->id->toArray(),
            $queryResults->get(2)['events']->map->id->toArray()
        );

        // From Thursday to Sunday
        $this->assertCount(0, $queryResults->get(3)['events']);
        $this->assertCount(0, $queryResults->get(4)['events']);
        $this->assertCount(0, $queryResults->get(5)['events']);
        $this->assertCount(0, $queryResults->get(6)['events']);
    }
}
