@extends('layouts.admin')

@section('title', 'Sortasi Telur - SIKAP')
@section('page-title', 'Input Sortasi Telur')

@section('content')
<div class="max-w-4xl mx-auto" x-data="sortasiForm()" x-init="init()">
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">
                        {{ $isSisaProduksi ? 'Sisa Sortir Shift ' . ucfirst($shift) . ' (' . $gudang->nama_gudang . ')' : 'Sortasi Shift ' . ucfirst($shift) }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}
                        @if(!$isSisaProduksi) &middot; {{ $gudang->nama_gudang }} &middot; {{ $kandang->nama_kandang }} @endif
                    </p>
                </div>
                <a href="{{ route('setoran.review', ['tanggal' => $tanggal, 'shift' => $shift, 'gudang_id' => $gudangId]) }}"
                   class="px-3 py-1.5 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                    &larr; Kembali
                </a>
            </div>
        </div>

        <div class="p-6 border-b border-gray-200 bg-gray-50">
            <div class="flex flex-wrap gap-4 text-sm">
                <div>
                    <span class="text-gray-500">Setoran Masuk:</span>
                    <span class="font-bold text-gray-800 ml-1">{{ number_format($totalButirMasuk) }} butir</span>
                </div>
                @if($isSisaProduksi && $sisaSebelumnya > 0)
                <div>
                    <span class="text-gray-500">Sisa {{ $shift === 'sore' ? 'Shift Siang' : 'Shift Sore Kemarin' }}:</span>
                    <span class="font-bold text-indigo-600 ml-1">{{ number_format($sisaSebelumnya) }} butir</span>
                </div>
                @endif
            </div>
        </div>

        @if(isset($produksi) && $produksi && $produksi->fotos->isNotEmpty())
        <div class="px-6 py-3 border-b border-gray-200">
            <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-2">Foto Produksi ({{ $produksi->fotos->count() }} foto)</p>
            <div class="flex gap-2 overflow-x-auto pb-1">
                @foreach($produksi->fotos as $f)
                <img src="{{ $f->url }}" class="w-16 h-16 object-cover rounded cursor-pointer border shrink-0" onclick="openImageModal('{{ $f->url }}')">
                @endforeach
            </div>
        </div>
        @endif

        @if(($existingSisaSortir ?? false) && !$isSisaProduksi)
        <div class="bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 mx-6 mt-3 mb-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>
                <p class="text-sm text-amber-800 font-medium">Sisa Sortir (Campuran) sudah disortir</p>
                <p class="text-xs text-amber-600 mt-0.5">Setelah edit sortasi ini, silakan <strong>sortir ulang Sisa Sortir</strong> agar data tetap sinkron.</p>
            </div>
        </div>
        @endif

        <form action="{{ route('setoran.simpanSortasi') }}" method="POST" x-on:submit="prepareSubmit">
            @csrf
            @method('PUT')

            <input type="hidden" name="tanggal" value="{{ $tanggal }}">
            <input type="hidden" name="shift" value="{{ $shift }}">
            <input type="hidden" name="gudang_id" value="{{ $gudangId }}">
            @if(!$isSisaProduksi)<input type="hidden" name="kandang_id" value="{{ $kandangId }}">@endif
            <input type="hidden" name="total_masuk" x-bind:value="totalMasuk">
            @php
                $initialPrefix = $isSisaProduksi ? 'SS' : ($kandang->initial ?? 'XX');
                $day = \Carbon\Carbon::parse($tanggal)->format('d');
            @endphp

            <div class="p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Timbang Per Peti (15 kg / peti)</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 w-12">Peti</th>
                                <th class="px-3 py-2">Kode Peti</th>
                                <th class="px-3 py-2">Butir</th>
                                <th class="px-3 py-2">Karpet</th>
                                <th class="px-3 py-2">Berat (kg)</th>
                                <th class="px-3 py-2 w-12">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(d, i) in detail" :key="i">
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-3 py-2 font-medium text-gray-900" x-text="i + 1"></td>
                                    <td class="px-3 py-2">
                                        <span class="inline-block px-2 py-1 text-xs font-bold bg-indigo-100 text-indigo-700 rounded" x-text="d.kode_peti"></span>
                                        <input type="hidden" :name="'detail['+i+'][kode_peti]'" :value="d.kode_peti">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" :name="'detail['+i+'][butir]'" x-model="d.butir" min="0" placeholder="0"
                                            class="w-full border-gray-300 rounded-md text-sm px-3 py-1.5 focus:border-indigo-500 focus:ring-indigo-500"
                                            x-on:input="hitungTotal()">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" :name="'detail['+i+'][karpet]'" x-model="d.karpet" min="0" placeholder="0"
                                            class="w-full border-gray-300 rounded-md text-sm px-3 py-1.5 focus:border-indigo-500 focus:ring-indigo-500"
                                            x-on:input="hitungTotal()">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" :name="'detail['+i+'][berat]'" x-model="d.berat" min="0" step="0.01" placeholder="15"
                                            class="w-20 border-gray-300 rounded-md text-sm px-3 py-1.5 focus:border-indigo-500 focus:ring-indigo-500"
                                            x-on:input="hitungTotal()">
                                    </td>
                                    <td class="px-3 py-2">
                                        <button type="button" x-on:click="hapusPeti(i)"
                                            class="p-1 text-red-500 hover:bg-red-50 rounded"
                                            x-show="detail.length > 1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <button type="button" x-on:click="tambahPeti()"
                    class="mt-3 inline-flex items-center px-3 py-1.5 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Peti
                </button>

                <div class="mt-4 bg-gray-50 rounded-lg p-4 grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="text-center">
                        <p class="text-xs text-gray-500">Peti</p>
                        <p class="text-lg font-bold text-gray-800" x-text="detail.length"></p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-500">Butir Tersortir</p>
                        <p class="text-lg font-bold"
                           :class="tersortirMelebihi ? 'text-red-600' : 'text-indigo-600'"
                           x-text="formatNumber(totalButir)"></p>
                        <p x-show="tersortirMelebihi" class="text-xs text-red-500 mt-1 font-medium">Melebihi butir masuk!</p>
                        <p x-show="adaPetiKosong && !tersortirMelebihi" class="text-xs text-red-500 mt-1 font-medium">Semua peti wajib diisi butir!</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-500">Karpet</p>
                        <p class="text-lg font-bold text-green-600" x-text="formatNumber(totalKarpet)"></p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-500">Berat (kg)</p>
                        <p class="text-lg font-bold text-sky-600" x-text="totalBerat.toFixed(2)"></p>
                    </div>
                </div>
            </div>

            @if($isSisaProduksi)
            <div class="p-6 border-t border-gray-200 space-y-4">
                <h3 class="text-sm font-semibold text-gray-700">Sortasi Cacat</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Pecah</label>
                        <input type="number" name="pecah" x-model="pecah" min="0" placeholder="0"
                            class="w-full border-gray-300 rounded-md text-sm px-3 py-1.5">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Retak</label>
                        <input type="number" name="retak" x-model="retak" min="0" placeholder="0"
                            class="w-full border-gray-300 rounded-md text-sm px-3 py-1.5">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Kopong</label>
                        <input type="number" name="kopong" x-model="kopong" min="0" placeholder="0"
                            class="w-full border-gray-300 rounded-md text-sm px-3 py-1.5">
                    </div>
                </div>

                <div>
                    <label class="block text-xs text-gray-600 mb-1">Catatan</label>
                    <textarea name="catatan" rows="2"
                        class="w-full border-gray-300 rounded-md text-sm px-3 py-1.5"
                        placeholder="Opsional...">{{ old('catatan', $sortasi->catatan ?? '') }}</textarea>
                </div>

                <div :class="anyError ? 'bg-red-50 rounded-lg p-4 border border-red-200' : 'bg-indigo-50 rounded-lg p-4'">
                    <div x-show="anyError" class="space-y-2 text-sm text-red-700 font-medium py-2">
                        <p x-show="tersortirMelebihi">Butir tersortir melebihi butir masuk. Silakan periksa input peti.</p>
                        <p x-show="defectMelebihi">Total cacat (pecah + retak + kopong) + tersortir melebihi butir masuk.</p>
                    </div>
                    <div x-show="!anyError" class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                        <div>
                            <span class="text-gray-600">Telur Bagus:</span>
                            <span class="font-bold text-gray-800 ml-1" x-text="formatNumber(hitungBagus())"></span>
                            <span class="text-xs text-gray-400">= Tersortir + Sisa</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Sisa:</span>
                            <span class="font-bold text-orange-600 ml-1" x-text="formatNumber(hitungSisa())"></span>
                            <span class="text-xs text-gray-400">→ dikumpulkan di Sisa Sortir</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Stok Tersedia:</span>
                            <span class="font-bold text-indigo-600 ml-1" x-text="formatNumber(totalButir)"></span>
                            <span class="text-xs text-gray-400">(dalam peti)</span>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="p-6 border-t border-gray-200">
                <div :class="tersortirMelebihi ? 'bg-red-50 rounded-lg p-4 border border-red-200' : 'bg-indigo-50 rounded-lg p-4'">
                    <div x-show="tersortirMelebihi" class="text-sm text-red-700 font-medium py-2 text-center">
                        Butir tersortir melebihi butir masuk. Silakan periksa input peti.
                    </div>
                    <div x-show="!tersortirMelebihi" class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                        <div>
                            <span class="text-gray-600">Telur Bagus:</span>
                            <span class="font-bold text-gray-800 ml-1" x-text="formatNumber(hitungBagus())"></span>
                            <span class="text-xs text-gray-400">= Tersortir + Sisa</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Sisa:</span>
                            <span class="font-bold text-orange-600 ml-1" x-text="formatNumber(hitungSisa())"></span>
                            <span class="text-xs text-gray-400">→ dikumpulkan di Sisa Sortir</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Stok Tersedia:</span>
                            <span class="font-bold text-indigo-600 ml-1" x-text="formatNumber(totalButir)"></span>
                            <span class="text-xs text-gray-400">(dalam peti)</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <a href="{{ route('setoran.review', ['tanggal' => $tanggal, 'shift' => $shift, 'gudang_id' => $gudangId]) }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit"
                    :disabled="anyError"
                    :class="anyError ? 'px-4 py-2 text-sm font-medium text-white bg-gray-400 rounded-lg cursor-not-allowed' : 'px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700'">
                    Simpan Sortasi
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function sortasiForm() {
    @php
    $initialPrefix = $initialPrefix;
    $day = $day;
    $existingDetail = $sortasi ? $sortasi->detail->map(function($d) {
        return [
            'kode_peti' => $d->kode_peti ?? '',
            'butir' => $d->butir > 0 ? strval($d->butir) : '',
            'karpet' => $d->karpet > 0 ? strval($d->karpet) : '',
            'berat' => $d->berat > 0 ? strval($d->berat) : '',
        ];
    }) : [['kode_peti' => $initialPrefix . '-' . $day . '-01', 'butir' => '', 'karpet' => '9', 'berat' => '15']];
    @endphp
    var existing = @json($existingDetail);
    var initialPrefix = '{{ $initialPrefix }}';
    var day = '{{ $day }}';
    var pecahVal = {{ (int)old('pecah', $sortasi->pecah ?? 0) }};
    var retakVal = {{ (int)old('retak', $sortasi->retak ?? 0) }};
    var kopongVal = {{ (int)old('kopong', $sortasi->kopong ?? 0) }};
    return {
        detail: existing,
        pecah: pecahVal > 0 ? String(pecahVal) : '',
        retak: retakVal > 0 ? String(retakVal) : '',
        kopong: kopongVal > 0 ? String(kopongVal) : '',
        totalMasuk: {{ $totalButirMasuk }},

        get totalButir() {
            return this.detail.reduce(function(a, b) { return a + (parseInt(b.butir) || 0); }, 0);
        },
        get totalKarpet() {
            return this.detail.reduce(function(a, b) { return a + (parseInt(b.karpet) || 0); }, 0);
        },
        get totalBerat() {
            return this.detail.reduce(function(a, b) { return a + (parseFloat(b.berat) || 0); }, 0);
        },
        get tersortirMelebihi() {
            return this.totalButir > this.totalMasuk;
        },
        get totalDefect() {
            return (parseInt(this.pecah) || 0) + (parseInt(this.retak) || 0) + (parseInt(this.kopong) || 0);
        },
        get defectMelebihi() {
            return (this.totalButir + this.totalDefect) > this.totalMasuk;
        },
        get adaPetiKosong() {
            return this.detail.some(function(d) { return !(parseInt(d.butir) > 0); });
        },
        get anyError() {
            return this.tersortirMelebihi || this.defectMelebihi || this.adaPetiKosong;
        },

        init() {
            this.generateKodePeti();
        },
        generateKodePeti() {
            var usedNumbers = [];
            this.detail.forEach(function(d) {
                if (d.kode_peti) {
                    var parts = d.kode_peti.split('-');
                    var num = parseInt(parts[parts.length - 1], 10);
                    if (!isNaN(num)) usedNumbers.push(num);
                }
            });
            var next = 1;
            this.detail.forEach(function(d) {
                if (!d.kode_peti || d.kode_peti === '') {
                    while (usedNumbers.includes(next)) next++;
                    d.kode_peti = initialPrefix + '-' + day + '-' + String(next).padStart(2, '0');
                    usedNumbers.push(next);
                }
            });
        },
        tambahPeti() {
            this.detail.push({ kode_peti: '', butir: '', karpet: '9', berat: '15' });
            this.generateKodePeti();
        },
        hapusPeti(i) {
            if (this.detail.length > 1) this.detail.splice(i, 1);
            this.generateKodePeti();
        },
        hitungTotal() {},
        hitungBagus() {
            return this.totalButir + this.hitungSisa();
        },
        hitungSisa() {
            var pecah = parseInt(this.pecah) || 0;
            var retak = parseInt(this.retak) || 0;
            var kopong = parseInt(this.kopong) || 0;
            return Math.max(0, this.totalMasuk - pecah - retak - kopong - this.totalButir);
        },
        formatNumber(n) {
            return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        },
        prepareSubmit() {}
    };
}
</script>
@endpush

@include('components.image-modal')
@endsection
