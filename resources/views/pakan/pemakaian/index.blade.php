@extends('layouts.admin')

@section('title', 'Pemakaian Pakan - SIKAP')
@section('page-title', 'Pemakaian Pakan')

@section('content')
<div class="space-y-6">

    @if($kandangAktif->isEmpty())
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm text-yellow-800">
        Anda belum ditugaskan ke kandang manapun. Hubungi admin.
    </div>
    @else

    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center gap-4">
                <h3 class="text-sm font-semibold text-gray-700">Kandang:</h3>
                @foreach($kandangAktif as $k)
                <a href="{{ route('pakan.pemakaian.index', ['kandang_id' => $k->id]) }}" class="px-3 py-1 text-xs rounded-full {{ ($kandangId == $k->id) ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    {{ $k->nama_kandang }}
                </a>
                @endforeach
            </div>
        </div>
    </div>

    @if($distribusiPending->isNotEmpty() && $kandangId)
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b border-gray-200 bg-yellow-50">
            <h3 class="text-sm font-semibold text-yellow-800">Distribusi Menunggu Diterima</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-3 py-2">Tgl Kirim</th>
                        <th class="px-3 py-2">Pakan</th>
                        <th class="px-3 py-2 text-right">Jumlah</th>
                        <th class="px-3 py-2">Dari Gudang</th>
                        <th class="px-3 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($distribusiPending as $d)
                    <tr class="bg-white border-b">
                        <td class="px-3 py-2">{{ $d->tanggal_kirim->format('d/m/Y') }}</td>
                        <td class="px-3 py-2 font-medium text-gray-900">{{ $d->pakan->nama ?? '-' }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($d->jumlah, 1) }} {{ $d->pakan->satuan ?? '' }}</td>
                        <td class="px-3 py-2">{{ $d->gudang->nama_gudang ?? '-' }}</td>
                        <td class="px-3 py-2">
                            <form action="{{ route('pakan.distribusi.terima', $d) }}" method="POST" onsubmit="return confirm('Konfirmasi penerimaan distribusi ini?')">
                                @csrf
                                <button type="submit" class="px-2 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700">Terima</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if($kandangId)
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b border-gray-200 bg-green-50">
            <h3 class="text-sm font-semibold text-green-800">Stok Tersedia di Kandang</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-3 py-2">Pakan</th>
                        <th class="px-3 py-2 text-right">Total Diterima</th>
                        <th class="px-3 py-2 text-right">Sudah Dipakai</th>
                        <th class="px-3 py-2 text-right font-semibold">Sisa</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $grouped = $distribusiDiterima->groupBy('pakan_id');
                    @endphp
                    @foreach($grouped as $pakanId => $items)
                    @php
                        $totalDiterima = $items->sum('jumlah');
                        $totalPakai = \App\Models\PakanPemakaian::where('pakan_id', $pakanId)
                            ->where('kandang_id', $kandangId)->sum('jumlah');
                        $sisa = $totalDiterima - $totalPakai;
                    @endphp
                    <tr class="bg-white border-b">
                        <td class="px-3 py-2 font-medium text-gray-900">{{ $items->first()->pakan->nama ?? '-' }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($totalDiterima, 1) }} {{ $items->first()->pakan->satuan ?? '' }}</td>
                        <td class="px-3 py-2 text-right text-orange-600">{{ number_format($totalPakai, 1) }} {{ $items->first()->pakan->satuan ?? '' }}</td>
                        <td class="px-3 py-2 text-right font-semibold {{ $sisa <= 0 ? 'text-red-600' : 'text-green-700' }}">
                            {{ number_format($sisa, 1) }} {{ $items->first()->pakan->satuan ?? '' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Catat Pemakaian Pakan</h3>
        </div>
        <form action="{{ route('pakan.pemakaian.store') }}" method="POST" class="p-4 space-y-3">
            @csrf
            <input type="hidden" name="kandang_id" value="{{ $kandangId }}">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Pakan</label>
                    <select name="pakan_id" class="block w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                        <option value="">Pilih Pakan</option>
                        @foreach($pakans as $p)
                        <option value="{{ $p->id }}" {{ old('pakan_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }} ({{ $p->satuan }})</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('pakan_id')" class="mt-1" />
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Jumlah</label>
                    <input type="number" name="jumlah" step="0.01" value="{{ old('jumlah') }}" class="block w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                    <x-input-error :messages="$errors->get('jumlah')" class="mt-1" />
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" class="block w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                    <x-input-error :messages="$errors->get('tanggal')" class="mt-1" />
                </div>
            </div>

            <div>
                <label class="block text-xs text-gray-600 mb-1">Keterangan (opsional)</label>
                <textarea name="keterangan" rows="2" class="block w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('keterangan') }}</textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
                    Catat Pemakaian
                </button>
            </div>
        </form>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Riwayat Pemakaian</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-3 py-2">Tanggal</th>
                        <th class="px-3 py-2">Pakan</th>
                        <th class="px-3 py-2">Kandang</th>
                        <th class="px-3 py-2 text-right">Jumlah</th>
                        <th class="px-3 py-2">Keterangan</th>
                        <th class="px-3 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pemakaians as $p)
                    <tr class="bg-white border-b">
                        <td class="px-3 py-2">{{ $p->tanggal->format('d/m/Y') }}</td>
                        <td class="px-3 py-2 font-medium text-gray-900">{{ $p->pakan->nama ?? '-' }}</td>
                        <td class="px-3 py-2">{{ $p->kandang->nama_kandang ?? '-' }}</td>
                        <td class="px-3 py-2 text-right text-orange-600">{{ number_format($p->jumlah, 1) }} {{ $p->pakan->satuan ?? '' }}</td>
                        <td class="px-3 py-2 text-gray-400">{{ $p->keterangan ?: '-' }}</td>
                        <td class="px-3 py-2">
                            <div class="flex space-x-1">
                                <a href="{{ route('pakan.pemakaian.edit', $p) }}" class="p-1 text-yellow-600 hover:bg-yellow-50 rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('pakan.pemakaian.destroy', $p) }}" method="POST" onsubmit="return confirm('Hapus pemakaian ini?')">
                                    @csrf @method('DELETE')
                                    <button class="p-1 text-red-600 hover:bg-red-50 rounded">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">Belum ada pemakaian.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t">{{ $pemakaians->links() }}</div>
    </div>

    @endif
</div>
@endsection
