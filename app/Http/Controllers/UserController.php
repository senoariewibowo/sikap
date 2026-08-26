<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Gudang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $users = User::with('role', 'gudang')
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
            ->orderBy('name')->paginate(10)->withQueryString();
        return view('users.index', compact('users', 'search'));
    }

    public function create()
    {
        $roles = Role::all();
        $gudangs = Gudang::where('status', 'aktif')->orderBy('nama_gudang')->get();
        return view('users.create', compact('roles', 'gudangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'gudang_id' => 'nullable|required_if:role_id,' . Role::where('nama_role', 'petugas_gudang')->value('id') . '|exists:gudang,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'gudang_id' => $request->gudang_id,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $gudangs = Gudang::where('status', 'aktif')->orderBy('nama_gudang')->get();
        return view('users.edit', compact('user', 'roles', 'gudangs'));
    }

    public function update(Request $request, User $user)
    {
        $petugasGudangRoleId = Role::where('nama_role', 'petugas_gudang')->value('id');

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'gudang_id' => 'nullable|required_if:role_id,' . $petugasGudangRoleId . '|exists:gudang,id',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'gudang_id' => $request->role_id == $petugasGudangRoleId ? $request->gudang_id : null,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
