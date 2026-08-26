@extends('layouts.admin')
@section('title', 'Input Telur Keluar - SIKAP')
@section('page-title', 'Input Telur Keluar')
@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b"><h2 class="text-lg font-semibold text-gray-800">Form Telur Keluar</h2></div>
        <form action="{{ route('telur.keluar.store') }}" method="POST" class="p-6 space-y-4" id="formKeluar">@csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label :value="'Tanggal'" />
                    <x-text-input name="tanggal" type="date" class="block mt-1 w-full" :value="old('tanggal', now()->format('Y-m-d'))" required />
                </div>
                <div x-data="driverSelect()">
                    <x-input-label :value="'Driver / Pengemudi'" />
                    <div class="relative mt-1">
                        <input type="text" x-model="search" @focus="open = true" @click.away="open = false" @input="open = true"
                            class="block w-full border-gray-300 rounded-md text-sm px-3 py-1.5 focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Cari driver...">
                        <input type="hidden" name="driver" x-model="selected">
                        <div x-show="open && filteredDrivers.length > 0" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-48 overflow-y-auto text-sm">
                            <template x-for="d in filteredDrivers" :key="d.id">
                                <div @click="select(d)" class="px-3 py-2 hover:bg-indigo-50 cursor-pointer" x-text="d.nama"></div>
                            </template>
                        </div>
                        <div x-show="open && search && filteredDrivers.length === 0" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg px-3 py-2 text-sm text-gray-400">
                            Tidak ditemukan
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <x-input-label :value="'Pilih Peti Sortasi'" />
                <p class="text-xs text-gray-500 mb-2">Satu surat jalan untuk peti dari gudang yang sama.</p>

                <input type="text" id="search_peti" placeholder="Cari kode peti, kandang, tanggal, atau jumlah butir..." class="block w-full mb-3 border-gray-300 rounded-md text-sm px-3 py-2">

                @forelse($petisByGudang as $gudangNama => $petis)
                <div class="mb-4 border rounded-lg overflow-hidden gudang-group" data-gudang="{{ $loop->index }}">
                    <div class="px-4 py-2 bg-gray-50 border-b text-sm font-semibold text-gray-700 gudang-title">{{ $gudangNama }}</div>
                    <div class="p-3 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 max-h-96 overflow-y-auto peti-list">
                        @foreach($petis as $p)
                        <label class="peti-label flex items-start gap-2 p-2 border rounded hover:bg-gray-50 cursor-pointer transition">
                            <input type="checkbox" name="peti_ids[]" value="{{ $p->id }}" class="peti-checkbox mt-1" data-butir="{{ $p->butir }}" data-berat="{{ $p->berat }}" data-karpet="{{ $p->karpet }}" data-kandang="{{ $p->kandang_nama ?? '-' }}" data-kode_peti="{{ $p->kode_peti ?? '-' }}" data-tanggal="{{ $p->tgl_sortir }}" data-gudang="{{ $loop->parent->index }}">
                            <div class="peti-text text-xs text-gray-700">
                                <p class="font-bold text-indigo-700">{{ $p->kode_peti ?? '-' }}</p>
                                <p class="font-medium">{{ number_format($p->butir) }} butir</p>
                                <p class="text-gray-500">{{ number_format($p->berat, 1) }} kg &middot; {{ $p->karpet }} karpet</p>
                                <p class="text-gray-400">{{ $p->kandang_nama ?? '-' }} &middot; {{ $p->tgl_sortir }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @empty
                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded text-sm text-yellow-700">Tidak ada peti sortasi yang tersedia untuk dialokasikan.</div>
                @endforelse
                <x-input-error :messages="$errors->get('peti_ids')" class="mt-2" />
            </div>

            <div id="selected_info" class="hidden border rounded-lg p-4 bg-indigo-50">
                <h3 class="text-sm font-semibold text-indigo-800 mb-2">Peti Terpilih</h3>
                <div class="max-h-48 overflow-y-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="text-indigo-700 font-semibold border-b border-indigo-200">
                            <tr><th class="py-1">Kode Peti</th><th class="py-1">Kandang</th><th class="py-1">Tanggal</th><th class="py-1 text-right">Butir</th><th class="py-1 text-right">Berat</th><th class="py-1 text-right">Karpet</th></tr>
                        </thead>
                        <tbody id="selected_info_body"></tbody>
                    </table>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-gray-50 p-4 rounded-lg">
                <div class="text-center">
                    <p class="text-xs text-gray-500">Peti</p>
                    <p class="text-lg font-bold text-gray-800" id="total_peti">0</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500">Butir</p>
                    <p class="text-lg font-bold text-indigo-600" id="total_butir">0</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500">Karpet</p>
                    <p class="text-lg font-bold text-amber-600" id="total_karpet">0</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500">Berat</p>
                    <p class="text-lg font-bold text-green-600" id="total_berat">0 kg</p>
                </div>
            </div>

            <div>
                <x-input-label :value="'Keterangan'" />
                <textarea name="keterangan" rows="2" class="block mt-1 w-full border-gray-300 rounded-md text-sm">{{ old('keterangan') }}</textarea>
            </div>

            <p class="text-xs text-gray-400">No. Surat Jalan akan dibuat otomatis.</p>
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('telur.keluar.index') }}" class="px-4 py-2 text-sm text-gray-700 bg-white border rounded-lg hover:bg-gray-50">Batal</a>
                <x-primary-button type="submit" id="btnSimpan" disabled>Simpan</x-primary-button>
            </div>
        </form>
    </div>
