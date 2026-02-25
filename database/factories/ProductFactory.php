<?php

namespace Database\Factories;

use App\Models\Category;
use App\Data\ProductNames;
use Illuminate\Database\Eloquent\Factories\Factory;


class ProductFactory extends Factory
{
    
    public function definition(): array
    {
        return [
            'name'        => $this->faker->randomelement(ProductNames::all()) . ' ' . $this->faker->unique()->numberBetween(1, 100),
            'price'       => $this->faker->randomFloat(2, 5, 500),

            // algunos productos con stock bajo para probar la validación
            'stock'       => $this->faker->randomElement([0, 2, 5, 10, 20, 50, 100]),
            'category_id' => Category::inRandomOrder()->first()->id,
        ];
    }
}
