<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Multa;
use App\Models\Prestamo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LoansExport;

class LoanController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of loans.
     */
    public function index(Request $request)
    {
        $query = Prestamo::query();

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by user if worker
        if (auth()->user()->hasRole('Trabajador')) {
            $query->orderBy('created_at', 'desc');
        }

        $loans = $query->with(['usuario', 'material', 'registradoPor'])
            ->paginate(15);

        return view('loans.index', compact('loans'));
    }

    /**
     * Show the form for creating a new loan.
     */
    public function create()
    {
        $this->authorize('create_loan');

        $users = User::role('Estudiante')->get();
        $materials = Material::where('type', '!=', 'digital')->get();

        return view('loans.create', compact('users', 'materials'));
    }

    /**
     * Store a newly created loan in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create_loan');

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'material_id' => 'required|exists:materials,id',
            'fecha_devolucion_esperada' => 'required|date|after:today',
        ]);

        // Check if user is blocked
        $user = User::find($validated['user_id']);
        if ($user->blocked_for_loans) {
            return back()->with('error', 'El usuario está bloqueado para préstamos. Motivo: ' . ($user->blocked_reason ?? 'Cuenta bloqueada'));
        }

        $material = Material::find($validated['material_id']);

        // Check if material is available (tiene stock)
        if (!$material->isAvailable()) {
            return back()->with('error', 'Material no disponible para préstamo (sin stock)');
        }

        // Verificar límite de 3 solicitudes activas por usuario
        $activeLoanCount = Prestamo::getActiveRequestsCount($validated['user_id']);
        $maxLoans = config('library.max_active_loans_per_user', 3);

        if ($activeLoanCount >= $maxLoans) {
            return back()->with('error', "El usuario alcanzó el límite permitido de solicitudes (máximo {$maxLoans})");
        }

        // Verificar que EL USUARIO no tenga ya este material prestado
        $existingLoan = Prestamo::where('user_id', $validated['user_id'])
            ->where('material_id', $validated['material_id'])
            ->where('status', 'activo')
            ->whereIn('approval_status', ['pending', 'approved', 'collected'])
            ->exists();

        if ($existingLoan) {
            return back()->with('error', 'Este usuario ya tiene un préstamo activo de este material');
        }

        // Check if user has unpaid fines
        $unpaidFines = Multa::where('user_id', $validated['user_id'])
            ->where('status', 'pendiente')
            ->sum('monto');

        if ($unpaidFines > 0) {
            // Auto-block user if they have fines
            if (!$user->blocked_for_loans) {
                $user->blockLoans("Multas pendientes por S/. {$unpaidFines}");
            }
            return back()->with('error', "Usuario tiene multas pendientes por S/. {$unpaidFines} y ha sido bloqueado.");
        }

        // Cuando el admin crea el préstamo directamente, se marca como entregado
        $loan = Prestamo::create([
            'user_id' => $validated['user_id'],
            'material_id' => $validated['material_id'],
            'fecha_prestamo' => now(),
            'fecha_recogida' => now(), // Ya se entregó
            'fecha_devolucion_esperada' => $validated['fecha_devolucion_esperada'],
            'status' => 'activo',
            'approval_status' => 'collected', // Ya está en manos del estudiante
            'registrado_por' => auth()->id(),
        ]);

        // Decrease available stock if physical material
        if ($material->materialFisico) {
            $material->materialFisico->decrement('available');
        }

        return redirect()->route('loan-approvals.index')
            ->with('success', 'Préstamo asignado exitosamente a ' . $user->name);
    }

    /**
     * Display the specified loan.
     */
    public function show(Prestamo $loan)
    {
        $loan->load(['usuario', 'material', 'multas']);

        return view('loans.show', compact('loan'));
    }

    /**
     * Show the form for returning a loan.
     */
    public function returnForm(Prestamo $loan)
    {
        $this->authorize('return_loan');

        return view('loans.return', compact('loan'));
    }

    /**
     * Return a loan.
     */
    public function return(Request $request, Prestamo $loan)
    {
        $this->authorize('return_loan');

        if ($loan->status !== 'activo') {
            return back()->with('error', 'Este préstamo no está activo');
        }

        $loan->update([
            'fecha_devolucion_actual' => now(),
            'status' => 'devuelto',
        ]);

        // Increase available stock if physical material
        if ($loan->material->materialFisico) {
            $loan->material->materialFisico->increment('available');
        }

        // Check if overdue and create fine
        if ($loan->isOverdue()) {
            $daysLate = now()->diffInDays($loan->fecha_devolucion_esperada);
            $fineAmount = $daysLate * 1.50; // $1.50 per day

            Multa::create([
                'prestamo_id' => $loan->id,
                'user_id' => $loan->user_id,
                'monto' => $fineAmount,
                'razon' => "Devolución tardía ({$daysLate} días)",
                'status' => 'pendiente',
                'registrado_por' => auth()->id(),
            ]);

            // Auto-block user due to fine
            $user = User::find($loan->user_id);
            $user->blockLoans("Multa generada por devolución tardía ({$daysLate} días de retraso)");
        }

        return redirect()->route('loans.show', $loan)
            ->with('success', 'Préstamo devuelto exitosamente');
    }

    /**
     * Export loans to Excel.
     */
    public function export()
    {
        $this->authorize('export_loans');

        return Excel::download(new LoansExport, 'prestamos_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Deliver a book to the student (Admin action).
     * This starts the 7-day loan period.
     */
    public function deliver(Prestamo $loan)
    {
        $this->authorize('manage_loans');

        // Verify the loan is approved and waiting for collection
        if ($loan->approval_status !== 'approved' || $loan->status !== 'pendiente_recogida') {
            return back()->with('error', 'Este préstamo no está pendiente de entrega.');
        }

        $loanDays = config('library.default_loan_days', 7);

        $loan->update([
            'approval_status' => 'collected',
            'status' => 'activo',
            'fecha_recogida' => now(),
            'fecha_devolucion_esperada' => now()->addDays($loanDays),
        ]);

        return back()->with('success', '¡Libro entregado! El préstamo de ' . $loanDays . ' días ha comenzado.');
    }

    /**
     * Receive a returned book from the student (Admin action).
     * Generates fine automatically if overdue.
     */
    public function receive(Prestamo $loan)
    {
        $this->authorize('manage_loans');

        // Verify the loan is active
        if ($loan->status !== 'activo' || $loan->approval_status !== 'collected') {
            return back()->with('error', 'Este préstamo no está activo para recibir devolución.');
        }

        $loan->update([
            'fecha_devolucion_actual' => now(),
            'status' => 'devuelto',
            'approval_status' => 'returned',
        ]);

        // Increase available stock
        if ($loan->material->materialFisico) {
            $loan->material->materialFisico->increment('available');
        }

        // Check if overdue and create fine automatically
        if ($loan->fecha_devolucion_esperada < now()) {
            $daysLate = now()->diffInDays($loan->fecha_devolucion_esperada);
            $fineRate = config('library.daily_fine_rate', 1.50);
            $fineAmount = $daysLate * $fineRate;

            Multa::create([
                'prestamo_id' => $loan->id,
                'user_id' => $loan->user_id,
                'monto' => $fineAmount,
                'razon' => "Devolución tardía ({$daysLate} días de retraso)",
                'status' => 'pendiente',
                'registrado_por' => auth()->id(),
            ]);

            // Auto-block user due to fine
            $user = User::find($loan->user_id);
            $user->blockLoans("Multa generada por devolución tardía de S/. " . number_format($fineAmount, 2));

            return back()->with('warning', 'Libro recibido. Se generó una multa de S/. ' . number_format($fineAmount, 2) . ' por ' . $daysLate . ' días de retraso. El usuario ha sido bloqueado.');
        }

        return back()->with('success', '¡Libro recibido exitosamente! Devolución a tiempo.');
    }
}
