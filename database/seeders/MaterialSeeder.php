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
     * Solo materiales físicos y digitales (sin híbridos)
     */
    public function run(): void
    {
        // Materiales Físicos (para préstamo)
        $materialesFisicos = [
            [
                'title' => 'Clean Code',
                'author' => 'Robert C. Martin',
                'code' => 'CC-001',
                'type' => 'fisico',
                'description' => 'Manual de desarrollo de software ágil. Este libro es una guía completa para escribir código limpio y mantenible.',
            ],
            [
                'title' => 'Design Patterns',
                'author' => 'Gang of Four',
                'code' => 'DP-002',
                'type' => 'fisico',
                'description' => 'Elementos de software orientado a objetos reutilizable. Patrones de diseño clásicos.',
            ],
            [
                'title' => 'The Pragmatic Programmer',
                'author' => 'Andrew Hunt, David Thomas',
                'code' => 'PP-003',
                'type' => 'fisico',
                'description' => 'Tu viaje hacia la maestría en programación. Consejos prácticos para desarrolladores.',
            ],
            [
                'title' => 'Refactoring',
                'author' => 'Martin Fowler',
                'code' => 'RF-004',
                'type' => 'fisico',
                'description' => 'Mejorando el diseño de código existente. Técnicas de refactorización.',
            ],
            [
                'title' => 'Estructura de Datos y Algoritmos',
                'author' => 'Thomas H. Cormen',
                'code' => 'EDA-005',
                'type' => 'fisico',
                'description' => 'Introducción a los algoritmos. Texto fundamental de ciencias de la computación.',
            ],
            [
                'title' => 'Fundamentos de Bases de Datos',
                'author' => 'Abraham Silberschatz',
                'code' => 'FBD-006',
                'type' => 'fisico',
                'description' => 'Conceptos de sistemas de bases de datos relacionales y NoSQL.',
            ],
            [
                'title' => 'Ingeniería de Software',
                'author' => 'Ian Sommerville',
                'code' => 'IS-007',
                'type' => 'fisico',
                'description' => 'Principios y prácticas de ingeniería de software moderna.',
            ],
        ];

        foreach ($materialesFisicos as $data) {
            // Usar firstOrCreate para evitar duplicados
            $material = Material::firstOrCreate(
                ['code' => $data['code']],
                $data
            );

            // Solo crear detalles si el material es nuevo
            if ($material->wasRecentlyCreated) {
                MaterialFisico::create([
                    'material_id' => $material->id,
                    'isbn' => '978-' . rand(1000000000, 9999999999),
                    'publisher' => 'Editorial Técnica',
                    'publication_year' => rand(2018, 2024),
                    'stock' => 3,
                    'available' => 3,
                    'location' => 'Estante ' . chr(65 + rand(0, 5)) . '-' . rand(1, 10),
                ]);
            }
        }

        // Materiales Digitales (lectura en línea gratuita)
        $materialesDigitales = [
            [
                'title' => 'Eloquent JavaScript',
                'author' => 'Marijn Haverbeke',
                'code' => 'EJS-D01',
                'type' => 'digital',
                'description' => 'Introducción moderna a la programación con JavaScript. Libro completo disponible en línea.',
                'url' => 'https://eloquentjavascript.net/',
            ],
            [
                'title' => 'You Don\'t Know JS Yet',
                'author' => 'Kyle Simpson',
                'code' => 'YDKJS-D02',
                'type' => 'digital',
                'description' => 'Serie de libros que profundizan en los mecanismos del lenguaje JavaScript.',
                'url' => 'https://github.com/getify/You-Dont-Know-JS',
            ],
            [
                'title' => 'Pro Git',
                'author' => 'Scott Chacon',
                'code' => 'GIT-D03',
                'type' => 'digital',
                'description' => 'Todo sobre el control de versiones con Git. Libro oficial y completo.',
                'url' => 'https://git-scm.com/book/es/v2',
            ],
            [
                'title' => 'Think Python',
                'author' => 'Allen B. Downey',
                'code' => 'TP-D04',
                'type' => 'digital',
                'description' => 'Cómo pensar como un programador. Introducción a Python.',
                'url' => 'https://greenteapress.com/thinkpython2/html/index.html',
            ],
            [
                'title' => 'Learn PHP in Y Minutes',
                'author' => 'Comunidad Open Source',
                'code' => 'PHP-D05',
                'type' => 'digital',
                'description' => 'Referencia rápida de PHP para desarrolladores.',
                'url' => 'https://learnxinyminutes.com/docs/php/',
            ],
            [
                'title' => 'MDN Web Docs - HTML',
                'author' => 'Mozilla Foundation',
                'code' => 'HTML-D06',
                'type' => 'digital',
                'description' => 'Documentación completa de HTML, CSS y JavaScript.',
                'url' => 'https://developer.mozilla.org/es/docs/Web/HTML',
            ],
            [
                'title' => 'The Linux Command Line',
                'author' => 'William Shotts',
                'code' => 'LINUX-D07',
                'type' => 'digital',
                'description' => 'Guía completa para usar la línea de comandos de Linux.',
                'url' => 'https://linuxcommand.org/tlcl.php',
            ],
            [
                'title' => 'SQL Tutorial',
                'author' => 'W3Schools',
                'code' => 'SQL-D08',
                'type' => 'digital',
                'description' => 'Tutorial interactivo de SQL para bases de datos.',
                'url' => 'https://www.w3schools.com/sql/',
            ],
        ];

        foreach ($materialesDigitales as $data) {
            $url = $data['url'];
            unset($data['url']);

            // Usar firstOrCreate para evitar duplicados
            $material = Material::firstOrCreate(
                ['code' => $data['code']],
                $data
            );

            // Solo crear detalles si el material es nuevo
            if ($material->wasRecentlyCreated) {
                MaterialDigital::create([
                    'material_id' => $material->id,
                    'url' => $url,
                    'file_type' => 'web',
                    'license' => 'Gratuito',
                    'downloadable' => false,
                    'access_count' => rand(10, 200),
                ]);
            }
        }
    }
}
