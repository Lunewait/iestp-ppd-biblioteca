<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Reserva;
use App\Models\User;
use App\Models\Prestamo;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReservationController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of reservations.
     */
    public function index(Request $request)
    {
        $this->authorize('view_reservations');

        // Expire old approved reservations (48 hours passed)
        $expiredReservations = Reserva::where('status', 'aprobada')
            ->where('fecha_expiracion', '<', now())
            ->get();

        foreach ($expiredReservations as $reservation) {
            $reservation->markAsExpired();
        }

        $query = Reserva::query();

        // Filter by status
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by user
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by material
        if ($request->material_id) {
            $query->where('material_id', $request->material_id);
        }

        $reservations = $query->with(['usuario', 'material'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $users = User::all();
        $materials = Material::all();

        return view('reservations.index', compact('reservations', 'users', 'materials'));
    }

    /**
     * Show the form for creating a new reservation.
     */
    public function create()
    {
        $this->authorize('create_reservation');

        $materials = Material::where('type', '!=', 'digital')->get();

        return view('reservations.create', compact('materials'));
    }

    /**
     * Store a newly created reservation in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create_reservation');

        $user = auth()->user();
        $maxLoans = config('library.max_active_loans_per_user', 3);

        // Check if user is blocked from requesting loans
        if ($user->blocked_for_loans) {
            return back()->with('error', 'No puedes solicitar préstamos. Motivo: ' . ($user->blocked_reason ?? 'Cuenta bloqueada. Contacta con la biblioteca.'));
        }

        // Check if user has pending fines
        $pendingFines = $user->multas()->where('status', 'pendiente')->sum('monto');
        if ($pendingFines > 0) {
            return back()->with('error', 'Tienes multas pendientes por S/. ' . number_format($pendingFines, 2) . '. Debes pagarlas antes de solicitar préstamos.');
        }

        // Check loan limit (active loans + pending requests)
        $activeLoansCount = Prestamo::where('user_id', $user->id)
            ->whereIn('approval_status', ['pending', 'approved', 'collected'])
            ->count();

        if ($activeLoansCount >= $maxLoans) {
            return back()->with('error', 'Has alcanzado el máximo de ' . $maxLoans . ' préstamos permitidos. Devuelve algún libro para poder solicitar más.');
        }

        $validated = $request->validate([
            'material_id' => 'required|exists:materials,id',
        ]);

        $material = Material::find($validated['material_id']);

        // Check if material is available
        if (!$material->isAvailable()) {
            return back()->with('error', 'Este material no está disponible actualmente.');
        }

        // Check if user has an active loan for this material
        $activeLoan = Prestamo::where('user_id', $user->id)
            ->where('material_id', $material->id)
            ->whereIn('approval_status', ['pending', 'approved', 'collected'])
            ->first();

        if ($activeLoan) {
            return back()->with('error', 'Ya tienes una solicitud o préstamo activo de este material.');
        }

        // Create the loan request - AUTO APPROVED (no pending fines)
        $loan = Prestamo::create([
            'user_id' => $user->id,
            'material_id' => $material->id,
            'fecha_prestamo' => now(),
            'fecha_limite_recogida' => now()->addDays(2), // 2 días para recoger
            'status' => 'pendiente_recogida',
            'approval_status' => 'approved', // Auto-approved
            'approved_by' => null, // System auto-approval
            'approval_date' => now(),
            'registrado_por' => $user->id,
        ]);

        // Decrease available stock (reserve the book)
        if ($material->materialFisico) {
            $material->materialFisico->decrement('available');
        }

        return redirect()->route('loans.index')
            ->with('success', '¡Solicitud aprobada automáticamente! Tienes 2 días para acercarte a la biblioteca a recoger tu libro.')
            ->with('auto_approved', true);
    }

    /**
     * Display the specified reservation.
     */
    public function show(Reserva $reservation)
    {
        $this->authorize('view_reservations');

        $reservation->load(['usuario', 'material']);

        return view('reservations.show', compact('reservation'));
    }

    /**
     * Show the form for editing the specified reservation.
     */
    public function edit(Reserva $reservation)
    {
        $this->authorize('manage_reservations');

        $materials = Material::where('type', '!=', 'digital')->get();

        return view('reservations.edit', compact('reservation', 'materials'));
    }

    /**
     * Update the specified reservation in storage.
     */
    public function update(Request $request, Reserva $reservation)
    {
        $this->authorize('manage_reservations');

        $validated = $request->validate([
            'status' => 'required|in:pendiente,aprobada,completada,cancelada,expirada',
        ]);

        $reservation->update($validated);

        return redirect()->route('reservations.show', $reservation)
            ->with('success', 'Reserva actualizada exitosamente');
    }

    /**
     * Cancel a reservation.
     */
    public function cancel(Reserva $reservation)
    {
        $this->authorize('manage_reservations');

        $reservation->update(['status' => 'cancelada']);

        return redirect()->route('reservations.show', $reservation)
            ->with('success', 'Reserva cancelada exitosamente');
    }

    /**
     * Approve a reservation and set 48-hour expiration.
     */
    public function approve(Reserva $reservation)
    {
        $this->authorize('manage_reservations');

        // Check if material is available
        if (!$reservation->material->materialFisico || $reservation->material->materialFisico->available < 1) {
            return back()->with('error', 'No hay ejemplares disponibles de este material.');
        }

        // Set expiration to 48 hours from now
        $reservation->update([
            'status' => 'aprobada',
            'fecha_expiracion' => now()->addHours(48),
        ]);

        // Decrement available stock (reserved for this user)
        $reservation->material->materialFisico->decrement('available');

        return redirect()->route('reservations.show', $reservation)
            ->with('success', 'Solicitud aprobada. El estudiante tiene 48 horas para recoger el material.');
    }

    /**
     * Mark a reservation as completed and create the loan.
     */
    public function complete(Reserva $reservation)
    {
        $this->authorize('manage_reservations');

        // Create the loan with 7-day period
        $loan = Prestamo::create([
            'user_id' => $reservation->user_id,
            'material_id' => $reservation->material_id,
            'fecha_prestamo' => now(),
            'fecha_devolucion_esperada' => now()->addDays(7),
            'status' => 'activo',
        ]);

        // Mark reservation as completed
        $reservation->update(['status' => 'completada']);

        // Reorder remaining reservations
        $remainingReservations = Reserva::where('material_id', $reservation->material_id)
            ->where('status', 'pendiente')
            ->orderBy('created_at')
            ->get();

        foreach ($remainingReservations as $index => $res) {
            $res->update(['posicion_cola' => $index + 1]);
        }

        return redirect()->route('reservations.show', $reservation)
            ->with('success', 'Préstamo creado exitosamente. El estudiante tiene 7 días para devolver el material.');
    }

    /**
     * Remove the specified reservation from storage.
     */
    public function destroy(Reserva $reservation)
    {
        $this->authorize('manage_reservations');

        $reservation->delete();

        return redirect()->route('reservations.index')
            ->with('success', 'Reserva eliminada exitosamente');
    }
}
