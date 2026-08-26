@extends('layouts.admin')
@section('title', 'Edit Transaksi Eceran - SIKAP')
@section('page-title', 'Edit Transaksi Eceran')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('eceran.transaksi.update', $transaksi) }}">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <x-input-label :value="'Tanggal'" />
                    <input type="date" name="tanggal" value="{{ old('tanggal', $transaksi->tanggal->format('Y-m-d')) }}" class="block mt-1 w-full border-gray-300 rounded-md text-sm" required>
                </div>

                <div class="bg-green-50 rounded-lg p-3 flex justify-between items-center">
                    <span class="text-sm text-green-700">Stok Eceran Tersedia</span>
                    <span class="text-xl font-bold text-green-700">{{ number_format($pool) }} <span class="text-xs font-normal">butir</span></span>
                </div>

                @if($transaksi->details->isNotEmpty())
                <div class="bg-blue-50 rounded-lg p-3">
                    <p class="text-xs text-blue-600 font-semibold mb-2">Alokasi saat ini:</p>
                    @foreach($transaksi->details as $d)
                    <p class="text-xs text-blue-500">- {{ $d->stokEceran->no_referensi ?? '#' . $d->stok_telur_eceran_id }} ({{ $d->stokEceran->gudang->nama_gudang ?? '-' }}): {{ number_format($d->jumlah_butir) }} butir</p>
                    @endforeach
                </div>
                @endif

                <div>
                    <x-input-label :value="'Satuan'" />
                    <div class="flex gap-4 mt-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="satuan" value="per_butir" {{ old('satuan', $transaksi->satuan) == 'per_butir' ? 'checked' : '' }} onchange="toggleSatuan()" class="text-green-600">
                            <span class="text-sm">Per Butir</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="satuan" value="per_kg" {{ old('satuan', $transaksi->satuan) == 'per_kg' ? 'checked' : '' }} onchange="toggleSatuan()" class="text-green-600">
                            <span class="text-sm">Per Kg</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="satuan" value="per_karpet" {{ old('satuan', $transaksi->satuan) == 'per_karpet' ? 'checked' : '' }} onchange="toggleSatuan()" class="text-green-600">
                            <span class="text-sm">Per Karpet</span>
                        </label>
                    </div>
                </div>

                <div id="section_kg" class="hidden">
                    <x-input-label :value="'Berat (kg)'" />
                    <input type="number" step="0.01" name="berat_kg" id="berat_kg" value="{{ old('berat_kg', $transaksi->berat_kg) }}" min="0.01" class="block mt-1 w-full border-gray-300 rounded-md text-sm">
                </div>

                <div id="section_karpet" class="hidden">
                    <x-input-label :value="'Karpet (tray)'" />
                    <input type="number" name="karpet" id="karpet" value="{{ old('karpet', $transaksi->karpet) }}" min="1" class="block mt-1 w-full border-gray-300 rounded-md text-sm">
                    <p class="text-xs text-gray-400 mt-1">1 karpet = 30 butir &middot; otomatis terisi</p>
                </div>

                <div>
                    <x-input-label :value="'Jumlah Butir'" />
                    <input type="number" name="jumlah_butir" id="jumlah_butir" value="{{ old('jumlah_butir', $transaksi->total_butir) }}" min="1" max="{{ $pool }}" class="block mt-1 w-full border-gray-300 rounded-md text-sm" required>
                    <p class="text-xs text-gray-400 mt-1">Wajib diisi untuk mengurangi stok alokasi eceran</p>
                </div>

                <div>
                    <x-input-label :value="'Harga per Satuan (Rp)'" />
                    <input type="text" name="harga_per_butir" id="harga_per_butir" data-type="rupiah" readonly class="block mt-1 w-full border-gray-200 bg-gray-50 rounded-md text-sm text-gray-700" required>
                </div>

                <div>
                    <div class="bg-gray-50 rounded p-3">
                        <span class="text-xs text-gray-500">Total:</span>
                        <span id="total_display" class="text-lg font-bold text-green-700 ml-2">Rp 0</span>
                    </div>
                </div>
                <div>
                    <x-input-label :value="'Keterangan'" />
                    <textarea name="keterangan" rows="2" class="block mt-1 w-full border-gray-300 rounded-md text-sm">{{ old('keterangan', $transaksi->keterangan) }}</textarea>
                </div>
                <div class="flex space-x-2">
                    <button type="submit" class="inline-flex items-center gap-1 px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 font-semibold"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Simpan</button>
                    <a href="{{ route('eceran.transaksi.index') }}" class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
