@extends('layouts.admin')

@section('title', 'Produksi Telur - SIKAP')
@section('page-title', 'Data Produksi Telur')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Daftar Produksi Telur</h2>
            <p class="text-sm text-gray-500 mt-1">Total: {{ isset($produksis) ? $produksis->total() : count($resume ?? []) }} data</p>
        </div>
        <a href="{{ route('produksi.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Produksi
        </a>
    </div>

    <form method="GET" class="px-6 py-3 border-b border-gray-200 bg-gray-50 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-600 mb-1">Kandang</label>
            <select name="kandang_id" class="border-gray-300 rounded-md text-sm px-3 py-1.5">
                <option value="">Semua Kandang</option>
                @foreach($kandangs as $k)
                <option value="{{ $k->id }}" {{ $kandangId == $k->id ? 'selected' : '' }}>{{ $k->nama_kandang }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-600 mb-1">Dari</label>
            <input type="date" name="dari" value="{{ $tanggalStart }}" class="border-gray-300 rounded-md text-sm px-3 py-1.5">
        </div>
        <div>
            <label class="block text-xs text-gray-600 mb-1">Sampai</label>
            <input type="date" name="sampai" value="{{ $tanggalEnd }}" class="border-gray-300 rounded-md text-sm px-3 py-1.5">
        </div>
        <div>
            <label class="block text-xs text-gray-600 mb-1">Status</label>
            <select name="status_setor" class="border-gray-300 rounded-md text-sm px-3 py-1.5">
                <option value="">Semua</option>
                <option value="belum_disetor" {{ ($statusSetor ?? '') == 'belum_disetor' ? 'selected' : '' }}>Belum Disetor</option>
                <option value="sudah_disetor" {{ ($statusSetor ?? '') == 'sudah_disetor' ? 'selected' : '' }}>Sudah Disetor</option>
            </select>
        </div>
        <div>
            <button type="submit" class="px-3 py-1.5 bg-gray-700 text-white rounded-md text-sm hover:bg-gray-800">Filter</button>
            <a href="{{ route('produksi.index') }}" class="px-3 py-1.5 text-gray-600 text-sm hover:bg-gray-200 inline-block ml-1">Reset</a>
        </div>
    </form>

    @if($isResume ?? false)
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-3 py-3">Kandang</th>
                    <th class="px-3 py-3 text-right">Total Butir</th>
                    <th class="px-3 py-3 text-right">Karpet</th>
                    <th class="px-3 py-3 text-right">Sisa</th>
                    <th class="px-3 py-3 text-right">Hari</th>
                    <th class="px-3 py-3 text-right">HDP%</th>
                    <th class="px-3 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($resume as $r)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-3 py-2 font-medium text-gray-900">{{ $r->kandang->nama_kandang ?? '-' }}</td>
                    <td class="px-3 py-2 text-right">{{ number_format($r->total_butir) }}</td>
                    <td class="px-3 py-2 text-right">{{ number_format($r->total_karpet ?? 0) }}</td>
                    <td class="px-3 py-2 text-right">{{ number_format($r->total_sisa ?? 0) }}</td>
                    <td class="px-3 py-2 text-right">{{ $r->hari_produksi }}</td>
                    <td class="px-3 py-2 text-right"><span class="text-xs font-medium {{ $r->hdp >= 90 ? 'text-green-600' : ($r->hdp >= 80 ? 'text-yellow-600' : 'text-red-600') }}">{{ $r->hdp }}%</span></td>
                    <td class="px-3 py-2">
                        <a href="{{ route('produksi.index', ['kandang_id' => $r->kandang_id, 'dari' => $tanggalStart, 'sampai' => $tanggalEnd]) }}" class="px-2 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">Detail</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-12 text-center text-gray-500">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-3 py-3">Tanggal</th>
                    <th class="px-3 py-3">Kandang</th>
                    <th class="px-3 py-3">Shift</th>
                    <th class="px-3 py-3 text-right">Karpet</th>
                    <th class="px-3 py-3 text-right">Butir Karpet</th>
                    <th class="px-3 py-3 text-right">Sisa</th>
                    <th class="px-3 py-3 text-right">Total Butir</th>
                    <th class="px-3 py-3">Status</th>
                    <th class="px-3 py-3">HDP%</th>
                    <th class="px-3 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produksis as $pr)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-3 py-2 font-medium text-gray-900">{{ $pr->tanggal->format('d/m/Y') }}</td>
                    <td class="px-3 py-2">{{ $pr->kandang->nama_kandang ?? '-' }}</td>
                    <td class="px-3 py-2">{{ $pr->shift ?: '-' }}</td>
                    <td class="px-3 py-2 text-right">{{ $pr->karpet ?: '-' }}</td>
                    <td class="px-3 py-2 text-right">{{ number_format($pr->karpet * 30) }}</td>
                    <td class="px-3 py-2 text-right">{{ number_format($pr->sisa) }}</td>
                    <td class="px-3 py-2 text-right font-medium">{{ number_format($pr->jumlah_butir) }}</td>
                    <td class="px-3 py-2">
                        @if($pr->status_setor === 'sudah_disetor')
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Sudah Disetor</span>
                        @else
                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Belum</span>
                        @endif
                    </td>
                    <td class="px-3 py-2 text-right">
                        @php $pop = $pr->kandang->populasiSekarang(); $hdp = $pop > 0 ? round($pr->jumlah_butir / $pop * 100, 1) : 0; @endphp
                        <span class="text-xs font-medium {{ $hdp >= 90 ? 'text-green-600' : ($hdp >= 80 ? 'text-yellow-600' : 'text-red-600') }}">{{ $hdp }}%</span>
                    </td>
                    <td class="px-3 py-2">
                        <div class="flex space-x-1">
                            @if($pr->status_setor !== 'sudah_disetor')
                            <form action="{{ route('produksi.setor', $pr) }}" method="POST" onsubmit="return confirm('Setor produksi ini ke gudang?')">
                                @csrf
                                <button class="p-1 text-green-600 hover:bg-green-50 rounded" title="Setor ke Gudang">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                            </form>
                            @endif
                            @if($pr->status_setor === 'sudah_disetor')
                            <a href="{{ route('produksi.show', $pr) }}"
                               class="p-1 text-blue-600 hover:bg-blue-50 rounded" title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            @endif
                            <a href="{{ route('produksi.edit', $pr) }}" class="p-1 text-yellow-600 hover:bg-yellow-50 rounded">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            @if($pr->status_setor !== 'sudah_disetor' || auth()->user()->hasRole('super_admin'))
                            <form action="{{ route('produksi.destroy', $pr) }}" method="POST" onsubmit="return confirm('Hapus?')">
                                @csrf @method('DELETE')
                                <button class="p-1 text-red-600 hover:bg-red-50 rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="px-4 py-12 text-center text-gray-500">Belum ada data produksi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-200">{{ $produksis->links() }}</div>
    @endif
</div>
@endsection
