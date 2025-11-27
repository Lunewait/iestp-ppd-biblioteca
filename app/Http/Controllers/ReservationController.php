<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Reserva;
use App\Models\User;
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

        $query = Reserva::query();

        // Filter by status
        if ($request->status && $request->status !== 'all') {
            $query->where('estado', $request->status);
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

        $validated = $request->validate([
            'material_id' => 'required|exists:materials,id',
            'fecha_reserva_esperada' => 'required|date|after:today',
        ]);

        $material = Material::find($validated['material_id']);

        // Check if user already has a reservation for this material
        $existingReservation = Reserva::where('user_id', auth()->id())
                                       ->where('material_id', $material->id)
                                       ->where('estado', 'activa')
                                       ->first();

        if ($existingReservation) {
            return back()->with('error', 'Ya tienes una reserva activa para este material');
        }

        Reserva::create([
            'user_id' => auth()->id(),
            'material_id' => $material->id,
            'fecha_reserva_esperada' => $validated['fecha_reserva_esperada'],
            'estado' => 'activa',
            'posicion' => Reserva::where('material_id', $material->id)
                                  ->where('estado', 'activa')
                                  ->count() + 1,
        ]);

        return redirect()->route('reservations.index')
                       ->with('success', 'Reserva creada exitosamente');
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
            'fecha_reserva_esperada' => 'required|date|after:today',
            'estado' => 'required|in:activa,completada,cancelada',
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

        $reservation->update(['estado' => 'cancelada']);

        return redirect()->route('reservations.show', $reservation)
                       ->with('success', 'Reserva cancelada exitosamente');
    }

    /**
     * Mark a reservation as completed.
     */
    public function complete(Reserva $reservation)
    {
        $this->authorize('manage_reservations');

        $reservation->update(['estado' => 'completada']);

        // Reorder remaining reservations
        $remainingReservations = Reserva::where('material_id', $reservation->material_id)
                                         ->where('estado', 'activa')
                                         ->orderBy('created_at')
                                         ->get();

        foreach ($remainingReservations as $index => $res) {
            $res->update(['posicion' => $index + 1]);
        }

        return redirect()->route('reservations.show', $reservation)
                       ->with('success', 'Reserva completada exitosamente');
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
