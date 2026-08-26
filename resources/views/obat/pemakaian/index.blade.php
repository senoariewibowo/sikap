@extends('layouts.admin')

@section('title', 'Pemakaian Obat - SIKAP')
@section('page-title', 'Pemakaian Obat & Vitamin')

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
                <a href="{{ route('obat.pemakaian.index', ['kandang_id' => $k->id]) }}" class="px-3 py-1 text-xs rounded-full {{ ($kandangId == $k->id) ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    {{ $k->nama_kandang }}
                </a>
                @endforeach
            </div>
        </div>
    </div>

    @if($kandangId)
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Catat Pemakaian</h3>
        </div>
        <form action="{{ route('obat.pemakaian.store') }}" method="POST" class="p-4 space-y-3">
            @csrf
            <input type="hidden" name="kandang_id" value="{{ $kandangId }}">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Item</label>
                    <select name="obat_id" class="block w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                        <option value="">Pilih Item</option>
                        @foreach($obats as $o)
                        <option value="{{ $o->id }}" {{ old('obat_id') == $o->id ? 'selected' : '' }}>{{ $o->nama }} ({{ $o->satuan }})</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('obat_id')" class="mt-1" />
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
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
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
                        <th class="px-3 py-2">Item</th>
                        <th class="px-3 py-2">Kandang</th>
                        <th class="px-3 py-2 text-right">Jumlah</th>
                        <th class="px-3 py-2">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pemakaians as $p)
                    <tr class="bg-white border-b">
                        <td class="px-3 py-2">{{ $p->tanggal->format('d/m/Y') }}</td>
                        <td class="px-3 py-2 font-medium text-gray-900">{{ $p->obat->nama ?? '-' }}</td>
                        <td class="px-3 py-2">{{ $p->kandang->nama_kandang ?? '-' }}</td>
                        <td class="px-3 py-2 text-right text-blue-600">{{ number_format($p->jumlah, 2) }} {{ $p->obat->satuan ?? '' }}</td>
                        <td class="px-3 py-2 text-gray-400">{{ $p->keterangan ?: '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">Belum ada pemakaian.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t">{{ $pemakaians->links() }}</div>
    </div>

    @endif
</div>
@endsection
