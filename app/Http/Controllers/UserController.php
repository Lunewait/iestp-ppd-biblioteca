<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $this->authorize('view_users');

        $query = User::query();

        // Search by name or email
        if ($request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('institutional_email', 'like', "%{$search}%");
        }

        // Filter by role
        if ($request->role) {
            $query->role($request->role);
        }

        $users = $query->with('roles')
            ->paginate(15);

        $roles = Role::all();

        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $this->authorize('create_user');

        $roles = Role::all();

        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create_user');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'institutional_email' => 'required|email|unique:users,institutional_email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'institutional_email' => $validated['institutional_email'],
            'password' => bcrypt($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('users.show', $user)
            ->with('success', 'Usuario creado exitosamente');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $this->authorize('view_users');

        $user->load(['roles', 'prestamos', 'multas', 'reservas']);

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $this->authorize('edit_user');

        $roles = Role::all();

        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('edit_user');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'institutional_email' => 'required|email|unique:users,institutional_email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'role' => 'required|exists:roles,name',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'institutional_email' => $validated['institutional_email'],
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => bcrypt($validated['password'])]);
        }

        // Update role
        $user->syncRoles([$validated['role']]);

        return redirect()->route('users.show', $user)
            ->with('success', 'Usuario actualizado exitosamente');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete_user');

        // Prevent deletion of self
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propio usuario');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuario eliminado exitosamente');
    }

    /**
     * Change user role.
     */
    public function changeRole(Request $request, User $user)
    {
        $this->authorize('manage_roles');

        $validated = $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        $user->syncRoles([$validated['role']]);

        return redirect()->route('users.show', $user)
            ->with('success', 'Rol actualizado exitosamente');
    }

    /**
     * Block user from requesting loans.
     */
    public function block(Request $request, User $user)
    {
        $this->authorize('edit_user');

        $validated = $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $user->blockLoans($validated['reason']);

        return back()->with('success', 'Usuario bloqueado para préstamos exitosamente');
    }

    /**
     * Unblock user to allow loan requests.
     */
    public function unblock(User $user)
    {
        $this->authorize('edit_user');

        $user->unblockLoans();

        return back()->with('success', 'Usuario desbloqueado exitosamente');
    }
}
