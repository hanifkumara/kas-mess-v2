<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function index(): View
    {
        $admins = Admin::with('roles')->orderBy('name')->get();
        $roles = Role::orderBy('name')->pluck('name');

        return view('admins.index', compact('admins', 'roles'));
    }

    public function create(): View
    {
        $roles = Role::orderBy('name')->get(['id', 'name']);

        return view('admins.form', ['admin' => new Admin, 'roles' => $roles]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateAdmin($request, new Admin);
        $admin = Admin::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);
        $admin->syncRoles($data['role']);

        return to_route('admins.index')
            ->with('toast', ['type' => 'success', 'message' => "Pengguna \"{$admin->name}\" ditambahkan."]);
    }

    public function edit(Admin $admin): View
    {
        $roles = Role::orderBy('name')->get(['id', 'name']);

        return view('admins.form', ['admin' => $admin, 'roles' => $roles]);
    }

    public function update(Request $request, Admin $admin): RedirectResponse
    {
        $data = $this->validateAdmin($request, $admin);

        $admin->name = $data['name'];
        $admin->email = $data['email'];
        $admin->role = $data['role'];
        if (! empty($data['password'])) {
            $admin->password = Hash::make($data['password']);
        }
        $admin->save();
        $admin->syncRoles($data['role']);

        return to_route('admins.index')
            ->with('toast', ['type' => 'success', 'message' => "Pengguna \"{$admin->name}\" diperbarui."]);
    }

    public function destroy(Request $request, Admin $admin): RedirectResponse
    {
        if ($admin->is(auth()->user())) {
            return back()->with('toast', ['type' => 'error', 'message' => 'Tidak bisa menghapus akun sendiri.']);
        }

        $name = $admin->name;
        $admin->delete();

        return to_route('admins.index')
            ->with('toast', ['type' => 'success', 'message' => "Pengguna \"{$name}\" dihapus."]);
    }

    private function validateAdmin(Request $request, Admin $admin): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', Rule::unique('admins', 'email')->ignore($admin->id)],
            'password' => [$admin->exists ? 'nullable' : 'required', 'string', 'min:6', 'max:60'],
            'role' => ['required', Rule::exists('roles', 'name')],
        ]);
    }
}
