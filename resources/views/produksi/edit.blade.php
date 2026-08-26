@extends('layouts.admin')

@section('title', 'Edit Produksi Telur - SIKAP')
@section('page-title', 'Edit Produksi Telur')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Edit Produksi Telur</h2>
        </div>

        <form action="{{ route('produksi.update', $produksi) }}" method="POST" class="p-6 space-y-4" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="kandang_id" :value="'Kandang'" />
                <select id="kandang_id" name="kandang_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" required>
                    <option value="">Pilih Kandang</option>
                    @foreach($kandangs as $k)
                    <option value="{{ $k->id }}" {{ old('kandang_id', $produksi->kandang_id) == $k->id ? 'selected' : '' }}>{{ $k->nama_kandang }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('kandang_id')" class="mt-2" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="tanggal" :value="'Tanggal Produksi'" />
                    <x-text-input id="tanggal" name="tanggal" type="date" class="block mt-1 w-full" :value="old('tanggal', $produksi->tanggal->format('Y-m-d'))" required />
                    <x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="shift" :value="'Shift'" />
            <select id="shift" name="shift" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" required>
                <option value="">Pilih Shift</option>
                <option value="siang" {{ old('shift', $produksi->shift) == 'siang' ? 'selected' : '' }}>Siang</option>
                <option value="sore" {{ old('shift', $produksi->shift) == 'sore' ? 'selected' : '' }}>Sore</option>
            </select>
                    <x-input-error :messages="$errors->get('shift')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="karpet" :value="'Karpet (tray)'" />
                    <x-text-input id="karpet" name="karpet" type="number" class="block mt-1 w-full" :value="old('karpet', $produksi->karpet)" required min="0" oninput="hitungButir()" />
                    <p class="text-xs text-gray-500 mt-1">1 karpet = 30 butir</p>
                    <x-input-error :messages="$errors->get('karpet')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="sisa" :value="'Sisa (butir)'" />
                    <x-text-input id="sisa" name="sisa" type="number" class="block mt-1 w-full" :value="old('sisa', $produksi->sisa)" min="0" max="29" oninput="hitungButir()" />
                    <p class="text-xs text-gray-500 mt-1">Sisa butir &lt; 30</p>
                    <x-input-error :messages="$errors->get('sisa')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label :value="'Butir Karpet (auto)'" />
                    <x-text-input id="butir_karpet" type="number" class="block mt-1 w-full bg-gray-100" :value="old('butir_karpet', $produksi->karpet * 30)" readonly />
                    <p class="text-xs text-gray-500 mt-1">Karpet &times; 30 butir</p>
                </div>
                <div>
                    <x-input-label for="jumlah_butir" :value="'Total Butir (auto)'" />
                    <x-text-input id="jumlah_butir" name="jumlah_butir" type="number" class="block mt-1 w-full bg-gray-100" :value="old('jumlah_butir', $produksi->jumlah_butir)" readonly required />
                    <p class="text-xs text-gray-500 mt-1">Butir karpet + sisa</p>
                    <x-input-error :messages="$errors->get('jumlah_butir')" class="mt-2" />
                </div>
            </div>

            <script>
            function hitungButir() {
                const karpet = parseInt(document.getElementById('karpet').value) || 0;
                const sisa = parseInt(document.getElementById('sisa').value) || 0;
                document.getElementById('butir_karpet').value = karpet * 30;
                document.getElementById('jumlah_butir').value = karpet * 30 + sisa;
            }
            </script>

            @if($produksi->status_setor === 'sudah_disetor')
            <div class="bg-yellow-50 border border-yellow-200 rounded p-3">
                <p class="text-sm text-yellow-700">Produksi ini sudah disetor. Hanya admin yang bisa mengedit data yang sudah disetor.</p>
            </div>
            @endif

            <div>
                <x-input-label :value="'Foto Lampiran (opsional, bisa >1)'" />
                <div id="existing_grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 mt-2">
                    @foreach($produksi->fotos as $f)
                    <div class="bg-white border rounded-lg overflow-hidden">
                        <img src="{{ $f->url }}" class="w-full h-28 sm:h-32 object-cover cursor-pointer" onclick="openImageModal('{{ $f->url }}')">
                        <div class="p-1.5 border-t bg-gray-50">
                            <button type="button" class="w-full text-xs text-red-600 hover:bg-red-50 rounded py-0.5" onclick="hapusExisting({{ $f->id }}, this.parentElement.parentElement)">Hapus</button>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div id="hapus_foto_container"></div>
                <label class="flex items-center justify-center gap-2 mt-3 px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-indigo-400 bg-gray-50 text-sm text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Tambah Foto
                    <input type="file" name="foto[]" id="foto_input" accept="image/*" capture="environment" multiple class="hidden" onchange="prosesFoto(this)">
                </label>
                <div id="preview_grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 mt-3"></div>
                <div id="base64_container"></div>
            </div>

            <script>
            var fotoList = [];
            function prosesFoto(input){
                var files = Array.from(input.files);
                files.forEach(function(file){
                    var reader = new FileReader();
                    reader.onload = function(e){
                        var img = new Image();
                        img.onload = function(){
                            var maxW = 800, w = img.width, h = img.height;
                            if(w > maxW){ h = Math.round(h * maxW / w); w = maxW; }
                            var canvas = document.createElement('canvas');
                            canvas.width = w; canvas.height = h;
                            var ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0, w, h);
                            fotoList.push(canvas.toDataURL('image/jpeg', 0.7));
                            renderPreviews();
                        };
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                });
                input.value = '';
            }
            function renderPreviews(){
                var grid = document.getElementById('preview_grid');
                var container = document.getElementById('base64_container');
                grid.innerHTML = ''; container.innerHTML = '';
                fotoList.forEach(function(b64, i){
                    var inp = document.createElement('input');
                    inp.type = 'hidden'; inp.name = 'foto_base64[]'; inp.value = b64;
                    container.appendChild(inp);
                    var div = document.createElement('div');
                    div.className = 'bg-white border rounded-lg overflow-hidden';
                    var img = document.createElement('img');
                    img.src = b64;
                    img.className = 'w-full h-28 sm:h-32 object-cover cursor-pointer';
                    img.onclick = function(){ openImageModal(b64); };
                    div.appendChild(img);
                    var bar = document.createElement('div');
                    bar.className = 'p-1.5 border-t bg-gray-50';
                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'w-full text-xs text-red-600 hover:bg-red-50 rounded py-0.5';
                    btn.textContent = 'Hapus';
                    btn.onclick = function(){ fotoList.splice(i,1); renderPreviews(); };
                    bar.appendChild(btn);
                    div.appendChild(bar);
                    grid.appendChild(div);
                });
            }
            var hapusIds = [];
            function hapusExisting(id, el){
                hapusIds.push(id);
                el.remove();
                var inp = document.createElement('input');
                inp.type = 'hidden'; inp.name = 'hapus_foto_ids[]'; inp.value = id;
                document.getElementById('hapus_foto_container').appendChild(inp);
            }
            </script>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('produksi.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
                <x-primary-button>Simpan Perubahan</x-primary-button>
            </div>
        </form>
    </div>
</div>

@include('components.image-modal')
@endsection
