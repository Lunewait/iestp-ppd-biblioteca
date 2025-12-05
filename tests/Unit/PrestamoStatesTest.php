<?php

namespace Tests\Unit;

use App\Models\Material;
use App\Models\MaterialFisico;
use App\Models\Prestamo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PrestamoStatesTest extends TestCase
{
    use RefreshDatabase;

    protected $prestamo;
    protected $estudiante;
    protected $admin;
    protected $material;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'Estudiante']);
        Role::create(['name' => 'Admin']);

        $this->estudiante = User::factory()->create();
        $this->estudiante->assignRole('Estudiante');

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

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
    public function prestamo_esta_esperando_recogida_si_esta_aprobado_sin_fecha_recogida()
    {
        $prestamo = Prestamo::create([
            'user_id' => $this->estudiante->id,
            'material_id' => $this->material->id,
            'fecha_prestamo' => now(),
            'status' => 'activo',
            'approval_status' => 'approved',
            'fecha_limite_recogida' => now()->addHours(24),
            'registrado_por' => $this->estudiante->id,
        ]);

        $this->assertTrue($prestamo->isWaitingCollection());
    }

    /** @test */
    public function prestamo_no_esta_esperando_recogida_si_ya_fue_recogido()
    {
        $prestamo = Prestamo::create([
            'user_id' => $this->estudiante->id,
            'material_id' => $this->material->id,
            'fecha_prestamo' => now(),
            'status' => 'activo',
            'approval_status' => 'collected',
            'fecha_recogida' => now(),
            'registrado_por' => $this->estudiante->id,
        ]);

        $this->assertFalse($prestamo->isWaitingCollection());
    }

    /** @test */
    public function prestamo_esta_expirado_si_paso_limite_de_recogida()
    {
        $prestamo = Prestamo::create([
            'user_id' => $this->estudiante->id,
            'material_id' => $this->material->id,
            'fecha_prestamo' => now(),
            'status' => 'activo',
            'approval_status' => 'approved',
            'fecha_limite_recogida' => now()->subHours(2), // 2 horas atrás
            'registrado_por' => $this->estudiante->id,
        ]);

        $this->assertTrue($prestamo->isCollectionExpired());
    }

    /** @test */
    public function prestamo_no_esta_expirado_si_aun_hay_tiempo()
    {
        $prestamo = Prestamo::create([
            'user_id' => $this->estudiante->id,
            'material_id' => $this->material->id,
            'fecha_prestamo' => now(),
            'status' => 'activo',
            'approval_status' => 'approved',
            'fecha_limite_recogida' => now()->addHours(12), // 12 horas adelante
            'registrado_por' => $this->estudiante->id,
        ]);

        $this->assertFalse($prestamo->isCollectionExpired());
    }

    /** @test */
    public function prestamo_esta_activo_si_fue_recogido()
    {
        $prestamo = Prestamo::create([
            'user_id' => $this->estudiante->id,
            'material_id' => $this->material->id,
            'fecha_prestamo' => now(),
            'status' => 'activo',
            'approval_status' => 'collected',
            'fecha_recogida' => now(),
            'registrado_por' => $this->estudiante->id,
        ]);

        $this->assertTrue($prestamo->isActive());
    }

    /** @test */
    public function prestamo_no_esta_activo_si_solo_esta_aprobado()
    {
        $prestamo = Prestamo::create([
            'user_id' => $this->estudiante->id,
            'material_id' => $this->material->id,
            'fecha_prestamo' => now(),
            'status' => 'activo',
            'approval_status' => 'approved',
            'registrado_por' => $this->estudiante->id,
        ]);

        $this->assertFalse($prestamo->isActive());
    }

    /** @test */
    public function marcar_como_recogido_establece_fechas_correctamente()
    {
        $this->actingAs($this->admin);

        $prestamo = Prestamo::create([
            'user_id' => $this->estudiante->id,
            'material_id' => $this->material->id,
            'fecha_prestamo' => now(),
            'status' => 'activo',
            'approval_status' => 'approved',
            'fecha_limite_recogida' => now()->addHours(24),
            'registrado_por' => $this->estudiante->id,
        ]);

        $prestamo->markAsCollected();
        $prestamo = $prestamo->fresh();

        $this->assertEquals('collected', $prestamo->approval_status);
        $this->assertNotNull($prestamo->fecha_recogida);
        $this->assertNotNull($prestamo->fecha_devolucion_esperada);
        
        // Verificar que tiene 7 días (configuración por defecto)
        $diasPrestamo = config('library.default_loan_days', 7);
        $fechaEsperada = Carbon::parse($prestamo->fecha_recogida)->addDays($diasPrestamo);
        $this->assertEquals(
            $fechaEsperada->format('Y-m-d'),
            $prestamo->fecha_devolucion_esperada->format('Y-m-d')
        );
    }

    /** @test */
    public function marcar_como_expirado_devuelve_stock()
    {
        $stockInicial = $this->material->materialFisico->available;
        
        // Decrementar stock (simular reserva)
        $this->material->materialFisico->decrement('available');

        $prestamo = Prestamo::create([
            'user_id' => $this->estudiante->id,
            'material_id' => $this->material->id,
            'fecha_prestamo' => now(),
            'status' => 'activo',
            'approval_status' => 'approved',
            'fecha_limite_recogida' => now()->subHours(2),
            'registrado_por' => $this->estudiante->id,
        ]);

        $this->assertEquals($stockInicial - 1, $this->material->fresh()->materialFisico->available);

        $prestamo->markAsExpired();

        $this->assertEquals('expired', $prestamo->fresh()->approval_status);
        $this->assertEquals($stockInicial, $this->material->fresh()->materialFisico->available);
    }

    /** @test */
    public function contador_de_solicitudes_cuenta_solo_estados_activos()
    {
        // Crear préstamos en diferentes estados
        $estados = [
            'pending',
            'approved',
            'collected',
            'returned',
            'rejected',
            'expired',
        ];

        foreach ($estados as $estado) {
            $material = Material::create([
                'title' => "Book $estado",
                'author' => 'Author',
                'type' => 'fisico',
                'code' => "CODE-$estado",
            ]);

            MaterialFisico::create([
                'material_id' => $material->id,
                'stock' => 5,
                'available' => 5,
            ]);

            Prestamo::create([
                'user_id' => $this->estudiante->id,
                'material_id' => $material->id,
                'fecha_prestamo' => now(),
                'status' => in_array($estado, ['returned', 'expired']) ? 'devuelto' : 'activo',
                'approval_status' => $estado,
                'registrado_por' => $this->estudiante->id,
            ]);
        }

        // Solo deben contar: pending, approved, collected (3 de 6)
        $count = Prestamo::getActiveRequestsCount($this->estudiante->id);
        $this->assertEquals(3, $count);
    }

    /** @test */
    public function prestamo_vencido_si_paso_fecha_devolucion()
    {
        $prestamo = Prestamo::create([
            'user_id' => $this->estudiante->id,
            'material_id' => $this->material->id,
            'fecha_prestamo' => now()->subDays(10),
            'fecha_devolucion_esperada' => now()->subDays(3),
            'status' => 'activo',
            'approval_status' => 'collected',
            'registrado_por' => $this->estudiante->id,
        ]);

        $this->assertTrue($prestamo->isOverdue());
        $this->assertEquals(3, $prestamo->getDaysOverdue());
    }

    /** @test */
    public function prestamo_no_vencido_si_aun_hay_tiempo()
    {
        $prestamo = Prestamo::create([
            'user_id' => $this->estudiante->id,
            'material_id' => $this->material->id,
            'fecha_prestamo' => now(),
            'fecha_devolucion_esperada' => now()->addDays(5),
            'status' => 'activo',
            'approval_status' => 'collected',
            'registrado_por' => $this->estudiante->id,
        ]);

        $this->assertFalse($prestamo->isOverdue());
        $this->assertEquals(0, $prestamo->getDaysOverdue());
    }
}
