<?php

namespace App\Http\Controllers;

use App\Models\Multa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FineController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of fines.
     */
    public function index(Request $request)
    {
        $this->authorize('view_fines');

        $query = Multa::query();

        // Students only see their own fines
        if (auth()->user()?->hasRole('Estudiante')) {
            $query->where('user_id', auth()->id());
        }

        // Filter by status
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by user (only admin/workers)
        if ($request->user_id && !auth()->user()?->hasRole('Estudiante')) {
            $query->where('user_id', $request->user_id);
        }

        // Search by reason or notes
        if ($request->search) {
            $query->where('razon', 'like', "%{$request->search}%")
                  ->orWhereHas('usuario', function ($q) {
                      $q->where('name', 'like', "%{$request->search}%");
                  });
        }

        $fines = $query->with(['usuario', 'prestamo'])
                      ->orderBy('created_at', 'desc')
                      ->paginate(15);

        $users = User::all();
        $totalPending = Multa::where('status', 'pendiente')->sum('monto');

        return view('fines.index', compact('fines', 'users', 'totalPending'));
    }

    /**
     * Show the form for creating a new fine.
     */
    public function create()
    {
        $this->authorize('create_fine');

        $users = User::all();

        return view('fines.create', compact('users'));
    }

    /**
     * Store a newly created fine in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create_fine');

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'prestamo_id' => 'nullable|exists:prestamos,id',
            'monto' => 'required|numeric|min:0.01',
            'razon' => 'required|string|max:255',
        ]);

        $validated['status'] = 'pendiente';
        $validated['registrado_por'] = auth()->id();

        Multa::create($validated);

        return redirect()->route('fines.index')
                       ->with('success', 'Multa creada exitosamente');
    }

    /**
     * Display the specified fine.
     */
    public function show(Multa $fine)
    {
        $this->authorize('view_fines');

        $fine->load(['usuario', 'prestamo']);

        return view('fines.show', compact('fine'));
    }

    /**
     * Show the form for editing the specified fine.
     */
    public function edit(Multa $fine)
    {
        $this->authorize('manage_fines');

        $users = User::all();

        return view('fines.edit', compact('fine', 'users'));
    }

    /**
     * Update the specified fine in storage.
     */
    public function update(Request $request, Multa $fine)
    {
        $this->authorize('manage_fines');

        $validated = $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'razon' => 'required|string|max:255',
            'status' => 'required|in:pendiente,pagada,condonada',
        ]);

        $fine->update($validated);

        return redirect()->route('fines.show', $fine)
                       ->with('success', 'Multa actualizada exitosamente');
    }

    /**
     * Mark a fine as paid.
     */
    public function markAsPaid(Multa $fine)
    {
        $this->authorize('manage_fines');

        $fine->update([
            'status' => 'pagada',
            'fecha_pago' => now(),
        ]);

        return redirect()->route('fines.show', $fine)
                       ->with('success', 'Multa marcada como pagada');
    }

    /**
     * Forgive a fine.
     */
    public function forgive(Multa $fine)
    {
        $this->authorize('forgive_fine');

        $fine->update([
            'status' => 'condonada',
            'fecha_condonacion' => now(),
        ]);

        return redirect()->route('fines.show', $fine)
                       ->with('success', 'Multa condonada exitosamente');
    }

    /**
     * Remove the specified fine from storage.
     */
    public function destroy(Multa $fine)
    {
        $this->authorize('manage_fines');

        $fine->delete();

        return redirect()->route('fines.index')
                       ->with('success', 'Multa eliminada exitosamente');
    }
}
