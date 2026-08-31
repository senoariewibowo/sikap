<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Kandang;
use App\Models\User;
use App\Models\Role;
use App\Models\Gudang;
use App\Http\Requests\StoreKaryawanRequest;
use App\Http\Requests\UpdateKaryawanRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $karyawans = Karyawan::with('user')
            ->when($search, fn($q) => $q->where('nama', 'like', "%{$search}%")
                ->orWhere('nik', 'like', "%{$search}%")
                ->orWhere('jabatan', 'like', "%{$search}%"))
            ->orderBy('nama')->paginate(10)->withQueryString();
        return view('karyawan.index', compact('karyawans', 'search'));
    }

    public function create()
    {
        $roles = Role::all();
        $gudangs = Gudang::where('status', 'aktif')->orderBy('nama_gudang')->get();
        return view('karyawan.create', compact('roles', 'gudangs'));
    }

    public function store(StoreKaryawanRequest $request)
    {
        $data = $request->validated();
        unset($data['buat_akun'], $data['email'], $data['password'], $data['role_id']);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('karyawan', 'public');
        }

        $karyawan = Karyawan::create($data);

        if ($request->buat_akun) {
            $user = User::create([
                'name' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'karyawan_id' => $karyawan->id,
                'gudang_id' => $request->gudang_id,
            ]);
            $msg = 'Data karyawan berhasil ditambahkan + akun login dibuat (' . $user->email . ').';
        } else {
            $msg = 'Data karyawan berhasil ditambahkan.';
        }

        return redirect()->route('karyawan.index')->with('success', $msg);
    }

    public function show(Karyawan $karyawan)
    {
        $karyawan->load(['kandang' => function ($query) {
            $query->orderBy('tanggal_mulai', 'desc');
        }, 'gudang' => function ($query) {
            $query->orderBy('tanggal_mulai', 'desc');
        }, 'user.gudang']);
        $kandangs = Kandang::where('status', 'aktif')->orderBy('nama_kandang')->get();
        $gudangs = Gudang::where('status', 'aktif')->orderBy('nama_gudang')->get();
        $riwayatKandang = $karyawan->kandang()->orderBy('tanggal_mulai', 'desc')->get();
        $riwayatGudang = $karyawan->gudang()->orderBy('tanggal_mulai', 'desc')->get();

        return view('karyawan.show', compact('karyawan', 'kandangs', 'gudangs', 'riwayatKandang', 'riwayatGudang'));
    }

    public function edit(Karyawan $karyawan)
    {
        $karyawan->load('user');
        $roles = Role::all();
        $gudangs = Gudang::where('status', 'aktif')->orderBy('nama_gudang')->get();
        return view('karyawan.edit', compact('karyawan', 'roles', 'gudangs'));
    }

    public function update(UpdateKaryawanRequest $request, Karyawan $karyawan)
{
    $data = $request->validated();
    unset(
        $data['reset_password'],
        $data['password'],
        $data['password_confirmation'],
        $data['gudang_id'],
        $data['buat_akun'],
        $data['email'],
        $data['role_id'],
    );

    if ($request->hasFile('foto')) {
        if ($karyawan->foto) {
            Storage::disk('public')->delete($karyawan->foto);
        }
        $data['foto'] = $request->file('foto')->store('karyawan', 'public');
    }

    $karyawan->update($data);

    $msg = 'Data karyawan berhasil diperbarui.';

    if (!$karyawan->user && $request->buat_akun) {
        // Karyawan belum punya akun -> buat baru
        $user = User::create([
            'name' => $karyawan->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'karyawan_id' => $karyawan->id,
            'gudang_id' => $request->gudang_id,
        ]);
        $msg = 'Data karyawan berhasil diperbarui + akun login dibuat (' . $user->email . ').';
    } elseif ($karyawan->user) {
        // Karyawan sudah punya akun -> update seperlunya
        if ($request->reset_password && $request->filled('password')) {
            $karyawan->user->update(['password' => Hash::make($request->password)]);
            $msg = 'Data karyawan + password akun berhasil diperbarui.';
        }
    }

    return redirect()->route('karyawan.index')->with('success', $msg);
}

    public function destroy(Karyawan $karyawan)
    {
        $karyawan->delete();

        return redirect()->route('karyawan.index')
            ->with('success', 'Data karyawan berhasil dinonaktifkan.');
    }

    public function assignKandang(Request $request, Karyawan $karyawan)
    {
        $request->validate([
            'kandang_id' => 'required|exists:kandang,id',
            'tanggal_mulai' => 'required|date',
        ]);

        $kandang = Kandang::findOrFail($request->kandang_id);

        $alreadyActive = $karyawan->kandang()
            ->wherePivot('kandang_id', $kandang->id)
            ->wherePivot('is_active', true)
            ->exists();

        if ($alreadyActive) {
            return back()->with('error', 'Karyawan sudah ditugaskan di kandang ini.');
        }

        $karyawan->kandang()->attach($kandang->id, [
            'tanggal_mulai' => $request->tanggal_mulai,
            'is_active' => true,
        ]);

        return back()->with('success', 'Karyawan berhasil ditugaskan ke kandang ' . $kandang->nama_kandang . '.');
    }

    public function assignGudang(Request $request, Karyawan $karyawan)
    {
        $request->validate([
            'gudang_id' => 'required|exists:gudang,id',
            'tanggal_mulai' => 'required|date',
        ]);

        $gudang = Gudang::findOrFail($request->gudang_id);

        $alreadyActive = $karyawan->gudang()
            ->wherePivot('gudang_id', $gudang->id)
            ->wherePivot('is_active', true)
            ->exists();

        if ($alreadyActive) {
            return back()->with('error', 'Karyawan sudah ditugaskan di gudang ini.');
        }

        $karyawan->gudang()->attach($gudang->id, [
            'tanggal_mulai' => $request->tanggal_mulai,
            'is_active' => true,
        ]);

        if ($karyawan->user) {
            $karyawan->user->update(['gudang_id' => $gudang->id]);
        }

        return back()->with('success', 'Karyawan berhasil ditugaskan ke gudang ' . $gudang->nama_gudang . '.');
    }

    public function unassignGudang(Request $request, Karyawan $karyawan, Gudang $gudang)
    {
        $pivot = $karyawan->gudang()
            ->wherePivot('gudang_id', $gudang->id)
            ->wherePivot('is_active', true)
            ->first();

        if ($pivot) {
            $karyawan->gudang()->updateExistingPivot($gudang->id, [
                'is_active' => false,
                'tanggal_selesai' => now()->format('Y-m-d'),
            ]);

            $masihAktif = $karyawan->gudangAktif()->first();
            if ($karyawan->user) {
                $karyawan->user->update(['gudang_id' => $masihAktif ? $masihAktif->id : null]);
            }

            return back()->with('success', 'Penugasan di gudang ' . $gudang->nama_gudang . ' telah diakhiri.');
        }

        return back()->with('error', 'Tidak ada penugasan aktif di gudang tersebut.');
    }

    public function unassignKandang(Request $request, Karyawan $karyawan, Kandang $kandang)
    {
        $pivot = $karyawan->kandang()
            ->wherePivot('kandang_id', $kandang->id)
            ->wherePivot('is_active', true)
            ->first();

        if ($pivot) {
            $karyawan->kandang()->updateExistingPivot($kandang->id, [
                'is_active' => false,
                'tanggal_selesai' => now()->format('Y-m-d'),
            ]);
            return back()->with('success', 'Penugasan di kandang ' . $kandang->nama_kandang . ' telah diakhiri.');
        }

        return back()->with('error', 'Tidak ada penugasan aktif di kandang tersebut.');
    }
}
