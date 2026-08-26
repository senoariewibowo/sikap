@extends('layouts.admin')
@section('title', 'Edit Alokasi Eceran - SIKAP')
@section('page-title', 'Edit Alokasi Stok Eceran')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('telur.eceran.update', $eceran->id) }}">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                @if($eceran->sortasiDetail)
                <div class="bg-blue-50 rounded-lg p-3">
                    <p class="text-xs text-blue-600">Peti saat ini: <strong>#{{ $eceran->sortasi_telur_detail_id }}</strong> — {{ $eceran->sortasiDetail->butir }} btr, {{ $eceran->sortasiDetail->karpet }} krpt, {{ number_format($eceran->sortasiDetail->berat, 2) }} kg</p>
                </div>
                @endif

                <div>
                    <x-input-label :value="'Ganti Peti (opsional)'" />
                    <select name="sortasi_telur_detail_id" id="peti_select" class="block mt-1 w-full border-gray-300 rounded-md text-sm">
                        <option value="">-- Biarkan peti saat ini --</option>
                        @foreach($petis as $p)
                        <option value="{{ $p->id }}" data-gudang="{{ $p->gudang_id }}" data-butir="{{ $p->butir }}" data-karpet="{{ $p->karpet }}" data-berat="{{ $p->berat }}" {{ old('sortasi_telur_detail_id')==$p->id?'selected':'' }}>{{ $p->gudang_nama }} | #{{ $p->id }} {{ $p->tgl_sortir }} @if($p->kandang_nama)({{ $p->kandang_nama }})@endif | {{ $p->butir }} btr, {{ $p->karpet }} krpt, {{ $p->berat }} kg</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-input-label :value="'Tanggal'" />
                    <input type="date" name="tanggal" value="{{ old('tanggal', $eceran->tanggal->format('Y-m-d')) }}" class="block mt-1 w-full border-gray-300 rounded-md text-sm" required>
                </div>
                <div>
                    <x-input-label :value="'Gudang'" />
                    <select name="gudang_id" id="gudang_select" class="block mt-1 w-full border-gray-300 rounded-md text-sm" required>
                        @foreach($gudangs as $g)
                        <option value="{{ $g->id }}" {{ old('gudang_id', $eceran->gudang_id)==$g->id?'selected':'' }} data-stok="{{ $stokByGudang[$g->id] ?? 0 }}">{{ $g->nama_gudang }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1" id="stok_info">Stok tersedia: -</p>
                </div>
                <div>
                    <x-input-label :value="'Unit Jual'" />
                    <select name="unit_jual" id="unit_jual" class="block mt-1 w-full border-gray-300 rounded-md text-sm" required>
                        <option value="butir" {{ old('unit_jual', $eceran->unit_jual)=='butir'?'selected':'' }}>Butir</option>
                        <option value="kg" {{ old('unit_jual', $eceran->unit_jual)=='kg'?'selected':'' }}>Kilogram (kg)</option>
                        <option value="peti" {{ old('unit_jual', $eceran->unit_jual)=='peti'?'selected':'' }}>Peti (15 kg/peti)</option>
                        <option value="karpet" {{ old('unit_jual', $eceran->unit_jual)=='karpet'?'selected':'' }}>Karpet (30 butir/karpet)</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label :value="'Karpet (tray)'" />
                        <input type="number" name="karpet" id="karpet" value="{{ old('karpet', $eceran->karpet) }}" min="0" class="block mt-1 w-full border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <x-input-label :value="'Peti'" />
                        <input type="number" name="peti" id="peti" value="{{ old('peti', $eceran->peti) }}" min="0" class="block mt-1 w-full border-gray-300 rounded-md text-sm">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label :value="'Jumlah Butir'" />
                        <input type="number" name="jumlah_butir" id="butir" value="{{ old('jumlah_butir', $eceran->jumlah_butir) }}" min="1" class="block mt-1 w-full border-gray-300 rounded-md text-sm" required>
                    </div>
                    <div>
                        <x-input-label :value="'Berat (kg)'" />
                        <input type="number" step="0.01" name="berat_kg" id="berat" value="{{ old('berat_kg', $eceran->berat_kg) }}" min="0" class="block mt-1 w-full border-gray-300 rounded-md text-sm" required>
                    </div>
                </div>
                <div>
                    <x-input-label :value="'Keterangan'" />
                    <textarea name="keterangan" rows="2" class="block mt-1 w-full border-gray-300 rounded-md text-sm">{{ old('keterangan', $eceran->keterangan) }}</textarea>
                </div>
                <div class="flex space-x-2">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">Simpan</button>
                    <a href="{{ route('setoran.gudang') }}" class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
(function(){
    var petiSelect = document.getElementById('peti_select');
    var gudangSelect = document.getElementById('gudang_select');
    var unitJual = document.getElementById('unit_jual');
    var karpet = document.getElementById('karpet');
    var peti = document.getElementById('peti');
    var butir = document.getElementById('butir');
    var berat = document.getElementById('berat');
    var stokInfo = document.getElementById('stok_info');

    petiSelect.addEventListener('change', function(){
        var opt = this.options[this.selectedIndex];
        if(this.value){
            gudangSelect.value = opt.getAttribute('data-gudang');
            unitJual.value = 'peti';
            butir.value = opt.getAttribute('data-butir');
            karpet.value = opt.getAttribute('data-karpet');
            berat.value = opt.getAttribute('data-berat');
            gudangSelect.dispatchEvent(new Event('change'));
            autoHitung();
        }
    });

    gudangSelect.addEventListener('change', function(){
        var opt = this.options[this.selectedIndex];
        var stok = opt.getAttribute('data-stok') || 0;
        stokInfo.textContent = 'Stok tersedia: ' + parseInt(stok).toLocaleString('id-ID');
    });
    if(gudangSelect.selectedIndex > 0) gudangSelect.dispatchEvent(new Event('change'));

    function autoHitung(){
        if(unitJual.value === 'karpet'){
            karpet.readOnly = false;
            peti.readOnly = true;
            butir.readOnly = true;
            berat.readOnly = true;
        } else if(unitJual.value === 'peti'){
            karpet.readOnly = true;
            peti.readOnly = false;
            butir.readOnly = true;
            berat.readOnly = true;
        } else if(unitJual.value === 'kg'){
            karpet.readOnly = true;
            peti.readOnly = true;
            butir.readOnly = true;
            berat.readOnly = false;
        } else {
            karpet.readOnly = true;
            peti.readOnly = true;
            butir.readOnly = false;
            berat.readOnly = true;
        }
    }

    unitJual.addEventListener('change', autoHitung);
    karpet.addEventListener('input', function(){ autoHitung(); var v=parseInt(this.value)||0; if(v>0 && unitJual.value==='karpet') butir.value=v*30; });
    peti.addEventListener('input', function(){ autoHitung(); var v=parseInt(this.value)||0; if(v>0 && unitJual.value==='peti') berat.value=(v*15).toFixed(2); });
    autoHitung();
})();
</script>
@endsection
