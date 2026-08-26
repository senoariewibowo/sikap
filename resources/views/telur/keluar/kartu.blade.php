@extends('layouts.admin')

@section('title', 'Kartu Stok Telur - SIKAP')
@section('page-title', 'Kartu Stok Telur')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow">
        <form method="GET" class="p-4 border-b border-gray-200 bg-gray-50 flex flex-wrap gap-3 items-end">
            <div><label class="block text-xs text-gray-600">Gudang</label><select name="gudang_id" class="mt-1 border-gray-300 rounded-md text-sm"><option value="">Semua</option>@foreach($gudangs as $g)<option value="{{ $g->id }}" {{ $gudangId==$g->id?'selected':'' }}>{{ $g->nama_gudang }}</option>@endforeach</select></div>
            <div><label class="block text-xs text-gray-600">Dari</label><input type="date" name="dari" value="{{ $dari }}" class="mt-1 border-gray-300 rounded-md text-sm"></div>
            <div><label class="block text-xs text-gray-600">Sampai</label><input type="date" name="sampai" value="{{ $sampai }}" class="mt-1 border-gray-300 rounded-md text-sm"></div>
            <button type="submit" class="px-3 py-1.5 bg-gray-700 text-white rounded-md text-sm">Filter</button>
            <a href="{{ route('telur.keluar.kartu') }}" class="px-3 py-1.5 text-gray-600 text-sm hover:bg-gray-200">Reset</a>
        </form>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3">Tipe</th><th class="px-4 py-3">Gudang</th><th class="px-4 py-3">Butir</th><th class="px-4 py-3">Kg</th><th class="px-4 py-3">Keterangan</th><th class="px-4 py-3">Saldo</th></tr>
                </thead>
                <tbody>
                    @forelse($merged as $m)
                    <tr class="border-b {{ $m->tipe == 'Keluar' ? 'bg-orange-50' : 'bg-green-50' }}">
                        <td class="px-4 py-2">{{ is_string($m->tanggal) ? \Carbon\Carbon::parse($m->tanggal)->format('d/m/Y') : $m->tanggal->format('d/m/Y') }}</td>
                        <td class="px-4 py-2"><span class="px-2 py-1 text-xs rounded-full {{ $m->tipe == 'Keluar' ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800' }}">{{ $m->tipe }}</span></td>
                        <td class="px-4 py-2">{{ $m->kandang }}</td>
                        <td class="px-4 py-2 {{ $m->butir < 0 ? 'text-red-600' : 'text-green-600' }}">{{ $m->butir > 0 ? '+' . number_format($m->butir) : number_format($m->butir) }}</td>
                        <td class="px-4 py-2">{{ $m->kg > 0 ? '+' . number_format($m->kg, 1) : number_format($m->kg, 1) }}</td>
                        <td class="px-4 py-2 text-xs max-w-xs truncate">{{ $m->ket }}</td>
                        <td class="px-4 py-2 font-semibold {{ $m->saldo < 0 ? 'text-red-600' : 'text-gray-900' }}">{{ number_format($m->saldo) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-12 text-center text-gray-500">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
