<?php

namespace Tests\Unit;

use App\Models\Material;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaterialModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_material_can_have_physical_details()
    {
        $material = Material::factory()->create(['type' => 'fisico']);
        $material->materialFisico()->create([
            'isbn' => '978-0-123456-78-9',
            'stock' => 5,
            'available' => 3,
        ]);

        $this->assertNotNull($material->materialFisico);
        $this->assertEquals('978-0-123456-78-9', $material->materialFisico->isbn);
    }

    /** @test */
    public function a_material_can_have_digital_details()
    {
        $material = Material::factory()->create(['type' => 'digital']);
        $material->materialDigital()->create([
            'url' => 'https://example.com/document.pdf',
            'downloadable' => 1,
            'file_type' => 'pdf',
        ]);

        $this->assertNotNull($material->materialDigital);
        $this->assertEquals(1, $material->materialDigital->downloadable);
    }

    /** @test */
    public function can_check_material_availability()
    {
        $physicalMaterial = Material::factory()->create(['type' => 'fisico']);
        $physicalMaterial->materialFisico()->create([
            'available' => 0,
        ]);

        $this->assertFalse($physicalMaterial->isAvailable());

        $availableMaterial = Material::factory()->create(['type' => 'fisico']);
        $availableMaterial->materialFisico()->create([
            'available' => 5,
        ]);

        $this->assertTrue($availableMaterial->isAvailable());

        $digitalMaterial = Material::factory()->create(['type' => 'digital']);
        $this->assertTrue($digitalMaterial->isAvailable());
    }
}
