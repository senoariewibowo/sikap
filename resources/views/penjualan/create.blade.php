@extends('layouts.admin')

@section('title', 'Buat Penjualan - SIKAP')
@section('page-title', 'Buat Transaksi Penjualan')

@section('content')
<div class="max-w-4xl mx-auto" x-data="customerSelect()">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b"><h2 class="text-lg font-semibold text-gray-800">Buat Transaksi Penjualan</h2></div>
        <form action="{{ route('penjualan.store') }}" method="POST" class="p-6 space-y-4" id="formPenjualan">
            @if($selectedSj)
            <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-3 text-sm text-indigo-800">
                <p>Surat Jalan aktif: <strong>{{ $selectedSj->no_referensi ?: '-' }}</strong> &middot; {{ $selectedSj->gudang->nama_gudang ?? '-' }} &middot; {{ $selectedSj->tanggal->format('d/m/Y') }}</p>
            </div>
            @endif
            @csrf
            <input type="hidden" name="satuan" value="per_peti">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label :value="'Customer'" />
                    <div class="relative mt-1">
                        <input type="text" x-model="customerSearch" @focus="customerOpen = true" @click.away="customerOpen = false" @input="customerOpen = true"
                            class="block w-full border-gray-300 rounded-md text-sm px-3 py-1.5 pr-10 focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Cari customer...">
                        <input type="hidden" name="customer_id" x-model="customerId">
                        <button type="button" @click="clearCustomer()" x-show="customerId || customerSearch || isNewCustomer"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500 focus:outline-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                        <div x-show="customerOpen && filteredCustomers.length > 0" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-48 overflow-y-auto text-sm">
                            <template x-for="c in filteredCustomers" :key="c.id">
                                <div @click="selectCustomer(c)" class="px-3 py-2 hover:bg-indigo-50 cursor-pointer">
                                    <span x-text="c.nama_customer + (c.alamat ? ' (' + c.alamat + ')' : '')"></span>
                                </div>
                            </template>
                        </div>
                        <div x-show="customerOpen && customerSearch && filteredCustomers.length === 0 && !isNewCustomer" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg px-3 py-2 text-sm text-gray-400">
                            Tidak ditemukan. Klik tombol "+ Customer Baru" untuk menambahkan.
                        </div>
                    </div>
                    <div class="mt-2">
                        <button type="button" @click="toggleNewCustomer()" class="text-xs text-indigo-600 hover:underline">
                            <span x-show="!isNewCustomer">+ Customer Baru</span>
                            <span x-show="isNewCustomer">Pilih Customer yang Ada</span>
                        </button>
                    </div>
                    <div x-show="isNewCustomer" class="mt-2 space-y-2">
                        <input type="text" name="customer_nama_baru" x-model="newCustomerName" @input="customerId = ''"
                            class="block w-full border-gray-300 rounded-md text-sm px-3 py-1.5 focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Nama customer baru">
                        <textarea name="customer_alamat_baru" x-model="newCustomerAlamat" rows="2"
                            class="block w-full border-gray-300 rounded-md text-sm px-3 py-1.5 focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Alamat customer baru"></textarea>
                    </div>
                    <x-input-error :messages="$errors->get('customer_id')" class="mt-1" />
                    <x-input-error :messages="$errors->get('customer_nama_baru')" class="mt-1" />
                    <x-input-error :messages="$errors->get('customer_alamat_baru')" class="mt-1" />
                </div>
                <div>
                    <x-input-label :value="'Tanggal'" />
                    <x-text-input name="tanggal" type="date" class="block mt-1 w-full" value="{{ old('tanggal', now()->format('Y-m-d')) }}" required />
                    <x-input-error :messages="$errors->get('tanggal')" class="mt-1" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <x-input-label :value="'Harga per Peti (Rp)'" />
                    <x-text-input id="harga_per_satuan" name="harga_per_satuan" type="text" data-type="rupiah" autocomplete="off" class="block mt-1 w-full" :value="old('harga_per_satuan')" required />
                    <p id="harga_info" class="mt-1 text-xs text-gray-500"></p>
                    <x-input-error :messages="$errors->get('harga_per_satuan')" class="mt-1" />
                </div>
                <div>
                    <x-input-label :value="'Total Harga (Rp)'" />
                    <x-text-input id="total_harga" type="text" class="block mt-1 w-full bg-gray-100" value="Rp 0" readonly />
                </div>
                <div>
                    <x-input-label :value="'Jumlah Peti'" />
                    <x-text-input id="jumlah_satuan" type="number" class="block mt-1 w-full bg-gray-100" :value="old('jumlah_satuan', 0)" readonly />
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Pilih Peti (Surat Jalan)</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm mb-4">
                    <div class="bg-white rounded p-3 text-center">
                        <p class="text-gray-500 text-xs">Butir</p>
                        <p class="text-xl font-bold text-gray-800">{{ number_format($totalGudang['butir']) }}</p>
                    </div>
                    <div class="bg-white rounded p-3 text-center">
                        <p class="text-gray-500 text-xs">Berat</p>
                        <p class="text-xl font-bold text-gray-800">{{ number_format($totalGudang['berat'], 1) }} kg</p>
                    </div>
                    <div class="bg-white rounded p-3 text-center">
                        <p class="text-gray-500 text-xs">Peti</p>
                        <p class="text-xl font-bold text-gray-800">{{ number_format($totalGudang['peti']) }}</p>
                    </div>
                </div>

                @if($stokDetail->isNotEmpty())
                <div id="detailSection" class="space-y-4">
                    <p class="text-xs text-gray-500 font-medium">Pilih peti untuk dijual (1 baris = 1 peti)</p>
                    @foreach($stokDetail as $gudangNama => $bySJ)
                    <div class="border rounded-lg overflow-hidden">
                        <div class="px-3 py-2 bg-gray-100 text-xs font-semibold text-gray-700">{{ $gudangNama }}</div>
                        <div class="p-3 space-y-4">
                            @foreach($bySJ as $sjId => $details)
                            @php $sj = $details->first()->stokTelurKeluar; @endphp
                            <div>
                                <p class="text-xs text-gray-500 mb-2">{{ $sj->no_referensi ?: '-' }} &middot; {{ $sj->tanggal->format('d/m/Y') }} &middot; {{ $sj->driver ?: '-' }}</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach($details as $d)
                                    <label class="detail-card flex items-start gap-3 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer transition {{ $errors->has('detail_qty.'.$d->id) ? 'border-red-300 bg-red-50' : '' }}">
                                        <input type="checkbox" class="detail-checkbox mt-1.5" name="detail_qty[{{ $d->id }}]" value="1" data-id="{{ $d->id }}" data-butir="{{ $d->sisa }}" data-berat="{{ $d->berat_kg }}" data-kode="{{ $d->sortasiTelurDetail->kode_peti ?? '-' }}" data-sj="{{ $sj->no_referensi ?: '-' }}" data-gudang="{{ $gudangNama }}" {{ old('detail_qty.'.$d->id)==1 ? 'checked' : '' }}>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="inline-flex items-center px-2 py-1 bg-indigo-100 text-indigo-800 text-xs font-semibold rounded">
                                                    Peti {{ $d->sortasiTelurDetail->kode_peti ?? '-' }}
                                                </span>
                                                @if($d->keterangan && str_contains($d->keterangan, 'Carryover'))
                                                <span class="ml-2 inline-flex items-center px-2 py-1 bg-orange-100 text-orange-800 text-xs font-semibold rounded">
                                                    {{ $d->keterangan }}
                                                </span>
                                                @endif
                                                <span class="text-xs text-gray-500">{{ $d->sisa }} butir &middot; {{ number_format($d->berat_kg, 1) }} kg</span>
                                            </div>
                                            <div class="text-xs text-gray-500 space-y-0.5">
                                                <p><span class="font-medium text-gray-700">SJ:</span> {{ $sj->no_referensi ?: '-' }} &middot; {{ $sj->tanggal->format('d/m/Y') }}</p>
                                                <p><span class="font-medium text-gray-700">Gudang:</span> {{ $gudangNama }}</p>
                                                <p><span class="font-medium text-gray-700">Driver:</span> {{ $sj->driver ?: '-' }}</p>
                                            </div>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded text-sm text-yellow-700">Tidak ada peti tersedia untuk dijual.</div>
                @endif

                <div id="selectedSummary" class="hidden mt-4">
                    <h4 class="text-xs font-semibold text-gray-700 mb-2">Peti yang Dipilih</h4>
                    <div class="overflow-x-auto border rounded-lg bg-white">
                        <table class="w-full text-xs text-left text-gray-500">
                            <thead class="bg-gray-100 text-gray-700"><tr><th class="px-3 py-2">No</th><th class="px-3 py-2">Kode Peti</th><th class="px-3 py-2">SJ</th><th class="px-3 py-2">Gudang</th><th class="px-3 py-2">Butir</th><th class="px-3 py-2">Berat</th></tr></thead>
                            <tbody id="selectedSummaryBody"></tbody>
                        </table>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('detail_qty')" class="mt-2" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label :value="'Status Pembayaran'" />
                    <select id="status_pembayaran" name="status_pembayaran" class="block mt-1 w-full border-gray-300 rounded-md text-sm" required>
                        <option value="belum_lunas" {{ old('status_pembayaran','belum_lunas')=='belum_lunas'?'selected':'' }}>Belum Lunas</option>
                        <option value="lunas" {{ old('status_pembayaran')=='lunas'?'selected':'' }}>Lunas</option>
                    </select>
                </div>
                <div>
                    <x-input-label :value="'Metode Pembayaran'" />
                    <select id="metode_pembayaran" name="metode_pembayaran" class="block mt-1 w-full border-gray-300 rounded-md text-sm" required>
                        <option value="tunai" {{ old('metode_pembayaran','tunai')=='tunai'?'selected':'' }}>Tunai</option>
                        <option value="transfer" {{ old('metode_pembayaran')=='transfer'?'selected':'' }}>Transfer</option>
                    </select>
                </div>
            </div>

            <div id="dpField" class="{{ old('status_pembayaran', 'belum_lunas')=='lunas'?'hidden':'' }}">
                <x-input-label :value="'Nominal DP / Uang Muka (Rp)'" />
                <x-text-input name="dp" type="text" data-type="rupiah" autocomplete="off" class="block mt-1 w-full" :value="old('dp')" />
            </div>

            <div>
                <x-input-label :value="'Catatan Pembayaran'" />
                <textarea name="catatan_pembayaran" rows="2" class="block mt-1 w-full border-gray-300 rounded-md text-sm" placeholder="Contoh: Bank BCA 123456789 a/n Driver X">{{ old('catatan_pembayaran') }}</textarea>
            </div>

            <p class="text-xs text-gray-400">No. Invoice akan dibuat otomatis.</p>

            <div class="flex justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('penjualan.index') }}" class="px-4 py-2 text-sm text-gray-700 bg-white border rounded-lg">Batal</a>
                <x-primary-button type="submit" id="btnSimpan" disabled>Simpan Transaksi</x-primary-button>
            </div>
        </form>
    </div>
