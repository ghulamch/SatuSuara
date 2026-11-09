<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of users with roles and dynamic role overview.
     */
    public function index(Request $request): View
    {
        $search = trim($request->input('search', ''));
        $roleFilter = $request->input('role');

        // Khusus akun staf / panitia / admin / petugas (mengecualikan voters agar tidak menumpuk)
        $query = User::with('roles')
            ->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'voters');
            })
            ->orderBy('created_at', 'desc');

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (! empty($roleFilter)) {
            $query->whereHas('roles', function ($q) use ($roleFilter) {
                $q->where('name', $roleFilter);
            });
        }

        $users = $query->paginate(15)->withQueryString();

        $allRoles = Role::with('permissions')->withCount('users')->orderBy('name')->get();
        $staffRoles = Role::where('name', '!=', 'voters')->orderBy('name')->get();

        $totalStaff = User::whereDoesntHave('roles', function ($q) {
            $q->where('name', 'voters');
        })->count();

        $adminCount = User::role('admin')->count();
        $petugasCount = User::role('petugas_presensi')->count();
        $votersCount = User::role('voters')->count();

        return view('admin.users.index', compact(
            'users',
            'allRoles',
            'staffRoles',
            'totalStaff',
            'adminCount',
            'petugasCount',
            'votersCount'
        ));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        $rolesQuery = Role::where('name', '!=', 'voters');

        // Cegah non-admin memilih role admin saat membuat user baru
        if (! auth()->user()->hasRole('admin')) {
            $rolesQuery->where('name', '!=', 'admin');
        }

        $roles = $rolesQuery->orderBy('name')->get();

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::min(6), 'confirmed'],
            'role'     => ['required', 'string', 'exists:roles,name'],
        ], [
            'name.required'      => 'Nama pengguna wajib diisi.',
            'email.required'     => 'Alamat email wajib diisi.',
            'email.unique'       => 'Alamat email ini sudah terdaftar.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required'      => 'Peran (role) pengguna wajib dipilih.',
            'role.exists'        => 'Peran yang dipilih tidak valid.',
        ]);

        // Proteksi Eskalasi Hak Akses (Privilege Escalation): Hanya admin yang boleh menetapkan role admin
        if ($validated['role'] === 'admin' && ! auth()->user()->hasRole('admin')) {
            return back()->withInput()->with('error', 'Hanya Administrator yang memiliki wewenang untuk menetapkan peran Superadmin (admin).');
        }

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->syncRoles([$validated['role']]);

        AuditLogger::log('user', "Menambahkan akun pengguna baru: {$user->name} ({$user->email}) dengan peran [{$validated['role']}]", [
            'created_user_id' => $user->id,
            'role'            => $validated['role'],
        ]);

        return redirect()->route('admin.users.index')
            ->with('status', "Akun pengguna '{$user->name}' berhasil ditambahkan dengan peran {$validated['role']}.");
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View|RedirectResponse
    {
        // Non-admin tidak boleh membuka form edit akun admin
        if ($user->hasRole('admin') && ! auth()->user()->hasRole('admin')) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Anda tidak memiliki wewenang untuk mengubah data akun Administrator.');
        }

        $rolesQuery = Role::where('name', '!=', 'voters');

        // Non-admin tidak boleh memilih opsi role admin
        if (! auth()->user()->hasRole('admin')) {
            $rolesQuery->where('name', '!=', 'admin');
        }

        $roles = $rolesQuery->orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        // Proteksi 1: Non-admin tidak boleh mengedit akun admin
        if ($user->hasRole('admin') && ! auth()->user()->hasRole('admin')) {
            return back()->withInput()->with('error', 'Anda tidak memiliki wewenang untuk mengubah data akun Administrator.');
        }

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', Password::min(6), 'confirmed'],
            'role'     => ['required', 'string', 'exists:roles,name'],
        ], [
            'name.required'      => 'Nama pengguna wajib diisi.',
            'email.required'     => 'Alamat email wajib diisi.',
            'email.unique'       => 'Alamat email ini sudah digunakan oleh akun lain.',
            'password.min'       => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'role.required'      => 'Peran (role) pengguna wajib dipilih.',
            'role.exists'        => 'Peran yang dipilih tidak valid.',
        ]);

        // Proteksi 2: Non-admin tidak boleh mempromosikan akun ke role admin
        if ($validated['role'] === 'admin' && ! auth()->user()->hasRole('admin')) {
            return back()->withInput()->with('error', 'Hanya Administrator yang memiliki wewenang untuk menetapkan peran Superadmin (admin).');
        }

        // Cegah mencopot role admin jika hanya tersisa 1 admin di sistem
        if ($user->hasRole('admin') && $validated['role'] !== 'admin') {
            $totalAdmins = User::role('admin')->count();
            if ($totalAdmins <= 1) {
                return back()->withInput()->with('error', 'Tidak dapat mengubah peran admin terakhir. Sistem harus memiliki minimal satu Administrator.');
            }
        }

        $updateData = [
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);
        $user->syncRoles([$validated['role']]);

        AuditLogger::log('user', "Memperbarui akun pengguna: {$user->name} ({$user->email}) menjadi peran [{$validated['role']}]", [
            'updated_user_id'  => $user->id,
            'role'             => $validated['role'],
            'password_changed' => ! empty($validated['password']),
        ]);

        return redirect()->route('admin.users.index')
            ->with('status', "Data akun pengguna '{$user->name}' berhasil diperbarui.");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri saat sedang login.');
        }

        // Proteksi: Non-admin tidak boleh menghapus akun Administrator
        if ($user->hasRole('admin') && ! auth()->user()->hasRole('admin')) {
            return back()->with('error', 'Anda tidak memiliki wewenang untuk menghapus akun Administrator.');
        }

        if ($user->hasRole('admin')) {
            $totalAdmins = User::role('admin')->count();
            if ($totalAdmins <= 1) {
                return back()->with('error', 'Tidak dapat menghapus administrator terakhir dalam sistem.');
            }
        }

        // Jika user memiliki vote yang tercatat, hapus transaksi suaranya
        if ($user->vote) {
            $user->vote->delete();
        }

        $userName = $user->name;
        $userEmail = $user->email;
        $userId = $user->id;

        $user->delete();

        AuditLogger::log('user', "Menghapus akun pengguna: {$userName} ({$userEmail})", [
            'deleted_user_id' => $userId,
        ]);

        return redirect()->route('admin.users.index')
            ->with('status', "Akun pengguna '{$userName}' ({$userEmail}) telah berhasil dihapus.");
    }
}

