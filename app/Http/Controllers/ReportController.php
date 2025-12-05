<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Prestamo;
use App\Models\Multa;
use App\Models\User;
use App\Models\RepositorioDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Obtiene la función SQL para formatear fecha según el driver de BD
     */
    private function getDateFormatSQL($column, $format)
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            // PostgreSQL usa TO_CHAR
            return "TO_CHAR({$column}, 'YYYY-MM')";
        } else {
            // MySQL usa DATE_FORMAT
            return "DATE_FORMAT({$column}, '%Y-%m')";
        }
    }

    public function index(Request $request)
    {
        // Solo Admin puede ver reportes
        if (!auth()->user()->hasRole('Admin')) {
            abort(403, 'Acceso denegado');
        }

        // Período de filtro (por defecto: último mes)
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : now()->subMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : now();

        // ===== ESTADÍSTICAS GENERALES =====
        $stats = [
            'total_materials' => Material::count(),
            'physical_materials' => Material::where('type', 'fisico')->count(),
            'digital_materials' => Material::where('type', 'digital')->count(),
            'total_users' => User::count(),
            'students' => User::role('Estudiante')->count(),
            'total_loans' => Prestamo::count(),
            'active_loans' => Prestamo::where('status', 'activo')->where('approval_status', 'collected')->count(),
            'overdue_loans' => Prestamo::where('status', 'activo')
                ->where('approval_status', 'collected')
                ->where('fecha_devolucion_esperada', '<', now())
                ->count(),
            'pending_pickups' => Prestamo::where('status', 'pendiente_recogida')->where('approval_status', 'approved')->count(),
            'total_fines' => Multa::sum('monto'),
            'pending_fines' => Multa::where('status', 'pendiente')->sum('monto'),
            'paid_fines' => Multa::where('status', 'pagada')->sum('monto'),
            'repository_documents' => RepositorioDocumento::count(),
            'pending_documents' => RepositorioDocumento::where('estado', 'pendiente')->count(),
        ];

        // ===== PRÉSTAMOS POR MES (últimos 6 meses) - Compatible MySQL/PostgreSQL =====
        $dateFormatSQL = $this->getDateFormatSQL('created_at', 'YYYY-MM');

        $loansByMonth = Prestamo::select(
            DB::raw("{$dateFormatSQL} as month"),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        // Llenar meses vacíos
        $monthsData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $monthsData[$month] = $loansByMonth[$month] ?? 0;
        }

        // ===== MATERIALES MÁS PRESTADOS (usando colecciones) =====
        $loanCounts = Prestamo::select('material_id', DB::raw('COUNT(*) as loan_count'))
            ->groupBy('material_id')
            ->pluck('loan_count', 'material_id')
            ->toArray();

        $topMaterials = Material::all()->map(function ($material) use ($loanCounts) {
            $material->loan_count = $loanCounts[$material->id] ?? 0;
            return $material;
        })->sortByDesc('loan_count')->take(10)->values();

        // ===== USUARIOS CON MÁS PRÉSTAMOS (usando colecciones) =====
        $userLoanCounts = Prestamo::select('user_id', DB::raw('COUNT(*) as loan_count'))
            ->groupBy('user_id')
            ->pluck('loan_count', 'user_id')
            ->toArray();

        $topUsers = User::role('Estudiante')->get()->map(function ($user) use ($userLoanCounts) {
            $user->loan_count = $userLoanCounts[$user->id] ?? 0;
            return $user;
        })->sortByDesc('loan_count')->take(10)->values();

        // ===== PRÉSTAMOS EN EL PERÍODO =====
        $loansInPeriod = Prestamo::whereBetween('created_at', [$startDate, $endDate])->count();
        $returnsInPeriod = Prestamo::where('status', 'devuelto')
            ->whereBetween('fecha_devolucion_actual', [$startDate, $endDate])
            ->count();

        // ===== DISTRIBUCIÓN DE ESTADOS DE PRÉSTAMOS =====
        $loanStatus = [
            'activos' => Prestamo::where('status', 'activo')->where('approval_status', 'collected')->count(),
            'pendiente_recogida' => Prestamo::where('status', 'pendiente_recogida')->count(),
            'devueltos' => Prestamo::where('status', 'devuelto')->count(),
            'vencidos' => Prestamo::where('status', 'activo')
                ->where('fecha_devolucion_esperada', '<', now())
                ->count(),
            'cancelados' => Prestamo::where('status', 'cancelado')->count(),
        ];

        // ===== MULTAS POR MES - Compatible MySQL/PostgreSQL =====
        $finesByMonth = Multa::select(
            DB::raw("{$dateFormatSQL} as month"),
            DB::raw('SUM(monto) as total')
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        $finesData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $finesData[$month] = floatval($finesByMonth[$month] ?? 0);
        }

        // ===== USUARIOS BLOQUEADOS =====
        $blockedUsers = User::where('blocked_for_loans', true)->count();

        return view('reports.index', compact(
            'stats',
            'monthsData',
            'topMaterials',
            'topUsers',
            'loansInPeriod',
            'returnsInPeriod',
            'loanStatus',
            'finesData',
            'blockedUsers',
            'startDate',
            'endDate'
        ));
    }
}
