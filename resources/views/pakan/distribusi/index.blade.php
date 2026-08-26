@extends('layouts.admin')

@section('title', 'Distribusi Pakan - SIKAP')
@section('page-title', 'Distribusi Pakan')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">Distribusi Pakan ke Kandang</h2>
        <a href="{{ route('pakan.distribusi.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Distribusi Baru
        </a>
    </div>

    <form method="GET" class="px-6 py-3 border-b border-gray-200 bg-gray-50 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-600 mb-1">Gudang</label>
            <select name="gudang_id" class="border-gray-300 rounded-md text-sm px-3 py-1.5">
                <option value="">Semua Gudang</option>
                @foreach($gudangs as $g)
                <option value="{{ $g->id }}" {{ ($gudangId ?? '') == $g->id ? 'selected' : '' }}>{{ $g->nama_gudang }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-600 mb-1">Status</label>
            <select name="status" class="border-gray-300 rounded-md text-sm px-3 py-1.5">
                <option value="">Semua</option>
                <option value="dikirim" {{ ($status ?? '') == 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                <option value="diterima" {{ ($status ?? '') == 'diterima' ? 'selected' : '' }}>Diterima</option>
            </select>
        </div>
        <div>
            <button type="submit" class="px-3 py-1.5 bg-gray-700 text-white rounded-md text-sm hover:bg-gray-800">Filter</button>
            <a href="{{ route('pakan.distribusi.index') }}" class="px-3 py-1.5 text-gray-600 text-sm hover:bg-gray-200 inline-block ml-1">Reset</a>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-3 py-3">Tgl Kirim</th>
                    <th class="px-3 py-3">Pakan</th>
                    <th class="px-3 py-3">Gudang</th>
                    <th class="px-3 py-3">Kandang</th>
                    <th class="px-3 py-3 text-right">Jumlah</th>
                    <th class="px-3 py-3">Status</th>
                    <th class="px-3 py-3">Diterima</th>
                    <th class="px-3 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($distribusis as $d)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-3 py-2">{{ $d->tanggal_kirim->format('d/m/Y') }}</td>
                    <td class="px-3 py-2 font-medium text-gray-900">{{ $d->pakan->nama ?? '-' }}</td>
                    <td class="px-3 py-2">{{ $d->gudang->nama_gudang ?? '-' }}</td>
                    <td class="px-3 py-2">{{ $d->kandang->nama_kandang ?? '-' }}</td>
                    <td class="px-3 py-2 text-right">{{ number_format($d->jumlah, 1) }} {{ $d->pakan->satuan ?? '' }}</td>
                    <td class="px-3 py-2">
                        <span class="px-2 py-1 text-xs rounded-full {{ $d->status === 'diterima' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($d->status) }}
                        </span>
                    </td>
                    <td class="px-3 py-2 text-xs text-gray-400">
                        @if($d->status === 'diterima')
                            {{ $d->penerima->name ?? '-' }} <br> {{ $d->tanggal_diterima?->format('d/m/Y') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-3 py-2">
                        @if($d->status === 'dikirim')
                        <form action="{{ route('pakan.distribusi.destroy', $d) }}" method="POST" onsubmit="return confirm('Batalkan distribusi ini?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline text-xs">Batal</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-6 py-12 text-center text-gray-500">Belum ada data distribusi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-200">{{ $distribusis->links() }}</div>
</div>
@endsection
