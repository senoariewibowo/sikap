@extends('layouts.admin')

@section('title', 'Customer - SIKAP')
@section('page-title', 'Master Customer')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">Daftar Customer</h2>
        <a href="{{ route('customer.create') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">&plus; Tambah Customer</a>
    </div>

    <form method="GET" class="px-6 py-3 border-b border-gray-200 bg-gray-50 flex gap-3">
        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama, kontak..." class="border-gray-300 rounded-md text-sm w-64 px-3 py-1.5">
        <button type="submit" class="px-3 py-1.5 bg-gray-700 text-white rounded-md text-sm hover:bg-gray-800">Cari</button>
        @if($search ?? false)<a href="{{ route('customer.index') }}" class="px-3 py-1.5 text-gray-600 text-sm hover:bg-gray-200">Reset</a>@endif
    </form>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr><th class="px-6 py-3">Nama</th><th class="px-6 py-3">Tipe</th><th class="px-6 py-3">No. HP</th><th class="px-6 py-3">Kontak Person</th><th class="px-6 py-3">Status</th><th class="px-6 py-3">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($customers as $c)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $c->nama_customer }}</td>
                    <td class="px-6 py-4"><span class="px-2 py-1 text-xs rounded-full {{ $c->tipe_customer=='korporat'?'bg-purple-100 text-purple-800':($c->tipe_customer=='agen'?'bg-blue-100 text-blue-800':'bg-gray-100 text-gray-800') }}">{{ ucfirst($c->tipe_customer) }}</span></td>
                    <td class="px-6 py-4">{{ $c->no_hp ?: '-' }}</td>
                    <td class="px-6 py-4">{{ $c->kontak_person ?: '-' }}</td>
                    <td class="px-6 py-4"><span class="px-2 py-1 text-xs rounded-full {{ $c->status=='aktif'?'bg-green-100 text-green-800':'bg-red-100 text-red-800' }}">{{ ucfirst($c->status) }}</span></td>
                    <td class="px-6 py-4"><div class="flex space-x-1">
                        <a href="{{ route('customer.edit', $c) }}" class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                        <form action="{{ route('customer.destroy', $c) }}" method="POST" onsubmit="return confirm('Nonaktifkan?')">@csrf @method('DELETE')<button class="p-1.5 text-red-600 hover:bg-red-50 rounded"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                    </div></td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">Belum ada data customer.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $customers->links() }}</div>
</div>
@endsection
