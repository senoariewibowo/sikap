@extends('layouts.admin')

@section('title', 'Kategori Pengeluaran - SIKAP')
@section('page-title', 'Kategori Pengeluaran')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">Daftar Kategori</h2>
        <a href="{{ route('keuangan.kategori.create') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">&plus; Tambah</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr><th class="px-6 py-3">Nama Kategori</th><th class="px-6 py-3 text-right">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($kategoris as $k)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $k->nama }}</td>
                    <td class="px-6 py-4">
                        <div class="flex justify-end space-x-1">
                            <a href="{{ route('keuangan.kategori.edit', $k) }}" class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                            <form action="{{ route('keuangan.kategori.destroy', $k) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="p-1.5 text-red-600 hover:bg-red-50 rounded"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="2" class="px-6 py-12 text-center text-gray-500">Belum ada kategori.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $kategoris->links() }}</div>
</div>
@endsection
