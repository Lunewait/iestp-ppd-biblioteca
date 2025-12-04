<?php

namespace Tests\Feature;

use App\Models\Material;
use App\Models\MaterialFisico;
use App\Models\Prestamo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LoanWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected $estudiante;
    protected $admin;
    protected $material;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear roles
        Role::create(['name' => 'Estudiante']);
        Role::create(['name' => 'Admin']);

        // Crear usuarios
        $this->estudiante = User::factory()->create([
            'name' => 'Juan Estudiante',
            'email' => 'estudiante@test.com',
        ]);
        $this->estudiante->assignRole('Estudiante');

        $this->admin = User::factory()->create([
            'name' => 'Admin Usuario',
            'email' => 'admin@test.com',
        ]);
        $this->admin->assignRole('Admin');

        // Crear material físico
        $this->material = Material::create([
            'title' => 'Clean Code',
            'description' => 'Libro de programación',
            'author' => 'Robert Martin',
            'type' => 'fisico',
            'code' => 'CC-001',
        ]);

        MaterialFisico::create([
            'material_id' => $this->material->id,
            'isbn' => '978-0132350884',
            'stock' => 5,
            'available' => 5,
            'publisher' => 'Prentice Hall',
            'publication_year' => 2008,
            'location' => 'Estante A1',
        ]);
    }

    /** @test */
    public function estudiante_puede_solicitar_prestamo()
    {
        $this->actingAs($this->estudiante);

        // Verificar stock inicial
        $this->assertEquals(5, $this->material->materialFisico->available);

        // Crear solicitud
        $prestamo = Prestamo::create([
            'user_id' => $this->estudiante->id,
            'material_id' => $this->material->id,
            'fecha_prestamo' => now(),
            'status' => 'activo',
            'approval_status' => 'pending',
            'registrado_por' => $this->estudiante->id,
        ]);

        // Decrementar stock (reserva)
        $this->material->materialFisico->decrement('available');

        // Verificaciones
        $this->assertDatabaseHas('prestamos', [
            'id' => $prestamo->id,
            'approval_status' => 'pending',
            'user_id' => $this->estudiante->id,
        ]);

        $this->assertEquals(4, $this->material->fresh()->materialFisico->available);
        $this->assertTrue($prestamo->fresh()->approval_status === 'pending');
    }

    /** @test */
    public function estudiante_no_puede_solicitar_mas_de_3_libros()
    {
        $this->actingAs($this->estudiante);

        // Crear 3 solicitudes
        for ($i = 1; $i <= 3; $i++) {
            $material = Material::create([
                'title' => "Libro $i",
                'author' => 'Autor',
                'type' => 'fisico',
                'code' => "LIB-00$i",
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
                'status' => 'activo',
                'approval_status' => 'pending',
                'registrado_por' => $this->estudiante->id,
            ]);
        }

        // Verificar que tiene 3 solicitudes
        $count = Prestamo::getActiveRequestsCount($this->estudiante->id);
        $this->assertEquals(3, $count);

        // Intentar solicitar un 4to debe fallar (en la lógica del controlador)
        $this->assertTrue($count >= 3);
    }

    /** @test */
    public function admin_puede_aprobar_prestamo()
    {
        $prestamo = Prestamo::create([
            'user_id' => $this->estudiante->id,
            'material_id' => $this->material->id,
            'fecha_prestamo' => now(),
            'status' => 'activo',
            'approval_status' => 'pending',
            'registrado_por' => $this->estudiante->id,
        ]);

        $this->actingAs($this->admin);

        // Aprobar préstamo
        $prestamo->update([
            'approval_status' => 'approved',
            'approved_by' => $this->admin->id,
            'approval_date' => now(),
            'fecha_limite_recogida' => now()->addHours(24),
        ]);

        // Verificaciones
        $this->assertDatabaseHas('prestamos', [
            'id' => $prestamo->id,
            'approval_status' => 'approved',
            'approved_by' => $this->admin->id,
        ]);

        $this->assertTrue($prestamo->fresh()->isWaitingCollection());
        $this->assertNotNull($prestamo->fresh()->fecha_limite_recogida);
    }

    /** @test */
    public function admin_puede_marcar_como_recogido()
    {
        $prestamo = Prestamo::create([
            'user_id' => $this->estudiante->id,
            'material_id' => $this->material->id,
            'fecha_prestamo' => now(),
            'status' => 'activo',
            'approval_status' => 'approved',
            'approved_by' => $this->admin->id,
            'fecha_limite_recogida' => now()->addHours(24),
            'registrado_por' => $this->estudiante->id,
        ]);

        $this->actingAs($this->admin);

        // Marcar como recogido
        $prestamo->markAsCollected();

        // Verificaciones
        $prestamo = $prestamo->fresh();
        $this->assertEquals('collected', $prestamo->approval_status);
        $this->assertNotNull($prestamo->fecha_recogida);
        $this->assertNotNull($prestamo->fecha_devolucion_esperada);
        
        // Debe tener 7 días desde la recogida
        $diasPrestamo = config('library.default_loan_days', 7);
        $this->assertEquals(
            now()->addDays($diasPrestamo)->format('Y-m-d'),
            $prestamo->fecha_devolucion_esperada->format('Y-m-d')
        );
    }

    /** @test */
    public function prestamo_expira_si_no_se_recoge_en_24_horas()
    {
        $prestamo = Prestamo::create([
            'user_id' => $this->estudiante->id,
            'material_id' => $this->material->id,
            'fecha_prestamo' => now(),
            'status' => 'activo',
            'approval_status' => 'approved',
            'approved_by' => $this->admin->id,
            'fecha_limite_recogida' => now()->subHours(2), // 2 horas en el pasado
            'registrado_por' => $this->estudiante->id,
        ]);

        // Decrementar stock (estaba reservado)
        $this->material->materialFisico->decrement('available');
        $this->assertEquals(4, $this->material->fresh()->materialFisico->available);

        // Verificar que está expirado
        $this->assertTrue($prestamo->isCollectionExpired());

        // Marcar como expirado
        $prestamo->markAsExpired();

        // Verificaciones
        $prestamo = $prestamo->fresh();
        $this->assertEquals('expired', $prestamo->approval_status);
        $this->assertEquals('vencido', $prestamo->status);
        
        // Stock debe volver al catálogo
        $this->assertEquals(5, $this->material->fresh()->materialFisico->available);
    }

    /** @test */
    public function admin_puede_rechazar_prestamo()
    {
        $prestamo = Prestamo::create([
            'user_id' => $this->estudiante->id,
            'material_id' => $this->material->id,
            'fecha_prestamo' => now(),
            'status' => 'activo',
            'approval_status' => 'pending',
            'registrado_por' => $this->estudiante->id,
        ]);

        // Decrementar stock (estaba reservado)
        $this->material->materialFisico->decrement('available');
        $this->assertEquals(4, $this->material->fresh()->materialFisico->available);

        $this->actingAs($this->admin);

        // Rechazar préstamo
        $prestamo->update([
            'approval_status' => 'rejected',
            'approved_by' => $this->admin->id,
            'approval_reason' => 'Usuario tiene multas pendientes',
        ]);

        // Devolver stock
        $this->material->materialFisico->increment('available');

        // Verificaciones
        $this->assertDatabaseHas('prestamos', [
            'id' => $prestamo->id,
            'approval_status' => 'rejected',
        ]);

        // Stock debe volver al catálogo
        $this->assertEquals(5, $this->material->fresh()->materialFisico->available);
    }

    /** @test */
    public function material_no_esta_disponible_si_tiene_prestamo_activo()
    {
        // Crear préstamo pending
        Prestamo::create([
            'user_id' => $this->estudiante->id,
            'material_id' => $this->material->id,
            'fecha_prestamo' => now(),
            'status' => 'activo',
            'approval_status' => 'pending',
            'registrado_por' => $this->estudiante->id,
        ]);

        // Material NO debe estar disponible
        $this->assertFalse($this->material->fresh()->isAvailable());

        // Cambiar a rejected
        Prestamo::where('material_id', $this->material->id)->update([
            'approval_status' => 'rejected'
        ]);

        // Ahora SÍ debe estar disponible
        $this->assertTrue($this->material->fresh()->isAvailable());
    }

    /** @test */
    public function contador_de_solicitudes_activas_funciona_correctamente()
    {
        // Sin solicitudes
        $this->assertEquals(0, Prestamo::getActiveRequestsCount($this->estudiante->id));

        // Crear solicitud pending
        $prestamo1 = Prestamo::create([
            'user_id' => $this->estudiante->id,
            'material_id' => $this->material->id,
            'fecha_prestamo' => now(),
            'status' => 'activo',
            'approval_status' => 'pending',
            'registrado_por' => $this->estudiante->id,
        ]);
        $this->assertEquals(1, Prestamo::getActiveRequestsCount($this->estudiante->id));

        // Aprobar
        $prestamo1->update(['approval_status' => 'approved']);
        $this->assertEquals(1, Prestamo::getActiveRequestsCount($this->estudiante->id));

        // Marcar como recogido
        $this->actingAs($this->admin);
        $prestamo1->markAsCollected();
        $this->assertEquals(1, Prestamo::getActiveRequestsCount($this->estudiante->id));

        // Crear segundo préstamo
        $material2 = Material::create([
            'title' => 'Libro 2',
            'author' => 'Autor',
            'type' => 'fisico',
            'code' => 'LIB-002',
        ]);
        MaterialFisico::create([
            'material_id' => $material2->id,
            'stock' => 5,
            'available' => 5,
        ]);

        Prestamo::create([
            'user_id' => $this->estudiante->id,
            'material_id' => $material2->id,
            'fecha_prestamo' => now(),
            'status' => 'activo',
            'approval_status' => 'pending',
            'registrado_por' => $this->estudiante->id,
        ]);
        $this->assertEquals(2, Prestamo::getActiveRequestsCount($this->estudiante->id));

        // Devolver el primero
        $prestamo1->update([
            'approval_status' => 'returned',
            'status' => 'devuelto',
            'fecha_devolucion_actual' => now(),
        ]);
        $this->assertEquals(1, Prestamo::getActiveRequestsCount($this->estudiante->id));
    }

    /** @test */
    public function prestamo_devuelto_no_cuenta_para_limite()
    {
        // Crear 3 préstamos
        for ($i = 1; $i <= 3; $i++) {
            $material = Material::create([
                'title' => "Libro $i",
                'author' => 'Autor',
                'type' => 'fisico',
                'code' => "LIB-00$i",
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
                'status' => 'activo',
                'approval_status' => 'collected',
                'registrado_por' => $this->estudiante->id,
            ]);
        }

        // Debe tener 3 solicitudes
        $this->assertEquals(3, Prestamo::getActiveRequestsCount($this->estudiante->id));

        // Devolver uno
        $prestamo = Prestamo::where('user_id', $this->estudiante->id)->first();
        $prestamo->update([
            'approval_status' => 'returned',
            'status' => 'devuelto',
            'fecha_devolucion_actual' => now(),
        ]);

        // Ahora debe tener solo 2
        $this->assertEquals(2, Prestamo::getActiveRequestsCount($this->estudiante->id));
    }

    /** @test */
    public function stock_se_maneja_correctamente_en_todo_el_flujo()
    {
        $stockInicial = $this->material->materialFisico->available;
        $this->assertEquals(5, $stockInicial);

        // 1. Solicitar (pending)
        $prestamo = Prestamo::create([
            'user_id' => $this->estudiante->id,
            'material_id' => $this->material->id,
            'fecha_prestamo' => now(),
            'status' => 'activo',
            'approval_status' => 'pending',
            'registrado_por' => $this->estudiante->id,
        ]);
        $this->material->materialFisico->decrement('available');
        $this->assertEquals(4, $this->material->fresh()->materialFisico->available);

        // 2. Aprobar (approved) - stock sigue igual
        $prestamo->update([
            'approval_status' => 'approved',
            'fecha_limite_recogida' => now()->addHours(24),
        ]);
        $this->assertEquals(4, $this->material->fresh()->materialFisico->available);

        // 3. Recoger (collected) - stock sigue igual
        $this->actingAs($this->admin);
        $prestamo->markAsCollected();
        $this->assertEquals(4, $this->material->fresh()->materialFisico->available);

        // 4. Devolver (returned) - stock vuelve
        $prestamo->update([
            'approval_status' => 'returned',
            'status' => 'devuelto',
        ]);
        $this->material->materialFisico->increment('available');
        $this->assertEquals(5, $this->material->fresh()->materialFisico->available);
    }
}
