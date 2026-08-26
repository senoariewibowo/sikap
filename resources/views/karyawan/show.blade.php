@extends('layouts.admin')

@section('title', 'Detail Karyawan - SIKAP')
@section('page-title', 'Detail Karyawan')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">{{ $karyawan->nama }}</h2>
            <div class="flex space-x-2">
                <a href="{{ route('karyawan.edit', $karyawan) }}" class="px-3 py-1.5 text-sm font-medium text-yellow-700 bg-yellow-50 border border-yellow-300 rounded-lg hover:bg-yellow-100">
                    Edit
                </a>
                <a href="{{ route('karyawan.index') }}" class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Kembali
                </a>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2 space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">NIK</p>
                            <p class="text-gray-900">{{ $karyawan->nik }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Status</p>
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($karyawan->status == 'aktif') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($karyawan->status) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Jabatan</p>
                            <p class="text-gray-900">{{ $karyawan->jabatan }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">No. HP</p>
                            <p class="text-gray-900">{{ $karyawan->no_hp ?: '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Tanggal Masuk</p>
                            <p class="text-gray-900">{{ \Carbon\Carbon::parse($karyawan->tanggal_masuk)->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Alamat</p>
                            <p class="text-gray-900">{{ $karyawan->alamat ?: '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Akun Login</p>
                            @if($karyawan->user)
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Aktif</span>
                                <p class="text-xs text-gray-400 mt-1">{{ $karyawan->user->email }}</p>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-500">Tidak Ada</span>
                            @endif
                        </div>
                    </div>

                    @if(in_array($karyawan->jabatan, ['Admin Gudang', 'Petugas Gudang']))
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Penugasan Gudang Aktif</h3>
                        @php $aktifGudang = $karyawan->gudang()->wherePivot('is_active', true)->get(); @endphp
                        @if($aktifGudang->isNotEmpty())
                            <div class="space-y-2">
                                @foreach($aktifGudang as $g)
                                    <div class="flex items-center justify-between bg-green-50 rounded-lg p-3 border border-green-200">
                                        <div class="flex items-center space-x-3">
                                            <div class="p-2 rounded-full bg-green-100 text-green-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-800">{{ $g->nama_gudang }}</p>
                                                <p class="text-xs text-gray-500">{{ $g->kode_gudang }} — {{ $g->lokasi ?: '-' }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xs text-gray-400">Mulai: {{ $g->pivot->tanggal_mulai }}</span>
                                            <form action="{{ route('karyawan.unassign-gudang', ['karyawan' => $karyawan->id, 'gudang' => $g->id]) }}" method="POST" onsubmit="return confirm('Akhiri penugasan di gudang ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs">Lepas</button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">Belum ditugaskan ke gudang manapun.</p>
                        @endif
                    </div>
                    @endif

                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Penugasan Kandang Aktif</h3>
                        @php $aktif = $karyawan->kandang()->wherePivot('is_active', true)->get(); @endphp
                        @if($aktif->isNotEmpty())
                            <div class="space-y-2">
                                @foreach($aktif as $kandang)
                                    <div class="flex items-center justify-between bg-gray-50 rounded-lg p-3">
                                        <div class="flex items-center space-x-3">
                                            <div class="p-2 rounded-full bg-indigo-100 text-indigo-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $kandang->nama_kandang }}</p>
                                <p class="text-xs text-gray-500">{{ $kandang->kecamatan }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-xs text-gray-400">Mulai: {{ $kandang->pivot->tanggal_mulai }}</span>
                            <form action="{{ route('karyawan.unassign-kandang', ['karyawan' => $karyawan->id, 'kandang' => $kandang->id]) }}" method="POST" onsubmit="return confirm('Akhiri penugasan di kandang ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs">Lepas</button>
                            </form>
                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">Belum ditugaskan ke kandang manapun.</p>
                        @endif
                    </div>

                    @if($riwayatKandang->isNotEmpty())
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Riwayat Penugasan Kandang</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2">Kandang</th>
                                        <th class="px-4 py-2">Tanggal Mulai</th>
                                        <th class="px-4 py-2">Tanggal Selesai</th>
                                        <th class="px-4 py-2">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($riwayatKandang as $k)
                                    <tr class="border-b">
                                        <td class="px-4 py-2 text-gray-900">{{ $k->nama_kandang }}</td>
                                        <td class="px-4 py-2">{{ $k->pivot->tanggal_mulai }}</td>
                                        <td class="px-4 py-2">{{ $k->pivot->tanggal_selesai ?: '-' }}</td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-1 text-xs rounded-full {{ $k->pivot->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                                {{ $k->pivot->is_active ? 'Aktif' : 'Selesai' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    @if($riwayatGudang->isNotEmpty())
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Riwayat Penugasan Gudang</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2">Gudang</th>
                                        <th class="px-4 py-2">Tanggal Mulai</th>
                                        <th class="px-4 py-2">Tanggal Selesai</th>
                                        <th class="px-4 py-2">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($riwayatGudang as $g)
                                    <tr class="border-b">
                                        <td class="px-4 py-2 text-gray-900">{{ $g->nama_gudang }}</td>
                                        <td class="px-4 py-2">{{ $g->pivot->tanggal_mulai }}</td>
                                        <td class="px-4 py-2">{{ $g->pivot->tanggal_selesai ?: '-' }}</td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-1 text-xs rounded-full {{ $g->pivot->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                                {{ $g->pivot->is_active ? 'Aktif' : 'Selesai' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>

                <div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Foto Karyawan</h3>
                        @if($karyawan->foto)
                            <img src="{{ asset('storage/' . $karyawan->foto) }}" alt="Foto karyawan" class="w-full rounded-lg border">
                        @else
                            <div class="flex items-center justify-center h-40 bg-gray-200 rounded-lg text-gray-400">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        @endif
                    </div>

                    @if(in_array($karyawan->jabatan, ['Manajer Kandang', 'Petugas Kandang']))
                    <div class="mt-4 bg-indigo-50 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-indigo-700 mb-3">Tugas ke Kandang</h3>
                        <form action="{{ route('karyawan.assign-kandang', $karyawan) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <select name="kandang_id" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" required>
                                    <option value="">Pilih Kandang</option>
                                    @foreach($kandangs as $kandang)
                                        <option value="{{ $kandang->id }}">{{ $kandang->nama_kandang }} ({{ $kandang->kode_kandang }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="tanggal_mulai" :value="'Tanggal Mulai'" />
                                <x-text-input id="tanggal_mulai" name="tanggal_mulai" type="date" class="block mt-1 w-full" :value="date('Y-m-d')" required />
                            </div>
                            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                                Tugaskan
                            </button>
                        </form>
                    </div>
                    @endif

                    @if(in_array($karyawan->jabatan, ['Admin Gudang', 'Petugas Gudang']))
                    <div class="mt-4 bg-green-50 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-green-700 mb-3">Tugas ke Gudang</h3>
                        <form action="{{ route('karyawan.assign-gudang', $karyawan) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <select name="gudang_id" class="block w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm text-sm" required>
                                    <option value="">Pilih Gudang</option>
                                    @foreach($gudangs as $gudang)
                                        <option value="{{ $gudang->id }}">{{ $gudang->nama_gudang }} ({{ $gudang->kode_gudang }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="tanggal_mulai" :value="'Tanggal Mulai'" />
                                <x-text-input id="tanggal_mulai" name="tanggal_mulai" type="date" class="block mt-1 w-full" :value="date('Y-m-d')" required />
                            </div>
                            <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
                                Tugaskan
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
