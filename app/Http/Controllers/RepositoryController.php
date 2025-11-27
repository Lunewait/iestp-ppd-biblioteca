<?php

namespace App\Http\Controllers;

use App\Models\Aprobacion;
use App\Models\RepositorioDocumento;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RepositoryController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display repository documents.
     */
    public function index(Request $request)
    {
        $query = RepositorioDocumento::query();

        // All users can see all documents in the repository
        // No restrictions based on status

        // Filter by type
        if ($request->tipo) {
            $query->where('tipo', $request->tipo);
        }

        // Search
        if ($request->search) {
            $search = $request->search;
            $query->where('titulo', 'like', "%{$search}%")
                ->orWhere('autor', 'like', "%{$search}%");
        }

        $documentos = $query->with('usuario')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('repository.index', compact('documentos'));
    }

    /**
     * Show upload form for students.
     */
    public function create()
    {
        // Only Admin, Jefe_Area and workers can submit documents
        if (!auth()->user()->hasAnyRole(['Admin', 'Jefe_Area', 'Trabajador'])) {
            abort(403, 'Solo administradores, jefes de área y trabajadores pueden subir documentos');
        }

        return view('repository.create');
    }

    /**
     * Store uploaded document.
     */
    public function store(Request $request)
    {
        // Only Admin, Jefe_Area and workers can submit documents
        if (!auth()->user()->hasAnyRole(['Admin', 'Jefe_Area', 'Trabajador'])) {
            abort(403, 'Solo administradores, jefes de área y trabajadores pueden subir documentos');
        }

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'autor' => 'required|string|max:255',
            'tipo' => 'required|in:tesis,investigacion,trabajo_final,otro',
            'archivo' => 'required|file|max:20480|mimes:pdf,doc,docx',
        ]);

        $file = $request->file('archivo');
        $filename = now()->timestamp . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('documents/submissions', $filename, 'private');

        $documento = RepositorioDocumento::create([
            'user_id' => auth()->id(),
            'titulo' => $validated['titulo'],
            'descripcion' => $validated['descripcion'],
            'autor' => $validated['autor'],
            'tipo' => $validated['tipo'],
            'file_path' => $path,
            'file_original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'estado' => 'pendiente',
        ]);

        // Create approval records for all area heads
        $jefeAreas = User::role('Jefe_Area')->get();
        foreach ($jefeAreas as $jefe) {
            Aprobacion::create([
                'documento_id' => $documento->id,
                'jefe_area_id' => $jefe->id,
                'estado' => 'pendiente',
            ]);
        }

        return redirect()->route('repository.show', $documento)
            ->with('success', 'Documento enviado para aprobación');
    }

    /**
     * Display the specified document.
     */
    public function show(RepositorioDocumento $documento)
    {
        // All users can view all documents
        $documento->load('usuario', 'aprobaciones.jefeArea');

        return view('repository.show', compact('documento'));
    }

    /**
     * Show approval form for area heads.
     */
    public function approve(RepositorioDocumento $documento)
    {
        $this->authorize('approve_document');

        return view('repository.approve', compact('documento'));
    }

    /**
     * Process document approval.
     */
    public function processApproval(Request $request, RepositorioDocumento $documento)
    {
        $this->authorize('approve_document');

        $validated = $request->validate([
            'estado' => 'required|in:aprobado,rechazado',
            'comentarios' => 'nullable|string',
        ]);

        // Find or create approval record for current user
        $aprobacion = Aprobacion::where('documento_id', $documento->id)
            ->where('jefe_area_id', auth()->id())
            ->first();

        if (!$aprobacion) {
            $aprobacion = Aprobacion::create([
                'documento_id' => $documento->id,
                'jefe_area_id' => auth()->id(),
            ]);
        }

        $aprobacion->update([
            'estado' => $validated['estado'],
            'comentarios' => $validated['comentarios'],
            'fecha_revision' => now(),
        ]);

        // Check if all area heads approved
        $totalAprobaciones = Aprobacion::where('documento_id', $documento->id)->count();
        $aprobacionesPositivas = Aprobacion::where('documento_id', $documento->id)
            ->where('estado', 'aprobado')
            ->count();

        if ($aprobacionesPositivas === $totalAprobaciones) {
            $documento->update([
                'estado' => 'publicado',
                'fecha_aprobacion' => now(),
            ]);

            return redirect()->route('repository.show', $documento)
                ->with('success', 'Documento aprobado y publicado');
        }

        if ($validated['estado'] === 'rechazado') {
            $documento->update(['estado' => 'rechazado']);

            return redirect()->route('repository.show', $documento)
                ->with('error', 'Documento rechazado');
        }

        return redirect()->route('repository.show', $documento)
            ->with('success', 'Aprobación registrada');
    }

    /**
     * Download document.
     */
    public function download(RepositorioDocumento $documento)
    {
        // All users can download all documents
        $documento->increment('descargas');

        return response()->download(storage_path('app/private/' . $documento->file_path));
    }
}
