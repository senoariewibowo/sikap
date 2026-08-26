@extends('layouts.admin')
@section('title', 'Transaksi Eceran - SIKAP')
@section('page-title', 'Transaksi Eceran')
@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b flex justify-between items-center flex-wrap gap-3">
            <h2 class="text-lg font-semibold text-gray-800">Transaksi Eceran</h2>
            <a href="{{ route('eceran.transaksi.create') }}" class="inline-flex items-center gap-1 px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 font-semibold"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Input Transaksi</a>
        </div>
        <form method="GET" class="p-4 border-b bg-gray-50 flex flex-wrap gap-3 items-end">
            <div><label class="block text-xs text-gray-600">Cari</label><input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Keterangan..." class="mt-1 border-gray-300 rounded-md text-sm w-40 px-2 py-1.5"></div>
            <div><label class="block text-xs text-gray-600">Dari</label><input type="date" name="dari" value="{{ $dari }}" class="mt-1 border-gray-300 rounded-md text-sm"></div>
            <div><label class="block text-xs text-gray-600">Sampai</label><input type="date" name="sampai" value="{{ $sampai }}" class="mt-1 border-gray-300 rounded-md text-sm"></div>
            <button type="submit" class="px-3 py-1.5 bg-gray-700 text-white rounded-md text-sm">Filter</button>
            <a href="{{ route('eceran.transaksi.index') }}" class="px-3 py-1.5 text-gray-600 text-sm">Reset</a>
        </form>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 cursor-pointer select-none hover:text-indigo-600" data-sort="0" data-type="date" onclick="sortTable(this)">Tanggal</th>
                        <th class="px-4 py-3">Satuan</th>
                        <th class="px-4 py-3 text-right cursor-pointer select-none hover:text-indigo-600" data-sort="2" data-type="number" onclick="sortTable(this)">Butir</th>
                        <th class="px-4 py-3 text-right cursor-pointer select-none hover:text-indigo-600" data-sort="3" data-type="number" onclick="sortTable(this)">Qty</th>
                        <th class="px-4 py-3 text-right cursor-pointer select-none hover:text-indigo-600" data-sort="4" data-type="rupiah" onclick="sortTable(this)">Harga</th>
                        <th class="px-4 py-3 text-right cursor-pointer select-none hover:text-indigo-600" data-sort="5" data-type="rupiah" onclick="sortTable(this)">Total</th>
                        <th class="px-4 py-3">Dari Alokasi</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $t)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-4 py-2 whitespace-nowrap">{{ $t->tanggal->format('d/m/Y') }}</td>
                        <td class="px-4 py-2"><span class="px-2 py-0.5 text-xs rounded {{ $t->satuan == 'per_butir' ? 'bg-blue-100 text-blue-700' : ($t->satuan == 'per_karpet' ? 'bg-purple-100 text-purple-700' : 'bg-amber-100 text-amber-700') }}">{{ $t->satuan == 'per_butir' ? 'Butir' : ($t->satuan == 'per_karpet' ? 'Karpet' : 'Kg') }}</span></td>
                        <td class="px-4 py-2 text-right">{{ $t->total_butir > 0 ? number_format($t->total_butir) : '—' }}</td>
                        <td class="px-4 py-2 text-right font-semibold">{{ $t->satuan == 'per_butir' ? number_format($t->total_butir) : ($t->satuan == 'per_karpet' ? number_format($t->karpet) . ' krpt' : number_format($t->berat_kg, 2) . ' kg') }}</td>
                        <td class="px-4 py-2 text-right">Rp {{ number_format($t->harga_per_butir, 0, ',', '.') }}</td>
                        <td class="px-4 py-2 text-right font-semibold text-green-700">Rp {{ number_format($t->total_harga, 0, ',', '.') }}</td>
                        <td class="px-4 py-2">
                            @if($t->details->isNotEmpty())
                            <div class="flex flex-wrap gap-1">
                                @foreach($t->details as $d)
                                <span class="inline-flex items-center px-1.5 py-0.5 text-xs rounded bg-gray-100 text-gray-600">{{ $d->stokEceran->no_referensi ?? '#' . $d->stok_telur_eceran_id }} <span class="font-semibold ml-0.5">{{ $d->jumlah_butir }}</span></span>
                                @endforeach
                            </div>
                            @else
                            <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            <div class="flex space-x-1">
                                <a href="{{ route('eceran.transaksi.edit', $t) }}" class="p-1 text-yellow-600 hover:bg-yellow-50 rounded" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('eceran.transaksi.destroy', $t) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini?')">
                                    @csrf @method('DELETE')
                                    <button class="p-1 text-red-600 hover:bg-red-50 rounded" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-12 text-center text-gray-500">Belum ada transaksi eceran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $transaksis->links() }}</div>
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
