<?php

namespace Database\Factories;

use App\Models\Material;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Material>
 */
class MaterialFactory extends Factory
{
    protected $model = Material::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['fisico', 'digital', 'hibrido'];

        return [
            'title' => fake()->sentence(),
            'author' => fake()->name(),
            'description' => fake()->paragraph(),
            'type' => fake()->randomElement($types),
            'code' => fake()->unique()->bothify('MAT-????-####'),
            'keywords' => fake()->words(3, true),
        ];
    }
}