(function(){
    var radioButir = document.querySelector('input[value="per_butir"]');
    var radioKg = document.querySelector('input[value="per_kg"]');
    var radioKarpet = document.querySelector('input[value="per_karpet"]');
    var sectionKg = document.getElementById('section_kg');
    var sectionKarpet = document.getElementById('section_karpet');
    var jumlahButir = document.getElementById('jumlah_butir');
    var beratKg = document.getElementById('berat_kg');
    var karpet = document.getElementById('karpet');
    var harga = document.getElementById('harga_per_butir');
    var totalDisplay = document.getElementById('total_display');

    var hargaButir = {{ $hargaButir }};
    var hargaKg = {{ $hargaKg }};
    var hargaKarpet = {{ $hargaKarpet }};
    var existingHarga = {{ $transaksi->harga_per_butir }};
    var existingSatuan = '{{ $transaksi->satuan }}';

    function toggleSatuan(){
        var isKg = radioKg.checked;
        var isKarpet = radioKarpet.checked;
        sectionKg.classList.toggle('hidden', !isKg);
        sectionKarpet.classList.toggle('hidden', !isKarpet);
        if(isKg){
            if(existingSatuan === 'per_kg'){
                harga.value = existingHarga.toLocaleString('id-ID');
            } else {
                harga.value = (parseFloat(hargaKg) || 0).toLocaleString('id-ID');
            }
            beratKg.setAttribute('required', 'required');
            karpet.removeAttribute('required');
        } else if(isKarpet){
            if(existingSatuan === 'per_karpet'){
                harga.value = existingHarga.toLocaleString('id-ID');
            } else {
                harga.value = (parseFloat(hargaKarpet) || 0).toLocaleString('id-ID');
            }
            karpet.setAttribute('required', 'required');
            beratKg.removeAttribute('required');
            var kv = parseInt(karpet.value) || 0;
            if(kv > 0) jumlahButir.value = kv * 30;
        } else {
            if(existingSatuan === 'per_butir'){
                harga.value = existingHarga.toLocaleString('id-ID');
            } else {
                harga.value = (parseFloat(hargaButir) || 0).toLocaleString('id-ID');
            }
            beratKg.removeAttribute('required');
            karpet.removeAttribute('required');
        }
        updateTotal();
    }

    function updateTotal(){
        var isKg = radioKg.checked;
        var isKarpet = radioKarpet.checked;
        var qty;
        if(isKg){
            qty = parseFloat(beratKg.value) || 0;
        } else if(isKarpet){
            qty = parseInt(karpet.value) || 0;
        } else {
            qty = parseInt(jumlahButir.value) || 0;
        }
        var h = parseFloat(harga.value.replace(/\D/g, '')) || 0;
        var total = qty * h;
        totalDisplay.textContent = 'Rp ' + Math.round(total).toLocaleString('id-ID');
    }

    radioButir.addEventListener('change', toggleSatuan);
    radioKg.addEventListener('change', toggleSatuan);
    radioKarpet.addEventListener('change', toggleSatuan);
    jumlahButir.addEventListener('input', updateTotal);
    beratKg.addEventListener('input', updateTotal);
    karpet.addEventListener('input', updateTotal);
    karpet.addEventListener('input', function(){
        var v = parseInt(this.value) || 0;
        if(v > 0 && radioKarpet.checked) jumlahButir.value = v * 30;
        updateTotal();
    });
    harga.addEventListener('input', updateTotal);

    toggleSatuan();
})();
</script>
@endsection
