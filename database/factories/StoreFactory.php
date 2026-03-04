<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StoreFactory extends Factory
{
    public function definition(): array
    {
        return [
            'gestao_click_id' => $this->faker->unique()->uuid(),
            'shipping_amount' => $this->faker->randomFloat(2, 5, 50),
            'unit_price' => $this->faker->randomFloat(2, 10, 30),
            'can_use_boleto' => $this->faker->boolean(),
            'boleto_due_days' => $this->faker->numberBetween(2, 7),
            'orders_count' => $this->faker->numberBetween(0, 100),
        ];
    }
}
