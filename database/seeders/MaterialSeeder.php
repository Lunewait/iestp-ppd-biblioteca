<?php

namespace Database\Seeders;

use App\Models\Material;
use App\Models\MaterialFisico;
use App\Models\MaterialDigital;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Materiales Físicos
        $materialesFisicos = [
            [
                'title' => 'Clean Code',
                'author' => 'Robert C. Martin',
                'code' => 'CC-001',
                'type' => 'fisico',
                'description' => 'A Handbook of Agile Software Craftsmanship',
            ],
            [
                'title' => 'Design Patterns',
                'author' => 'Gang of Four',
                'code' => 'DP-002',
                'type' => 'fisico',
                'description' => 'Elements of Reusable Object-Oriented Software',
            ],
            [
                'title' => 'The Pragmatic Programmer',
                'author' => 'Andrew Hunt, David Thomas',
                'code' => 'PP-003',
                'type' => 'fisico',
                'description' => 'Your Journey to Mastery',
            ],
            [
                'title' => 'Refactoring',
                'author' => 'Martin Fowler',
                'code' => 'RF-004',
                'type' => 'fisico',
                'description' => 'Improving the Design of Existing Code',
            ],
            [
                'title' => 'The Art of Computer Programming',
                'author' => 'Donald Knuth',
                'code' => 'ACP-005',
                'type' => 'fisico',
                'description' => 'Fundamental Algorithms',
            ],
        ];

        foreach ($materialesFisicos as $data) {
            $material = Material::create($data);

            // Crear detalles físicos
            MaterialFisico::create([
                'material_id' => $material->id,
                'isbn' => '978-' . rand(1000000000000, 9999999999999),
                'publisher' => 'Prentice Hall',
                'publication_year' => rand(2000, 2024),
                'stock' => rand(3, 10),
                'available' => rand(1, 5),
                'location' => 'Estante ' . chr(65 + rand(0, 5)) . '-' . rand(1, 10),
            ]);
        }

        // Materiales Digitales
        $materialesDigitales = [
            [
                'title' => 'Laravel Documentation',
                'author' => 'Laravel Community',
                'code' => 'LD-006',
                'type' => 'digital',
                'description' => 'Official Laravel Framework Documentation',
                'url' => 'https://media.readthedocs.org/pdf/laravel/latest/laravel.pdf', // PDF Real (ejemplo)
            ],
            [
                'title' => 'PHP: The Right Way',
                'author' => 'Josh Lockhart',
                'code' => 'PHP-007',
                'type' => 'digital',
                'description' => 'A PHP Best Practices Quick Reference',
                'url' => 'https://pubs.opengroup.org/onlinepubs/009695399/download/susv3.pdf', // Placeholder técnico
            ],
            [
                'title' => 'JavaScript: The Definitive Guide',
                'author' => 'David Flanagan',
                'code' => 'JS-008',
                'type' => 'digital',
                'description' => 'Master the World\'s Most-Used Programming Language',
                'url' => 'https://eloquentjavascript.net/Eloquent_JavaScript.pdf', // Eloquent JS PDF
            ],
            [
                'title' => 'You Don\'t Know JS',
                'author' => 'Kyle Simpson',
                'code' => 'YDKJS-009',
                'type' => 'digital',
                'description' => 'A Book Series on JavaScript',
                'url' => 'https://github.com/getify/You-Dont-Know-JS/raw/1st-ed/up%20&%20going/ch1.md', // Enlace directo a contenido (simulado)
            ],
        ];

        foreach ($materialesDigitales as $data) {
            $url = $data['url'];
            unset($data['url']); // Remove URL from main material data

            $material = Material::create($data);

            // Crear detalles digitales
            MaterialDigital::create([
                'material_id' => $material->id,
                'url' => $url,
                'file_type' => 'pdf',
                'license' => 'Creative Commons',
            ]);
        }

        // Materiales Híbridos
        $materialesHibridos = [
            [
                'title' => 'Web Development Masterclass',
                'author' => 'Various Instructors',
                'code' => 'WDM-010',
                'type' => 'hibrido',
                'description' => 'Complete Web Development Course with Materials',
            ],
            [
                'title' => 'Database Design Fundamentals',
                'author' => 'C.J. Date',
                'code' => 'DDF-011',
                'type' => 'hibrido',
                'description' => 'With Digital Supplements',
            ],
        ];

        foreach ($materialesHibridos as $data) {
            $material = Material::create($data);

            // Crear detalles físicos para híbrido
            MaterialFisico::create([
                'material_id' => $material->id,
                'isbn' => '978-' . rand(1000000000000, 9999999999999),
                'publisher' => 'Tech Books',
                'publication_year' => rand(2020, 2024),
                'stock' => rand(2, 5),
                'available' => rand(1, 3),
                'location' => 'Estante H-' . rand(1, 5),
            ]);

            // Crear detalles digitales para híbrido
            MaterialDigital::create([
                'material_id' => $material->id,
                'url' => 'https://example.com/hybrid-' . $material->id,
                'file_type' => 'pdf',
                'license' => 'Commercial',
            ]);
        }
    }
}
