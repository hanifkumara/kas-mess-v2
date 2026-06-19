<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::with('permissions')->orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get(['id', 'name']);

        return view('roles.index', compact('roles', 'permissions'));
    }

    public function create(): View
    {
        $permissions = Permission::orderBy('name')->get(['id', 'name']);

        return view('roles.form', ['role' => null, 'permissions' => $permissions, 'selected' => []]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateRole($request);

        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
        $role->syncPermissions($data['permissions'] ?? []);

        return to_route('roles.index')
            ->with('toast', ['type' => 'success', 'message' => "Role \"{$role->name}\" dibuat."]);
    }

    public function edit(Role $role): View
    {
        $permissions = Permission::orderBy('name')->get(['id', 'name']);

        return view('roles.form', [
            'role' => $role,
            'permissions' => $permissions,
            'selected' => $role->permissions->pluck('name')->all(),
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        if (in_array($role->name, ['superadmin'], true)) {
            $data = $request->validate(['name' => ['required', Rule::unique('roles', 'name')->ignore($role->id)]]);
            $role->update(['name' => $data['name']]);

            return to_route('roles.index')
                ->with('toast', ['type' => 'info', 'message' => 'Superadmin tetap memegang semua permission.']);
        }

        $data = $this->validateRole($request, $role);
        $role->update(['name' => $data['name']]);
        $role->syncPermissions($data['permissions'] ?? []);

        return to_route('roles.index')
            ->with('toast', ['type' => 'success', 'message' => "Role \"{$role->name}\" diperbarui."]);
    }

    public function destroy(Role $role): RedirectResponse
    {
        if (in_array($role->name, ['superadmin', 'bendahara', 'anggota'], true)) {
            return back()->with('toast', ['type' => 'error', 'message' => 'Role bawaan tidak boleh dihapus.']);
        }

        $name = $role->name;
        $role->delete();

        return to_route('roles.index')
            ->with('toast', ['type' => 'success', 'message' => "Role \"{$name}\" dihapus."]);
    }

    private function validateRole(Request $request, ?Role $role = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:60', Rule::unique('roles', 'name')->ignore($role?->id)],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::exists('permissions', 'name')],
        ]);
    }
}
