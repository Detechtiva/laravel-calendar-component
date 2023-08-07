<?php

namespace Detechtiva\VueCalendarForLaravel\Tests\Feature\database\factories;

use Carbon\CarbonImmutable;
use Detechtiva\VueCalendarForLaravel\Models\Event;
use Detechtiva\VueCalendarForLaravel\Tests\Feature\TestModels\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $hours = collect([12, 13, 14, 15]);
        $duration = collect([30, 60, 90]);
        $startsAt = CarbonImmutable::today()
            ->setTime($hours->random(), 0);

        return [
            'model_type' => User::class,
            'model_id' => UserFactory::new(),
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->addMinutes($duration->random()),
            'is_all_day' => false,
            'created_by_type' => User::class,
            'created_by_id' => UserFactory::new(),
        ];
    }
}
