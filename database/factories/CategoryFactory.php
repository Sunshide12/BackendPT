<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{

    public function definition(): array
    {
        $categories = [
            'Laptops y Computadoras',
            'Monitores y Pantallas',
            'Periféricos',
            'Almacenamiento',
            'Componentes PC',
            'Redes y Conectividad',
            'Smartphones y Tablets',
            'Audio y Video',
            'Accesorios Gaming',
            'Impresión y Oficina',
        ];

        return [
            'name' => $this->faker->unique()->randomElement($categories),
        ];
    }
}
