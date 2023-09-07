<?php

namespace Detechtiva\VueCalendarForLaravel\Tests\Feature\database\factories;

use Detechtiva\VueCalendarForLaravel\Tests\Feature\TestModels\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
        ];
    }
}
