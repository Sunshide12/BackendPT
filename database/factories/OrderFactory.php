<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id' => Client::inRandomOrder()->first()->id,
            'date'      => $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'total'     => 0, // // se calcula en OrderSeeder una vez que los detalles del pedido existen
            'status'    => $this->faker->randomElement(['pending', 'completed', 'cancelled']),
        ];
    }
}
