<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Límite de Préstamos por Usuario
    |--------------------------------------------------------------------------
    |
    | Este valor determina el número máximo de libros que un estudiante
    | puede tener prestados simultáneamente (incluyendo pendientes de aprobación).
    |
    */

    'max_active_loans_per_user' => env('LIBRARY_MAX_LOANS', 3),

    /*
    |--------------------------------------------------------------------------
    | Días de Préstamo por Defecto
    |--------------------------------------------------------------------------
    |
    | Número de días que un libro puede estar prestado por defecto.
    |
    */

    'default_loan_days' => env('LIBRARY_DEFAULT_LOAN_DAYS', 14),

    /*
    |--------------------------------------------------------------------------
    | Multa Diaria
    |--------------------------------------------------------------------------
    |
    | Monto de la multa por día de retraso en la devolución de un libro.
    |
    */

    'daily_fine_rate' => env('LIBRARY_DAILY_FINE_RATE', 1.50),

];
