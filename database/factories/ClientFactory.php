<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state([
                'name' => 'Client',
                'email' => 'client@email.com',
            ])->afterCreating(function (User $user): void {
                $user->assignRole('client');
            }),
            'phone' => $this->faker->unique()->numerify('55119########'),
        ];
    }
}
