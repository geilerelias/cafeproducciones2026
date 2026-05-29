<?php

namespace Database\Factories;

use App\Models\EventRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventRequest>
 */
class EventRequestFactory extends Factory
{
    protected $model = EventRequest::class;

    public function definition(): array
    {
        return [
            'reference' => EventRequest::nextReference(),
            'client_user_id' => User::factory(),
            'created_by' => User::factory(),
            'title' => fake()->sentence(4),
            'event_type' => 'corporativo',
            'desired_date' => fake()->dateTimeBetween('+1 week', '+3 months')->format('Y-m-d'),
            'location' => fake()->city(),
            'description' => fake()->paragraph(),
            'guest_count' => fake()->numberBetween(20, 500),
            'stage_key' => 'recibida',
            'submitted_at' => now(),
        ];
    }
}
