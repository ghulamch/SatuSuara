<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AuditLogger;
use App\Support\PermissionModule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    /**
     * Store a newly created dynamic role in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:50', 'regex:/^[a-zA-Z0-9_\-\s]+$/'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ], [
            'name.required' => 'Nama peran (role) wajib diisi.',
            'name.regex'    => 'Nama peran hanya boleh berisi huruf, angka, spasi, tanda strip (-), atau garis bawah (_).',
        ]);

        $roleSlug = Str::slug($validated['name'], '_');

        if (empty($roleSlug)) {
            return back()->with('error', 'Format nama peran tidak valid.');
        }

        if (Role::where('name', $roleSlug)->where('guard_name', 'web')->exists()) {
            return back()->with('error', "Peran '{$roleSlug}' sudah terdaftar dalam sistem.");
        }

        $role = Role::create([
            'name'       => $roleSlug,
            'guard_name' => 'web',
        ]);

        if (! empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
        }

        AuditLogger::log('role', "Menambahkan peran dinamis baru: [{$role->name}]", [
            'role_id'           => $role->id,
            'role_name'         => $role->name,
            'permissions_count' => count($validated['permissions'] ?? []),
        ]);

        return redirect()->route('admin.roles.permissions', $role->id)
            ->with('status', "Peran dinamis '{$role->name}' berhasil dibuat. Silakan atur matriks hak akses per modul.");
    }

    /**
     * Show the granular module permission matrix for the specified role.
     */
    public function permissions(Role $role): View
    {
        $modules = PermissionModule::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        $totalPermissionsCount = Permission::count();

        return view('admin.roles.permissions', compact('role', 'modules', 'rolePermissions', 'totalPermissionsCount'));
    }

    /**
     * Update the permissions assigned to the specified role.
     */
    public function updatePermissions(Request $request, Role $role): RedirectResponse
    {
        if ($role->name === 'voters') {
            return back()->with('error', 'Peran pemilih (voters) dikhususkan untuk bilik suara dan tidak memerlukan izin panel.');
        }

        $validated = $request->validate([
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $selectedPermissions = $validated['permissions'] ?? [];

        $role->syncPermissions($selectedPermissions);
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        AuditLogger::log('role', "Memperbarui matriks hak akses (permissions) peran [{$role->name}] (" . count($selectedPermissions) . ' izin aktif)', [
            'role_id'           => $role->id,
            'role_name'         => $role->name,
            'permissions_count' => count($selectedPermissions),
        ]);

        return redirect()->route('admin.users.index', ['tab' => 'roles'])
            ->with('status', "Matriks hak akses (RBAC) untuk peran '{$role->name}' berhasil diperbarui (" . count($selectedPermissions) . ' izin aktif).');
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role): RedirectResponse
    {
        $coreRoles = ['admin', 'voters', 'petugas_presensi'];

        if (in_array(strtolower($role->name), $coreRoles, true)) {
            return back()->with('error', "Peran sistem inti [{$role->name}] dilindungi dan tidak dapat dihapus.");
        }

        $assignedUsersCount = $role->users()->count();
        if ($assignedUsersCount > 0) {
            return back()->with('error', "Peran [{$role->name}] tidak dapat dihapus karena masih digunakan oleh {$assignedUsersCount} akun pengguna.");
        }

        $roleName = $role->name;
        $roleId = $role->id;

        $role->delete();

        AuditLogger::log('role', "Menghapus peran dinamis: [{$roleName}]", [
            'role_id'   => $roleId,
            'role_name' => $roleName,
        ]);

        return redirect()->route('admin.users.index', ['tab' => 'roles'])
            ->with('status', "Peran dinamis '{$roleName}' berhasil dihapus dari sistem.");
    }
}
