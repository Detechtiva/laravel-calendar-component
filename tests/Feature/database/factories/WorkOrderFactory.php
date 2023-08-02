<?php

namespace Detechtiva\VueCalendarForLaravel\Tests\Feature\database\factories;

use Detechtiva\VueCalendarForLaravel\Tests\Feature\TestModels\WorkOrder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class WorkOrderFactory extends Factory
{
    protected $model = WorkOrder::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
