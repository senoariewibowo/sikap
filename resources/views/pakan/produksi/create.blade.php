@extends('layouts.admin')

@section('title', 'Tambah Produksi Pakan - SIKAP')
@section('page-title', 'Tambah Produksi Pakan')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Form Tambah Produksi Pakan</h2>
        </div>

        <form action="{{ route('pakan.produksi.store') }}" method="POST" class="p-6 space-y-4" id="produksiForm">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <x-input-label for="tanggal" :value="'Tanggal'" />
                    <x-text-input id="tanggal" name="tanggal" type="date" class="block mt-1 w-full" :value="old('tanggal', date('Y-m-d'))" required />
                    <x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="gudang_id" :value="'Gudang'" />
                    <select id="gudang_id" name="gudang_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                        <option value="">Pilih Gudang</option>
                        @foreach($gudangs as $g)
                        <option value="{{ $g->id }}" {{ old('gudang_id') == $g->id ? 'selected' : '' }}>{{ $g->nama_gudang }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('gudang_id')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="jumlah" :value="'Jumlah Hasil'" />
                    <x-text-input id="jumlah" name="jumlah" type="number" step="0.01" class="block mt-1 w-full" :value="old('jumlah')" required />
                    <x-input-error :messages="$errors->get('jumlah')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="pakan_id" :value="'Pakan Hasil'" />
                    <select id="pakan_id" name="pakan_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                        <option value="">Pilih Pakan</option>
                        @foreach($pakans as $p)
                        <option value="{{ $p->id }}" {{ old('pakan_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }} ({{ $p->satuan }})</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('pakan_id')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="resep_pakan_id" :value="'Resep'" />
                    <select id="resep_pakan_id" name="resep_pakan_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                        <option value="">Pilih Resep</option>
                    </select>
                    <x-input-error :messages="$errors->get('resep_pakan_id')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="keterangan" :value="'Keterangan (opsional)'" />
                <x-text-input id="keterangan" name="keterangan" class="block mt-1 w-full" :value="old('keterangan')" />
                <x-input-error :messages="$errors->get('keterangan')" class="mt-2" />
            </div>

            <div class="pt-4 border-t border-gray-200">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-sm font-semibold text-gray-700">Biaya Produksi Lain</h3>
                    <button type="button" id="addBiaya" class="px-3 py-1.5 text-sm bg-green-600 text-white rounded hover:bg-green-700">+ Tambah Biaya</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500" id="biayaTable">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-3 py-2">Nama Biaya</th>
                                <th class="px-3 py-2">Jumlah (Rp)</th>
                                <th class="px-3 py-2"></th>
                            </tr>
                        </thead>
                        <tbody id="biayaBody">
                            @if(old('biaya_lain'))
                                @foreach(old('biaya_lain') as $i => $b)
                                <tr class="biaya-row bg-white border-b">
                                    <td class="px-3 py-2">
                                        <input type="text" name="biaya_lain[{{ $i }}][nama_biaya]" value="{{ $b['nama_biaya'] ?? '' }}" class="w-full border-gray-300 rounded-md text-sm" required>
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" step="0.01" name="biaya_lain[{{ $i }}][jumlah]" value="{{ $b['jumlah'] ?? '' }}" class="w-full border-gray-300 rounded-md text-sm" required>
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <button type="button" class="remove-biaya text-red-600 hover:text-red-800">Hapus</button>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                @if($errors->has('biaya_lain'))
                <p class="text-red-600 text-sm mt-2">{{ $errors->first('biaya_lain') }}</p>
                @endif
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('pakan.produksi.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
                <x-primary-button>Simpan</x-primary-button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const pakanSelect = document.getElementById('pakan_id');
        const resepSelect = document.getElementById('resep_pakan_id');
        const oldResep = "{{ old('resep_pakan_id') }}";

        function loadResep(pakanId) {
            resepSelect.innerHTML = '<option value="">Pilih Resep</option>';
            if (!pakanId) return;

            fetch(`{{ url('/pakan/resep/by-pakan') }}/${pakanId}`)
                .then(r => r.json())
                .then(data => {
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.text = item.nama_resep + (item.is_default ? ' (Default)' : '');
                        if (oldResep == item.id) option.selected = true;
                        resepSelect.appendChild(option);
                    });
                });
        }

        pakanSelect.addEventListener('change', function () {
            loadResep(this.value);
        });

        if (pakanSelect.value) {
            loadResep(pakanSelect.value);
        }

        // Biaya lain
        const tbody = document.getElementById('biayaBody');
        const addBiaya = document.getElementById('addBiaya');

        function updateBiayaIndex() {
            tbody.querySelectorAll('.biaya-row').forEach((row, index) => {
                row.querySelector('input[type="text"]').name = `biaya_lain[${index}][nama_biaya]`;
                row.querySelector('input[type="number"]').name = `biaya_lain[${index}][jumlah]`;
            });
        }

        addBiaya.addEventListener('click', function () {
            const index = tbody.querySelectorAll('.biaya-row').length;
            const tr = document.createElement('tr');
            tr.className = 'biaya-row bg-white border-b';
            tr.innerHTML = `
                <td class="px-3 py-2">
                    <input type="text" name="biaya_lain[${index}][nama_biaya]" class="w-full border-gray-300 rounded-md text-sm" required>
                </td>
                <td class="px-3 py-2">
                    <input type="number" step="0.01" name="biaya_lain[${index}][jumlah]" class="w-full border-gray-300 rounded-md text-sm" required>
                </td>
                <td class="px-3 py-2 text-center">
                    <button type="button" class="remove-biaya text-red-600 hover:text-red-800">Hapus</button>
                </td>
            `;
            tbody.appendChild(tr);
            updateBiayaIndex();
        });

        tbody.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-biaya')) {
                e.target.closest('.biaya-row').remove();
                updateBiayaIndex();
            }
        });
    });
</script>
@endsection