</div>

<script>
(function(){
    var form = document.getElementById('formPenjualan');
    var hargaInput = document.getElementById('harga_per_satuan');
    var hargaInfo = document.getElementById('harga_info');
    var totalInput = document.getElementById('total_harga');
    var jumlahSatuanInput = document.getElementById('jumlah_satuan');
    var btnSimpan = document.getElementById('btnSimpan');
    var statusSelect = document.getElementById('status_pembayaran');
    var dpField = document.getElementById('dpField');
    var selectedSummary = document.getElementById('selectedSummary');
    var selectedSummaryBody = document.getElementById('selectedSummaryBody');
    var prices = @json($prices);

    function parseRupiah(v){
        return parseInt((v || '').replace(/\D/g, '')) || 0;
    }

    function formatRupiah(n){
        return 'Rp ' + parseInt(n).toLocaleString('id-ID');
    }

    function getDefaultPrice(customerId){
        customerId = customerId != null ? String(customerId) : (document.querySelector('[name="customer_id"]').value || '0');
        var price = prices[customerId] ? parseInt(prices[customerId]) : null;
        if(!price && prices['0']) price = parseInt(prices['0']);
        return price;
    }

    function updateHarga(customerId){
        var price = getDefaultPrice(customerId);
        if (price) {
            hargaInfo.textContent = 'Harga default per peti: Rp ' + price.toLocaleString('id-ID') + '. Boleh diubah.';
        } else {
            hargaInfo.textContent = 'Belum ada harga default per peti. Isi manual.';
        }
    }

    function applyDefaultPrice(force, customerId){
        var price = getDefaultPrice(customerId);
        if(price && (force || !hargaInput.value.replace(/\D/g, ''))){
            hargaInput.value = price.toLocaleString('id-ID');
        }
    }

    function updateTotals(){
        var jumlahSatuan = 0;
        var totalButir = 0;
        var rows = [];
        form.querySelectorAll('.detail-checkbox:checked').forEach(function(cb, idx){
            jumlahSatuan += 1;
            totalButir += parseInt(cb.dataset.butir) || 0;
            rows.push({
                no: idx + 1,
                kode: cb.dataset.kode || '-',
                sj: cb.dataset.sj || '-',
                gudang: cb.dataset.gudang || '-',
                butir: parseInt(cb.dataset.butir) || 0,
                berat: parseFloat(cb.dataset.berat) || 0,
            });
        });
        jumlahSatuanInput.value = jumlahSatuan;
        var harga = parseRupiah(hargaInput.value);
        totalInput.value = formatRupiah(jumlahSatuan * harga);
        btnSimpan.disabled = jumlahSatuan === 0;

        selectedSummary.classList.toggle('hidden', rows.length === 0);
        selectedSummaryBody.innerHTML = rows.map(function(r){
            return '<tr class="border-b"><td class="px-3 py-2">' + r.no + '</td><td class="px-3 py-2 font-semibold text-indigo-700">' + r.kode + '</td><td class="px-3 py-2">' + r.sj + '</td><td class="px-3 py-2">' + r.gudang + '</td><td class="px-3 py-2">' + r.butir.toLocaleString('id-ID') + '</td><td class="px-3 py-2">' + r.berat.toLocaleString('id-ID', {minimumFractionDigits: 1, maximumFractionDigits: 1}) + ' kg</td></tr>';
        }).join('');
    }

    form.querySelectorAll('.detail-checkbox').forEach(function(cb){
        cb.addEventListener('change', updateTotals);
    });

    hargaInput.addEventListener('input', updateTotals);
    statusSelect.addEventListener('change', function(){
        dpField.classList.toggle('hidden', this.value === 'lunas');
    });

    form.addEventListener('submit', function(e){
        hargaInput.value = parseRupiah(hargaInput.value);
        var dpInput = form.querySelector('[name="dp"]');
        if(dpInput) dpInput.value = parseRupiah(dpInput.value);
    });

    window.addEventListener('customerSelected', function(e){
        var customerId = e.detail && e.detail.customerId !== undefined ? e.detail.customerId : null;
        applyDefaultPrice(true, customerId);
        updateHarga(customerId);
        updateTotals();
    });
    applyDefaultPrice(false);
    updateHarga();
    updateTotals();
})();

