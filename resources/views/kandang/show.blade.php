@extends('layouts.admin')

@section('title', 'Detail Kandang - SIKAP')
@section('page-title', 'Detail Kandang')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">{{ $kandang->nama_kandang }}</h2>
            <div class="flex space-x-2">
                <a href="{{ route('kandang.edit', $kandang) }}" class="px-3 py-1.5 text-sm font-medium text-yellow-700 bg-yellow-50 border border-yellow-300 rounded-lg hover:bg-yellow-100">
                    Edit
                </a>
                <a href="{{ route('kandang.index') }}" class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Kembali
                </a>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2 space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Kode Kandang</p>
                            <p class="text-gray-900">{{ $kandang->kode_kandang }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Status</p>
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($kandang->status == 'aktif') bg-green-100 text-green-800
                                @elseif($kandang->status == 'renovasi') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($kandang->status) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Gudang</p>
                            <p class="text-gray-900">{{ $kandang->gudang->nama_gudang ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Tipe Kandang</p>
                            <p class="text-gray-900">{{ ucfirst(str_replace('_', ' ', $kandang->tipe_kandang)) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Kapasitas</p>
                            <p class="text-gray-900">{{ number_format($kandang->kapasitas) }} ekor</p>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Alamat Lengkap</h3>
                        <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700">
                            <p>{{ $kandang->alamat_jalan }}</p>
                            <p>{{ $kandang->desa_kelurahan }}, {{ $kandang->kecamatan }}</p>
                            <p>{{ $kandang->kabupaten_kota }}, {{ $kandang->provinsi }} {{ $kandang->kode_pos }}</p>
                        </div>
                    </div>

                    @if($kandang->latitude && $kandang->longitude)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Koordinat</h3>
                        <p class="text-sm text-gray-600">{{ $kandang->latitude }}, {{ $kandang->longitude }}</p>
                    </div>
                    @endif

                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Karyawan Aktif</h3>
                        @if($kandang->karyawan->isNotEmpty())
                            <div class="space-y-2">
                                @foreach($kandang->karyawan as $karyawan)
                                    <div class="flex items-center justify-between bg-gray-50 rounded-lg p-3">
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">{{ $karyawan->nama }}</p>
                                            <p class="text-xs text-gray-500">{{ $karyawan->jabatan }}</p>
                                        </div>
                                        <span class="text-xs text-gray-400">Mulai: {{ $karyawan->pivot->tanggal_mulai }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">Belum ada karyawan ditugaskan.</p>
                        @endif
                    </div>
                </div>

                <div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Foto Kandang</h3>
                        @if($kandang->foto)
                            <img src="{{ asset('storage/' . $kandang->foto) }}" alt="Foto kandang" class="w-full rounded-lg border">
                        @else
                            <div class="flex items-center justify-center h-40 bg-gray-200 rounded-lg text-gray-400">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
