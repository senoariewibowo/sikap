@extends('layouts.admin')
@section('title', 'Harga Telur - SIKAP')
@section('page-title', 'Master Harga Telur')
@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Umum &mdash; Per Butir</p>
            @if($hargaButir)
            <p class="text-3xl font-bold text-blue-600 mt-2">Rp {{ number_format($hargaButir->harga, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">berlaku sejak {{ $hargaButir->tanggal_mulai_berlaku->format('d/m/Y') }}</p>
            @else
            <p class="text-2xl font-bold text-gray-300 mt-2">Belum diatur</p>
            @endif
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Umum &mdash; Per Kg</p>
            @if($hargaKg)
            <p class="text-3xl font-bold text-amber-600 mt-2">Rp {{ number_format($hargaKg->harga, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">berlaku sejak {{ $hargaKg->tanggal_mulai_berlaku->format('d/m/Y') }}</p>
            @else
            <p class="text-2xl font-bold text-gray-300 mt-2">Belum diatur</p>
            @endif
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Umum &mdash; Per Karpet</p>
            @if($hargaKarpet)
            <p class="text-3xl font-bold text-purple-600 mt-2">Rp {{ number_format($hargaKarpet->harga, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">berlaku sejak {{ $hargaKarpet->tanggal_mulai_berlaku->format('d/m/Y') }}</p>
            @else
            <p class="text-2xl font-bold text-gray-300 mt-2">Belum diatur</p>
            @endif
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Umum &mdash; Per Peti</p>
            @if($hargaPeti)
            <p class="text-3xl font-bold text-indigo-600 mt-2">Rp {{ number_format($hargaPeti->harga, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">berlaku sejak {{ $hargaPeti->tanggal_mulai_berlaku->format('d/m/Y') }}</p>
            @else
            <p class="text-2xl font-bold text-gray-300 mt-2">Belum diatur</p>
            @endif
        </div>
    </div>
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b flex justify-between items-center flex-wrap gap-3">
            <h2 class="text-lg font-semibold text-gray-800">Histori Harga</h2>
            <a href="{{ route('harga.create') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">&plus; Tambah Harga</a>
        </div>
        <form method="GET" class="p-4 border-b bg-gray-50 flex flex-wrap gap-3 items-end">
            <div><label class="block text-xs text-gray-600">Cari</label><input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Satuan, Customer..." class="mt-1 border-gray-300 rounded-md text-sm w-44 px-2 py-1.5"></div>
            <button type="submit" class="px-3 py-1.5 bg-gray-700 text-white rounded-md text-sm">Cari</button>
            <a href="{{ route('harga.index') }}" class="px-3 py-1.5 text-gray-600 text-sm">Reset</a>
        </form>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 cursor-pointer select-none hover:text-indigo-600" data-sort="0" data-type="rupiah" onclick="sortTable(this)">Harga</th>
                        <th class="px-4 py-3 cursor-pointer select-none hover:text-indigo-600" data-sort="1" data-type="text" onclick="sortTable(this)">Satuan</th>
                        <th class="px-4 py-3 cursor-pointer select-none hover:text-indigo-600" data-sort="2" data-type="text" onclick="sortTable(this)">Customer</th>
                        <th class="px-4 py-3 cursor-pointer select-none hover:text-indigo-600" data-sort="3" data-type="date" onclick="sortTable(this)">Berlaku Mulai</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hargas as $h)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-4 py-2 font-medium">Rp {{ number_format($h->harga, 0, ',', '.') }}</td>
                        <td class="px-4 py-2">{{ $h->satuan=='per_butir'?'per butir':($h->satuan=='per_karpet'?'per karpet':($h->satuan=='per_peti'?'per peti':'per kg')) }}</td>
                        <td class="px-4 py-2">{{ $h->customer->nama_customer ?? 'Umum' }}</td>
                        <td class="px-4 py-2">{{ $h->tanggal_mulai_berlaku->format('d/m/Y') }}</td>
                        <td class="px-4 py-2"><div class="flex space-x-1"><a href="{{ route('harga.edit', $h) }}" class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a><form action="{{ route('harga.destroy', $h) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="p-1.5 text-red-600 hover:bg-red-50 rounded"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form></div></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-12 text-center text-gray-500">Belum ada data harga.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $hargas->links() }}</div>
    </div>
</div>
<script>
function sortTable(th){
    var col = parseInt(th.dataset.sort);
    var type = th.dataset.type;
    var table = th.closest('table');
    var tbody = table.querySelector('tbody');
    var rows = Array.from(tbody.querySelectorAll('tr'));
    if(rows.length < 2) return;

    var asc = th.dataset.dir !== 'asc';
    rows.sort(function(a, b){
        var va = cellVal(a.cells[col], type);
        var vb = cellVal(b.cells[col], type);
        return asc ? (va > vb ? 1 : va < vb ? -1 : 0) : (va < vb ? 1 : va > vb ? -1 : 0);
    });
    rows.forEach(function(r){ tbody.appendChild(r); });
    th.dataset.dir = asc ? 'asc' : 'desc';

    table.querySelectorAll('th').forEach(function(h){
        var s = h.querySelector('.sort-arrow');
        if(s) s.remove();
    });
    var arrow = document.createElement('span');
    arrow.className = 'sort-arrow text-indigo-500 ml-1 text-xs';
    arrow.textContent = asc ? '▲' : '▼';
    th.appendChild(arrow);
}

function cellVal(cell, type){
    if(!cell) return '';
    var t = cell.textContent.trim();
    if(type === 'number') return parseFloat(t.replace(/[^0-9.-]/g,'')) || 0;
    if(type === 'rupiah') return parseFloat(t.replace(/[^0-9]/g,'')) || 0;
    if(type === 'date'){
        var parts = t.split('/');
        if(parts.length === 3) return parts[2]+parts[1]+parts[0];
        return t;
    }
    return t.toLowerCase();
}
</script>
@endsection
