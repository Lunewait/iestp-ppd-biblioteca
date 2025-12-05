<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Prestamo;
use App\Models\Multa;

class CheckStudentRestrictions
{
    /**
     * Rutas permitidas para estudiantes con restricciones
     */
    protected $allowedRoutes = [
        'materials.index',
        'materials.show',
        'repository.index',
        'repository.show',
        'repository.download',
        'fines.index',
        'fines.show',
        'logout',
        'dashboard',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // Solo aplicar a estudiantes
        if (!$user || !$user->hasRole('Estudiante')) {
            return $next($request);
        }

        // Verificar si tiene préstamos vencidos
        $hasOverdueLoans = Prestamo::where('user_id', $user->id)
            ->where('status', 'activo')
            ->where('approval_status', 'collected')
            ->where('fecha_devolucion_esperada', '<', now())
            ->exists();

        // Verificar si tiene multas pendientes
        $hasPendingFines = Multa::where('user_id', $user->id)
            ->where('status', 'pendiente')
            ->exists();

        // Si tiene restricciones
        if ($hasOverdueLoans || $hasPendingFines || $user->blocked_for_loans) {
            $currentRoute = $request->route()->getName();

            // Verificar si la ruta está permitida
            $isAllowed = false;
            foreach ($this->allowedRoutes as $route) {
                if ($currentRoute === $route || str_starts_with($currentRoute, $route)) {
                    $isAllowed = true;
                    break;
                }
            }

            if (!$isAllowed) {
                // Construir mensaje de error apropiado
                $message = '';
                if ($hasOverdueLoans) {
                    $message = '⚠️ Tienes préstamos vencidos. Devuelve los libros para recuperar el acceso completo.';
                } elseif ($hasPendingFines) {
                    $message = '⚠️ Tienes multas pendientes. Acércate a la biblioteca para pagar.';
                } else {
                    $message = '⚠️ Tu cuenta está restringida. Contacta a la biblioteca.';
                }

                return redirect()->route('materials.index')
                    ->with('error', $message);
            }
        }

        return $next($request);
    }
}
