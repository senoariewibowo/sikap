@extends('layouts.admin')
@section('title', 'Alokasi Eceran - SIKAP')
@section('page-title', 'Alokasi Stok Eceran')
@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('telur.eceran.store') }}" onsubmit="return validateForm()">
            @csrf
            <div class="space-y-4">
                <div>
                    <x-input-label :value="'Tanggal'" />
                    <input type="date" name="tanggal" id="tanggal_input" value="{{ old('tanggal', now()->format('Y-m-d')) }}" class="block mt-1 w-full border-gray-300 rounded-md text-sm" required>
                </div>

                <div>
                    <x-input-label :value="'Pilih Peti'" />
                    @if($petisByGudang->isEmpty())
                    <p class="mt-2 text-sm text-red-500">Tidak ada peti tersedia.</p>
                    @else
                    <div class="relative mt-1" id="dropdown_wrapper">
                        <button type="button" id="dropdown_btn" class="w-full flex items-center justify-between border border-gray-300 rounded-md px-3 py-2 text-sm text-left bg-white hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <span class="text-gray-400">Cari & pilih peti...</span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div id="dropdown_panel" class="hidden absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg max-h-72 overflow-hidden">
                            <div class="p-2 border-b sticky top-0 bg-white">
                                <input type="text" id="search_peti" placeholder="Cari peti..." class="w-full border-gray-200 rounded-md text-sm px-2 py-1.5 focus:border-indigo-400 focus:ring-0">
                            </div>
                            <div class="overflow-y-auto max-h-56" id="peti_list">
                                @foreach($petisByGudang as $gudangNama => $items)
                                <div class="gudang-group">
                                    <p class="px-3 py-1.5 text-xs font-semibold text-gray-500 bg-gray-50 uppercase">{{ $gudangNama }}</p>
                                    @foreach($items as $p)
                                    <label class="peti-item flex items-center gap-2 px-3 py-2 cursor-pointer hover:bg-indigo-50">
                                        <input type="checkbox" name="peti_ids[]" value="{{ $p->id }}" class="peti-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" data-tanggal="{{ $p->tgl_raw }}" data-gudang="{{ $p->gudang_nama }}" data-butir="{{ $p->butir }}" data-karpet="{{ $p->karpet }}" data-berat="{{ $p->berat }}">
                                        <span class="text-sm">#{{ $p->id }} <span class="text-gray-400">{{ $p->tgl_sortir }}</span> @if($p->kandang_nama)<span class="text-xs text-indigo-500">({{ $p->kandang_nama }})</span>@endif — <strong>{{ number_format($p->butir) }}</strong> btr, {{ $p->karpet }} krpt, {{ number_format($p->berat, 2) }} kg</span>
                                    </label>
                                    @endforeach
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 flex items-center gap-4">
                        <span class="text-sm text-gray-600" id="hitung_info">0 peti | 0 btr | 0 krpt | 0 kg</span>
                        <button type="button" id="clear_btn" class="text-xs text-red-500 hover:text-red-700 hidden">Hapus pilihan</button>
                    </div>
                    @endif
                </div>

                <div>
                    <x-input-label :value="'Keterangan'" />
                    <textarea name="keterangan" rows="2" class="block mt-1 w-full border-gray-300 rounded-md text-sm" placeholder="Misal: Alokasi untuk penjualan eceran pagi...">{{ old('keterangan') }}</textarea>
                </div>
                <div class="flex space-x-2">
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 font-semibold">Simpan Alokasi</button>
                    <a href="{{ route('setoran.gudang') }}" class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
(function(){
    var btn = document.getElementById('dropdown_btn');
    var panel = document.getElementById('dropdown_panel');
    var search = document.getElementById('search_peti');
    var info = document.getElementById('hitung_info');
    var clearBtn = document.getElementById('clear_btn');
    var tanggalInput = document.getElementById('tanggal_input');
    var checks = document.querySelectorAll('.peti-checkbox');
    var wrapper = document.getElementById('dropdown_wrapper');

    function getSelectedDate(){
        return tanggalInput ? tanggalInput.value : '';
    }

    function updateBtnText(){
        var checked = document.querySelectorAll('.peti-checkbox:checked');
        if(checked.length === 0){
            btn.querySelector('span').textContent = 'Cari & pilih peti...';
            btn.querySelector('span').classList.add('text-gray-400');
            clearBtn.classList.add('hidden');
        } else {
            btn.querySelector('span').textContent = checked.length + ' peti dipilih (' + Array.from(checked).map(function(c){ return '#'+c.value; }).join(', ') + ')';
            btn.querySelector('span').classList.remove('text-gray-400');
            clearBtn.classList.remove('hidden');
        }
        var butir = 0, karpet = 0, berat = 0;
        checked.forEach(function(cb){
            butir += parseInt(cb.getAttribute('data-butir')) || 0;
            karpet += parseInt(cb.getAttribute('data-karpet')) || 0;
            berat += parseFloat(cb.getAttribute('data-berat')) || 0;
        });
        info.textContent = checked.length + ' peti | ' + butir.toLocaleString('id-ID') + ' btr | ' + karpet + ' krpt | ' + berat.toFixed(2) + ' kg';
    }

    btn.addEventListener('click', function(e){
        e.stopPropagation();
        panel.classList.toggle('hidden');
        if(!panel.classList.contains('hidden')){
            search.focus();
        }
    });

    document.addEventListener('click', function(e){
        if(!wrapper.contains(e.target)){
            panel.classList.add('hidden');
        }
    });

    clearBtn.addEventListener('click', function(){
        checks.forEach(function(cb){ cb.checked = false; });
        updateBtnText();
        filterList();
    });

    search.addEventListener('input', function(){
        filterList();
    });

    tanggalInput.addEventListener('change', function(){
        checks.forEach(function(cb){ cb.checked = false; });
        updateBtnText();
        filterList();
    });

    function filterList(){
        var q = search.value.toLowerCase();
        var tgl = getSelectedDate();
        var items = document.querySelectorAll('.peti-item');
        var groups = document.querySelectorAll('.gudang-group');
        groups.forEach(function(g){
            var visible = false;
            g.querySelectorAll('.peti-item').forEach(function(item){
                var cb = item.querySelector('.peti-checkbox');
                var text = item.textContent.toLowerCase();
                var matchSearch = q === '' || text.indexOf(q) !== -1;
                var matchDate = tgl === '' || (cb && cb.getAttribute('data-tanggal') === tgl);
                var show = matchSearch && matchDate;
                item.style.display = show ? '' : 'none';
                if(show) visible = true;
            });
            g.style.display = visible ? '' : 'none';
        });
    }

    checks.forEach(function(cb){
        cb.addEventListener('change', function(){
    updateBtnText();
    filterList();
        });
    });

    updateBtnText();
    filterList();

    window.validateForm = function(){
        var checked = document.querySelectorAll('.peti-checkbox:checked');
        if(checked.length === 0){
            alert('Pilih minimal 1 peti.');
            return false;
        }
        return true;
    };
})();
</script>
@endsection
