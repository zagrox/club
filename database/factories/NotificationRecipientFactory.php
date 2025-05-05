<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\NotificationRecipient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NotificationRecipient>
 */
class NotificationRecipientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NotificationRecipient::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'notification_id' => Notification::factory(),
            'user_id' => User::factory(),
            'read_at' => $this->faker->optional(0.3)->dateTimeBetween('-1 month', 'now'),
            'dismissed_at' => $this->faker->optional(0.2)->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Indicate that the notification has been read.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function read()
    {
        return $this->state(function (array $attributes) {
            return [
                'read_at' => now(),
            ];
        });
    }

    /**
     * Indicate that the notification has been dismissed.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function dismissed()
    {
        return $this->state(function (array $attributes) {
            return [
                'dismissed_at' => now(),
            ];
        });
    }
} 