<?php

namespace Tests\Unit;

use App\Models\Material;
use App\Models\MaterialFisico;
use App\Models\Prestamo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MaterialAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    protected $material;
    protected $estudiante;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'Estudiante']);

        $this->estudiante = User::factory()->create();
        $this->estudiante->assignRole('Estudiante');

        $this->material = Material::create([
            'title' => 'Test Book',
            'author' => 'Test Author',
            'type' => 'fisico',
            'code' => 'TEST-001',
        ]);

        MaterialFisico::create([
            'material_id' => $this->material->id,
            'stock' => 5,
            'available' => 5,
        ]);
    }

    /** @test */
    public function material_esta_disponible_si_tiene_stock()
    {
        $this->assertTrue($this->material->isAvailable());
    }

    /** @test */
    public function material_no_esta_disponible_si_stock_es_cero()
    {
        $this->material->materialFisico->update(['available' => 0]);
        $this->assertFalse($this->material->fresh()->isAvailable());
    }

    /** @test */
    public function material_no_esta_disponible_si_tiene_prestamo_pending()
    {
        Prestamo::create([
            'user_id' => $this->estudiante->id,
            'material_id' => $this->material->id,
            'fecha_prestamo' => now(),
            'status' => 'activo',
            'approval_status' => 'pending',
            'registrado_por' => $this->estudiante->id,
        ]);

        $this->assertFalse($this->material->fresh()->isAvailable());
    }

    /** @test */
    public function material_no_esta_disponible_si_tiene_prestamo_approved()
    {
        Prestamo::create([
            'user_id' => $this->estudiante->id,
            'material_id' => $this->material->id,
            'fecha_prestamo' => now(),
            'status' => 'activo',
            'approval_status' => 'approved',
            'registrado_por' => $this->estudiante->id,
        ]);

        $this->assertFalse($this->material->fresh()->isAvailable());
    }

    /** @test */
    public function material_no_esta_disponible_si_tiene_prestamo_collected()
    {
        Prestamo::create([
            'user_id' => $this->estudiante->id,
            'material_id' => $this->material->id,
            'fecha_prestamo' => now(),
            'status' => 'activo',
            'approval_status' => 'collected',
            'registrado_por' => $this->estudiante->id,
        ]);

        $this->assertFalse($this->material->fresh()->isAvailable());
    }

    /** @test */
    public function material_esta_disponible_si_prestamo_fue_devuelto()
    {
        Prestamo::create([
            'user_id' => $this->estudiante->id,
            'material_id' => $this->material->id,
            'fecha_prestamo' => now(),
            'status' => 'devuelto',
            'approval_status' => 'returned',
            'registrado_por' => $this->estudiante->id,
        ]);

        $this->assertTrue($this->material->fresh()->isAvailable());
    }

    /** @test */
    public function material_esta_disponible_si_prestamo_fue_rechazado()
    {
        Prestamo::create([
            'user_id' => $this->estudiante->id,
            'material_id' => $this->material->id,
            'fecha_prestamo' => now(),
            'status' => 'activo',
            'approval_status' => 'rejected',
            'registrado_por' => $this->estudiante->id,
        ]);

        $this->assertTrue($this->material->fresh()->isAvailable());
    }

    /** @test */
    public function material_esta_disponible_si_prestamo_expiro()
    {
        Prestamo::create([
            'user_id' => $this->estudiante->id,
            'material_id' => $this->material->id,
            'fecha_prestamo' => now(),
            'status' => 'vencido',
            'approval_status' => 'expired',
            'registrado_por' => $this->estudiante->id,
        ]);

        $this->assertTrue($this->material->fresh()->isAvailable());
    }

    /** @test */
    public function material_digital_siempre_esta_disponible()
    {
        $digitalMaterial = Material::create([
            'title' => 'Digital Book',
            'author' => 'Digital Author',
            'type' => 'digital',
            'code' => 'DIG-001',
        ]);

        $this->assertTrue($digitalMaterial->isAvailable());

        // Incluso con préstamo activo
        Prestamo::create([
            'user_id' => $this->estudiante->id,
            'material_id' => $digitalMaterial->id,
            'fecha_prestamo' => now(),
            'status' => 'activo',
            'approval_status' => 'collected',
            'registrado_por' => $this->estudiante->id,
        ]);

        $this->assertTrue($digitalMaterial->fresh()->isAvailable());
    }
}
