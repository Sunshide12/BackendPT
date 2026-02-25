<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $faker = 'es_ES';

    public function definition(): array
    {
        return [
            'name'  => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->numerify('###-###-####'),
        ];
    }
}
