<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_role'   => 'ROLE' . fake()->unique()->numberBetween(100, 999),
            'nama_role' => fake()->unique()->jobTitle(),
            'menu'      => null, // null = akses penuh (default untuk testing)
        ];
    }
}