function customerSelect(){
    var customers = @json($customers);
    var oldCustomerId = '{{ old('customer_id') }}';
    return {
        customers: customers,
        customerSearch: '',
        customerId: oldCustomerId,
        customerOpen: false,
        isNewCustomer: false,
        newCustomerName: '{{ old('customer_nama_baru') }}',
        newCustomerAlamat: '{{ old('customer_alamat_baru') }}',
        get filteredCustomers(){
            var term = this.customerSearch.toLowerCase().trim();
            if(!term) return this.customers;
            return this.customers.filter(function(c){
                return c.nama_customer.toLowerCase().indexOf(term) >= 0;
            });
        },
        selectCustomer: function(c){
            this.customerId = c.id;
            this.customerSearch = c.nama_customer + (c.alamat ? ' (' + c.alamat + ')' : '');
            this.customerOpen = false;
            this.isNewCustomer = false;
            var hidden = document.querySelector('input[name="customer_id"]');
            if (hidden) hidden.value = c.id;
            window.dispatchEvent(new CustomEvent('customerSelected', { detail: { customerId: c.id } }));
        },
        toggleNewCustomer: function(){
            this.isNewCustomer = !this.isNewCustomer;
            if(this.isNewCustomer){
                this.customerId = '';
                this.customerSearch = '';
            } else {
                this.newCustomerName = '';
                this.newCustomerAlamat = '';
            }
        },
        clearCustomer: function(){
            this.customerId = '';
            this.customerSearch = '';
            this.newCustomerName = '';
            this.newCustomerAlamat = '';
            this.isNewCustomer = false;
            this.customerOpen = false;
            var hidden = document.querySelector('input[name="customer_id"]');
            if (hidden) hidden.value = '';
            document.getElementById('harga_per_satuan').value = '';
            window.dispatchEvent(new CustomEvent('customerSelected', { detail: { customerId: '' } }));
        }
    };
}
</script>
@endsection
