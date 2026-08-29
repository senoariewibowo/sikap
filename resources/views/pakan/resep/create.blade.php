@extends('layouts.admin')

@section('title', 'Tambah Resep Pakan - SIKAP')
@section('page-title', 'Tambah Resep Pakan')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Form Tambah Resep Pakan</h2>
        </div>

        <form action="{{ route('pakan.resep.store') }}" method="POST" class="p-6 space-y-4" id="resepForm">
            @csrf

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
                    <x-input-label for="nama_resep" :value="'Nama Resep'" />
                    <x-text-input id="nama_resep" name="nama_resep" class="block mt-1 w-full" :value="old('nama_resep')" required />
                    <x-input-error :messages="$errors->get('nama_resep')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-center">
                    <input id="is_default" name="is_default" type="checkbox" value="1" {{ old('is_default') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <label for="is_default" class="ml-2 text-sm text-gray-700">Jadikan resep default</label>
                    <x-input-error :messages="$errors->get('is_default')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="status" :value="'Status'" />
                    <select id="status" name="status" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                        <option value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="keterangan" :value="'Keterangan (opsional)'" />
                    <x-text-input id="keterangan" name="keterangan" class="block mt-1 w-full" :value="old('keterangan')" />
                    <x-input-error :messages="$errors->get('keterangan')" class="mt-2" />
                </div>
            </div>

            <div class="pt-4 border-t border-gray-200">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-sm font-semibold text-gray-700">Bahan Resep</h3>
                    <button type="button" id="addRow" class="px-3 py-1.5 text-sm bg-green-600 text-white rounded hover:bg-green-700">+ Tambah Bahan</button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500" id="detailsTable">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-3 py-2">Bahan</th>
                                <th class="px-3 py-2">Jumlah per Satuan Pakan</th>
                                <th class="px-3 py-2">Catatan</th>
                                <th class="px-3 py-2"></th>
                            </tr>
                        </thead>
                        <tbody id="detailsBody">
                            @if(old('details'))
                                @foreach(old('details') as $i => $d)
                                <tr class="detail-row bg-white border-b">
                                    <td class="px-3 py-2">
                                        <select name="details[{{ $i }}][bahan_pakan_id]" class="w-full border-gray-300 rounded-md text-sm" required>
                                            <option value="">Pilih Bahan</option>
                                            @foreach($bahans as $b)
                                            <option value="{{ $b->id }}" {{ ($d['bahan_pakan_id'] ?? '') == $b->id ? 'selected' : '' }}>{{ $b->nama }} ({{ $b->satuan }})</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" step="0.01" name="details[{{ $i }}][jumlah]" value="{{ $d['jumlah'] ?? '' }}" class="w-full border-gray-300 rounded-md text-sm" required>
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="text" name="details[{{ $i }}][catatan]" value="{{ $d['catatan'] ?? '' }}" class="w-full border-gray-300 rounded-md text-sm">
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <button type="button" class="remove-row text-red-600 hover:text-red-800">Hapus</button>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr class="detail-row bg-white border-b">
                                    <td class="px-3 py-2">
                                        <select name="details[0][bahan_pakan_id]" class="w-full border-gray-300 rounded-md text-sm" required>
                                            <option value="">Pilih Bahan</option>
                                            @foreach($bahans as $b)
                                            <option value="{{ $b->id }}">{{ $b->nama }} ({{ $b->satuan }})</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" step="0.01" name="details[0][jumlah]" class="w-full border-gray-300 rounded-md text-sm" required>
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="text" name="details[0][catatan]" class="w-full border-gray-300 rounded-md text-sm">
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <button type="button" class="remove-row text-red-600 hover:text-red-800">Hapus</button>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                @if($errors->has('details'))
                <p class="text-red-600 text-sm mt-2">{{ $errors->first('details') }}</p>
                @endif
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('pakan.resep.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
                <x-primary-button>Simpan</x-primary-button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const bahans = `@foreach($bahans as $b)<option value="{{ $b->id }}">{{ $b->nama }} ({{ $b->satuan }})</option>@endforeach`;
        const tbody = document.getElementById('detailsBody');
        const addRow = document.getElementById('addRow');

        function updateRowIndex() {
            tbody.querySelectorAll('.detail-row').forEach((row, index) => {
                row.querySelector('select').name = `details[${index}][bahan_pakan_id]`;
                row.querySelector('input[type="number"]').name = `details[${index}][jumlah]`;
                row.querySelector('input[type="text"]').name = `details[${index}][catatan]`;
            });
        }

        addRow.addEventListener('click', function () {
            const index = tbody.querySelectorAll('.detail-row').length;
            const tr = document.createElement('tr');
            tr.className = 'detail-row bg-white border-b';
            tr.innerHTML = `
                <td class="px-3 py-2">
                    <select name="details[${index}][bahan_pakan_id]" class="w-full border-gray-300 rounded-md text-sm" required>
                        <option value="">Pilih Bahan</option>${bahans}
                    </select>
                </td>
                <td class="px-3 py-2">
                    <input type="number" step="0.01" name="details[${index}][jumlah]" class="w-full border-gray-300 rounded-md text-sm" required>
                </td>
                <td class="px-3 py-2">
                    <input type="text" name="details[${index}][catatan]" class="w-full border-gray-300 rounded-md text-sm">
                </td>
                <td class="px-3 py-2 text-center">
                    <button type="button" class="remove-row text-red-600 hover:text-red-800">Hapus</button>
                </td>
            `;
            tbody.appendChild(tr);
            updateRowIndex();
        });

        tbody.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-row')) {
                e.target.closest('.detail-row').remove();
                updateRowIndex();
            }
        });
    });
</script>
@endsection
