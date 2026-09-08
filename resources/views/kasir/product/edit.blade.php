@extends('layouts.admin')

@section('title', 'Edit Produk - Kasir')
@section('page-title', 'Edit Produk')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('kasir.product.update', $product) }}">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <x-input-label :value="'Nama Produk'" />
                    <input type="text" name="product_name" value="{{ old('product_name', $product->product_name) }}" class="block mt-1 w-full border-gray-300 rounded-md text-sm" required>
                    @error('product_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label :value="'Barcode'" />
                        <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}" placeholder="Opsional" class="block mt-1 w-full border-gray-300 rounded-md text-sm">
                        @error('barcode')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <x-input-label :value="'Grup Produk'" />
                        <select name="group_id" class="block mt-1 w-full border-gray-300 rounded-md text-sm">
                            <option value="">Tanpa Grup</option>
                            @foreach($groups as $g)
                            <option value="{{ $g->id }}" {{ old('group_id', $product->group_id) == $g->id ? 'selected' : '' }}>{{ $g->group_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <x-input-label :value="'Modal (Rp)'" />
                    <input type="number" step="0.01" name="modal" value="{{ old('modal', $product->modal) }}" class="block mt-1 w-full border-gray-300 rounded-md text-sm">
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-700">Harga Bertingkat (per qty minimal)</h3>
                    <div class="mt-2 space-y-2">
                        @php $priceMap = $product->prices->keyBy('tier'); @endphp
                        @for($i = 1; $i <= 4; $i++)
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-gray-500 w-10 shrink-0">Tier {{ $i }}</span>
                            <input type="number" name="min_qty[]" min="1" placeholder="Min qty" value="{{ old("min_qty.$i", $priceMap->has($i) ? $priceMap[$i]->min_qty : ($i == 1 ? 1 : '')) }}" class="block w-24 border-gray-300 rounded-md text-sm px-2 py-1.5">
                            <input type="number" step="0.01" name="tier[]" placeholder="Harga" value="{{ old("tier.$i", $priceMap->has($i) ? $priceMap[$i]->harga : '') }}" class="block flex-1 border-gray-300 rounded-md text-sm px-2 py-1.5">
                        </div>
                        @endfor
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Kosongkan harga untuk tier yang tidak dipakai.</p>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-emerald-600">
                    <label for="is_active" class="text-sm text-gray-700">Aktif</label>
                </div>

                <div class="flex space-x-2">
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700 font-semibold">Simpan</button>
                    <a href="{{ route('kasir.product.index') }}" class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
