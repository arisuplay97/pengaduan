<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kecamatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    /**
     * Display user management page with filters
     */
    public function index(Request $request)
    {
        // Only superadmin can access
        if (auth()->user()->role !== 'superadmin') {
            abort(403, 'Akses ditolak.');
        }

        $query = User::query();

        // Filter by role
        if ($request->filled('role') && in_array($request->role, ['superadmin', 'admin', 'direksi', 'petugas'])) {
            $query->where('role', $request->role);
        }

        // Search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('username', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $users = $query->orderByRaw("FIELD(role, 'superadmin', 'direksi', 'admin', 'petugas')")
            ->orderBy('name')
            ->paginate(20);

        $counts = [
            'total'     => User::count(),
            'superadmin'=> User::where('role', 'superadmin')->count(),
            'admin'     => User::where('role', 'admin')->count(),
            'direksi'   => User::where('role', 'direksi')->count(),
            'petugas'   => User::where('role', 'petugas')->count(),
        ];

        $kecamatans = Kecamatan::orderBy('nama')->get();

        return view('pages.user-management', compact('users', 'counts', 'kecamatans'));
    }

    /**
     * Store a new user
     */
    public function store(Request $request)
    {
        if (auth()->user()->role !== 'superadmin') {
            abort(403);
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:users,username',
            'email'    => 'nullable|email|max:255|unique:users,email',
            'phone'    => 'nullable|string|max:20',
            'role'     => 'required|in:superadmin,admin,direksi,petugas',
            'password' => 'required|string|min:6|max:100',
            'telegram_chat_id' => 'nullable|string|max:50',
            'kecamatan_ids'    => 'nullable|array',
            'kecamatan_ids.*'  => 'exists:kecamatans,id',
        ], [
            'username.unique' => 'Username sudah digunakan.',
            'email.unique'    => 'Email sudah digunakan.',
            'password.min'    => 'Password minimal 6 karakter.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'role'     => $request->role,
            'password' => $request->password, // auto-hashed via model cast
            'telegram_chat_id' => $request->telegram_chat_id,
        ]);

        if ($request->role === 'petugas' && $request->filled('kecamatan_ids')) {
            $user->kecamatans()->attach($request->kecamatan_ids);
        }

        return back()->with('success', "Pengguna \"{$request->name}\" berhasil ditambahkan! 🎉");
    }

    /**
     * Update user details (no password)
     */
    public function update(Request $request, $id)
    {
        if (auth()->user()->role !== 'superadmin') {
            abort(403);
        }

        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => ['required', 'string', 'max:100', Rule::unique('users')->ignore($user->id)],
            'email'    => ['nullable', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone'    => 'nullable|string|max:20',
            'role'     => 'required|in:superadmin,admin,direksi,petugas',
            'telegram_chat_id' => 'nullable|string|max:50',
            'kecamatan_ids'    => 'nullable|array',
            'kecamatan_ids.*'  => 'exists:kecamatans,id',
        ], [
            'username.unique' => 'Username sudah digunakan.',
            'email.unique'    => 'Email sudah digunakan.',
        ]);

        $user->update([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'role'     => $request->role,
            'telegram_chat_id' => $request->telegram_chat_id,
        ]);

        if ($request->role === 'petugas') {
            $user->kecamatans()->sync($request->kecamatan_ids ?? []);
        } else {
            $user->kecamatans()->detach();
        }

        return back()->with('success', "Data pengguna \"{$user->name}\" berhasil diperbarui.");
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, $id)
    {
        if (auth()->user()->role !== 'superadmin') {
            abort(403);
        }

        $user = User::findOrFail($id);

        $request->validate([
            'new_password' => 'required|string|min:6|max:100',
        ]);

        $user->update([
            'password' => $request->new_password,
        ]);

        return back()->with('success', "Password \"{$user->name}\" berhasil direset. 🔑");
    }

    /**
     * Delete user
     */
    public function destroy($id)
    {
        if (auth()->user()->role !== 'superadmin') {
            abort(403);
        }

        $user = User::findOrFail($id);

        // Cannot delete yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri!');
        }

        $name = $user->name;
        $user->delete();

        return back()->with('success', "Pengguna \"{$name}\" berhasil dihapus.");
    }
}
