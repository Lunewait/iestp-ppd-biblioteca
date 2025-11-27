<?php

namespace Tests\Unit;

use App\Models\Material;
use App\Models\Prestamo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrestamoModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_loan_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $material = Material::factory()->create();

        $loan = Prestamo::factory()->create([
            'user_id' => $user->id,
            'material_id' => $material->id,
        ]);

        $this->assertTrue($loan->usuario->is($user));
    }

    /** @test */
    public function a_loan_belongs_to_a_material()
    {
        $user = User::factory()->create();
        $material = Material::factory()->create();

        $loan = Prestamo::factory()->create([
            'user_id' => $user->id,
            'material_id' => $material->id,
        ]);

        $this->assertTrue($loan->material->is($material));
    }

    /** @test */
    public function can_check_if_loan_is_overdue()
    {
        $user = User::factory()->create();
        $material = Material::factory()->create();

        // Create overdue loan
        $overdueLoan = Prestamo::factory()->create([
            'user_id' => $user->id,
            'material_id' => $material->id,
            'fecha_devolucion_esperada' => now()->subDays(5),
            'status' => 'activo',
        ]);

        $this->assertTrue($overdueLoan->isOverdue());

        // Create active loan not overdue
        $activeLoan = Prestamo::factory()->create([
            'user_id' => $user->id,
            'material_id' => $material->id,
            'fecha_devolucion_esperada' => now()->addDays(5),
            'status' => 'activo',
        ]);

        $this->assertFalse($activeLoan->isOverdue());
    }
}
