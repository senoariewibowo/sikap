<?php

namespace App\Http\Controllers;

use App\Models\Kandang;
use App\Http\Requests\StoreKandangRequest;
use App\Http\Requests\UpdateKandangRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KandangController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $kandangs = $this->getUserKandangs()
            ->with('gudang')
            ->when($search, fn($q) => $q->where(fn($q2) => $q2->where('nama_kandang', 'like', "%{$search}%")
                ->orWhere('kode_kandang', 'like', "%{$search}%")
                ->orWhere('kecamatan', 'like', "%{$search}%")))
            ->paginate(10)->withQueryString();
        return view('kandang.index', compact('kandangs', 'search'));
    }

    public function create()
    {
        $nextKode = 'KDG-' . str_pad(\App\Models\Kandang::withTrashed()->count() + 1, 3, '0', STR_PAD_LEFT);
        $gudangs = \App\Models\Gudang::where('status', 'aktif')->orderBy('nama_gudang')->get();
        return view('kandang.create', compact('nextKode', 'gudangs'));
    }

    public function store(StoreKandangRequest $request)
    {
        $data = $request->validated();

        $data['initial'] = $this->generateUniqueInitial($request->nama_kandang);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('kandang', 'public');
        }

        Kandang::create($data);

        return redirect()->route('kandang.index')
            ->with('success', 'Data kandang berhasil ditambahkan.');
    }

    public function show(Kandang $kandang)
    {
        $kandang->load(['karyawan' => function ($query) {
            $query->wherePivot('is_active', true);
        }, 'gudang']);
        return view('kandang.show', compact('kandang'));
    }

    public function edit(Kandang $kandang)
    {
        $gudangs = \App\Models\Gudang::where('status', 'aktif')->orderBy('nama_gudang')->get();
        return view('kandang.edit', compact('kandang', 'gudangs'));
    }

    public function update(UpdateKandangRequest $request, Kandang $kandang)
    {
        $data = $request->validated();

        $data['initial'] = $this->generateUniqueInitial($request->nama_kandang, $kandang->id);

        if ($request->hasFile('foto')) {
            if ($kandang->foto) {
                Storage::disk('public')->delete($kandang->foto);
            }
            $data['foto'] = $request->file('foto')->store('kandang', 'public');
        }

        $kandang->update($data);

        return redirect()->route('kandang.index')
            ->with('success', 'Data kandang berhasil diperbarui.');
    }

    public function destroy(Kandang $kandang)
    {
        $kandang->delete();

        return redirect()->route('kandang.index')
            ->with('success', 'Data kandang berhasil dinonaktifkan.');
    }

    private function generateUniqueInitial(string $nama, ?int $excludeId = null): string
    {
        $base = $this->makeBaseInitial($nama);
        $used = Kandang::withTrashed()
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->whereNotNull('initial')
            ->pluck('initial')
            ->toArray();

        if (!in_array($base, $used, true)) {
            return $base;
        }

        $first = substr($base, 0, 1);
        foreach (array_merge(range('A', 'Z'), range('0', '9')) as $c) {
            $candidate = $first . $c;
            if (!in_array($candidate, $used, true)) {
                return $candidate;
            }
        }

        foreach (array_merge(range('A', 'Z'), range('0', '9')) as $a) {
            foreach (array_merge(range('A', 'Z'), range('0', '9')) as $b) {
                $candidate = $a . $b;
                if (!in_array($candidate, $used, true)) {
                    return $candidate;
                }
            }
        }

        return $base;
    }

    private function makeBaseInitial(string $nama): string
    {
        $nama = preg_replace('/[^A-Za-z0-9\s\-_]/', '', $nama);
        $parts = preg_split('/[\s\-_]+/', strtoupper($nama));

        if (count($parts) >= 2) {
            $initial = substr($parts[0], 0, 1) . substr($parts[1], 0, 1);
        } else {
            $initial = substr($parts[0] ?? 'KA', 0, 2);
        }

        return str_pad($initial, 2, substr($parts[0] ?? 'K', 0, 1), STR_PAD_RIGHT);
    }
}
