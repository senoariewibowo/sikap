@extends('layouts.admin')

@section('title', 'Distribusi Pakan - SIKAP')
@section('page-title', 'Distribusi Pakan Baru')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Distribusi Pakan ke Kandang</h2>
            <p class="text-sm text-gray-500 mt-1">Kirim pakan dari gudang ke kandang.</p>
        </div>

        <form action="{{ route('pakan.distribusi.store') }}" method="POST" class="p-6 space-y-4">
            @csrf

            <div>
                <x-input-label for="gudang_id" :value="'Gudang Asal'" />
                <select id="gudang_id" name="gudang_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                    <option value="">Pilih Gudang</option>
                    @foreach($gudangs as $g)
                    <option value="{{ $g->id }}" {{ old('gudang_id', $gudangId) == $g->id ? 'selected' : '' }}>{{ $g->nama_gudang }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('gudang_id')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="kandang_id" :value="'Kandang Tujuan'" />
                <select id="kandang_id" name="kandang_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm" required {{ $kandangs->isEmpty() ? 'disabled' : '' }}>
                    <option value="">Pilih Kandang</option>
                    @foreach($kandangs as $k)
                    <option value="{{ $k->id }}" {{ old('kandang_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kandang }} ({{ $k->kode_kandang }})</option>
                    @endforeach
                </select>
                @if($kandangs->isEmpty() && old('gudang_id'))
                <p class="text-xs text-red-500 mt-1">Tidak ada kandang aktif di gudang ini.</p>
                @elseif($kandangs->isEmpty())
                <p class="text-xs text-gray-400 mt-1">Pilih gudang terlebih dahulu.</p>
                @endif
                <x-input-error :messages="$errors->get('kandang_id')" class="mt-2" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="pakan_id" :value="'Pakan'" />
                    <select id="pakan_id" name="pakan_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                        <option value="">Pilih Pakan</option>
                        @foreach($pakan as $p)
                        <option value="{{ $p->id }}" {{ old('pakan_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }} ({{ $p->satuan }})</option>
                        @endforeach
                    </select>
                    <p id="stok-info" class="text-xs text-green-600 mt-1 hidden"></p>
                    <x-input-error :messages="$errors->get('pakan_id')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="jumlah" :value="'Jumlah'" />
                    <x-text-input id="jumlah" name="jumlah" type="number" step="0.01" class="block mt-1 w-full" :value="old('jumlah')" required />
                    <x-input-error :messages="$errors->get('jumlah')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="tanggal_kirim" :value="'Tanggal Kirim'" />
                <x-text-input id="tanggal_kirim" name="tanggal_kirim" type="date" class="block mt-1 w-full" :value="old('tanggal_kirim', date('Y-m-d'))" required />
                <x-input-error :messages="$errors->get('tanggal_kirim')" class="mt-2" />
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('pakan.distribusi.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
                <x-primary-button>Kirim</x-primary-button>
            </div>
        </form>
    </div>
</div>

<script>
var gudangEl = document.getElementById('gudang_id');
var pakanEl = document.getElementById('pakan_id');
var stokInfo = document.getElementById('stok-info');

function fetchStok() {
    var gid = gudangEl.value;
    var pid = pakanEl.value;
    if (!gid || !pid) {
        stokInfo.classList.add('hidden');
        return;
    }
    fetch('/pakan/stok-ajax?pakan_id=' + pid + '&gudang_id=' + gid)
        .then(r => r.json())
        .then(data => {
            if (data.stok !== null) {
                stokInfo.innerHTML = 'Stok gudang tersedia: <strong>' + Number(data.stok).toLocaleString() + ' ' + data.satuan + '</strong>';
                stokInfo.classList.remove('hidden');
            } else {
                stokInfo.classList.add('hidden');
            }
        });
}

gudangEl.addEventListener('change', fetchStok);
pakanEl.addEventListener('change', fetchStok);
</script>
@endsection
