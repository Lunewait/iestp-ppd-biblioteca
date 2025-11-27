<?php

namespace Database\Factories;

use App\Models\Material;
use App\Models\Prestamo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Prestamo>
 */
class PrestamoFactory extends Factory
{
    protected $model = Prestamo::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['activo', 'devuelto', 'vencido'];

        return [
            'user_id' => User::factory(),
            'material_id' => Material::factory(),
            'fecha_prestamo' => fake()->dateTimeBetween('-30 days'),
            'fecha_devolucion_esperada' => fake()->dateTimeBetween('now', '+30 days'),
            'fecha_devolucion_actual' => null,
            'status' => 'activo',
            'registrado_por' => User::factory(),
            'notas' => fake()->optional()->sentence(),
        ];
    }
}