</div>
<script>
(function(){
    var checkboxes = document.querySelectorAll('.peti-checkbox');
    var labels = document.querySelectorAll('.peti-label');
    var gudangGroups = document.querySelectorAll('.gudang-group');
    var searchInput = document.getElementById('search_peti');
    var btnSimpan = document.getElementById('btnSimpan');
    var totalPeti = document.getElementById('total_peti');
    var totalButir = document.getElementById('total_butir');
    var totalKarpet = document.getElementById('total_karpet');
    var totalBerat = document.getElementById('total_berat');
    var selectedInfo = document.getElementById('selected_info');
    var selectedInfoBody = document.getElementById('selected_info_body');

    function formatNumber(n){
        return parseFloat(n).toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 2});
    }

    function updateSummary(){
        var checked = Array.from(checkboxes).filter(function(c){ return c.checked; });
        var peti = checked.length;
        var butir = checked.reduce(function(a, c){ return a + parseInt(c.dataset.butir); }, 0);
        var karpet = checked.reduce(function(a, c){ return a + parseInt(c.dataset.karpet); }, 0);
        var berat = checked.reduce(function(a, c){ return a + parseFloat(c.dataset.berat); }, 0);
        totalPeti.textContent = peti;
        totalButir.textContent = butir.toLocaleString('id-ID');
        totalKarpet.textContent = karpet.toLocaleString('id-ID');
        totalBerat.textContent = berat.toLocaleString('id-ID', {minimumFractionDigits: 1, maximumFractionDigits: 2}) + ' kg';
        btnSimpan.disabled = peti === 0;
        renderSelectedInfo(checked);
    }

    function renderSelectedInfo(checked){
        if(checked.length === 0){
            selectedInfo.classList.add('hidden');
            selectedInfoBody.innerHTML = '';
            return;
        }
        selectedInfo.classList.remove('hidden');
        selectedInfoBody.innerHTML = checked.map(function(c){
            return '<tr class="border-b border-indigo-100">' +
                '<td class="py-1 font-bold text-indigo-700">' + (c.dataset.kode_peti || '-') + '</td>' +
                '<td class="py-1">' + (c.dataset.kandang || '-') + '</td>' +
                '<td class="py-1">' + (c.dataset.tanggal || '-') + '</td>' +
                '<td class="py-1 text-right">' + formatNumber(c.dataset.butir) + '</td>' +
                '<td class="py-1 text-right">' + formatNumber(c.dataset.berat) + ' kg</td>' +
                '<td class="py-1 text-right">' + formatNumber(c.dataset.karpet) + '</td>' +
            '</tr>';
        }).join('');
    }

    function filterPeti(){
        var term = searchInput.value.toLowerCase().trim();
        labels.forEach(function(label){
            var text = label.querySelector('.peti-text').textContent.toLowerCase();
            label.style.display = text.indexOf(term) >= 0 ? '' : 'none';
        });
        gudangGroups.forEach(function(group){
            var visible = group.querySelectorAll('.peti-label[style*="display: none"]').length < group.querySelectorAll('.peti-label').length;
            group.style.display = visible ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterPeti);
    checkboxes.forEach(function(cb){
        cb.addEventListener('change', updateSummary);
    });
    updateSummary();
})();

function driverSelect() {
    var drivers = @json($dapters ?? []);
    return {
        drivers: drivers,
        search: '{{ old('driver', '') }}',
        selected: '{{ old('driver', '') }}',
        open: false,
        get filteredDrivers() {
            var term = this.search.toLowerCase().trim();
            if (!term) return this.drivers;
            return this.drivers.filter(function(d) {
                return d.nama.toLowerCase().indexOf(term) >= 0;
            });
        },
        select: function(d) {
            this.selected = d.nama;
            this.search = d.nama;
            this.open = false;
        }
    };
}
</script>
@endsection
