<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Laporan Pengeluaran</title>
<style>body{font-family:Arial,sans-serif;font-size:10px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #000;padding:5px}th{background:#f0f0f0}.header{text-align:center;margin-bottom:10px}</style></head>
<body>
<div class="header"><h2>LAPORAN PENGELUARAN</h2><p>Periode: {{ $dari }} s/d {{ $sampai }}</p></div>
<table>
<thead><tr><th>No</th><th>Tanggal</th><th>Kategori</th><th>Kandang</th><th>Jumlah (Rp)</th><th>Keterangan</th></tr></thead>
<tbody>
@foreach($data as $i => $d)
<tr><td>{{ $i+1 }}</td><td>{{ $d->tanggal->format('d/m/Y') }}</td><td>{{ $d->kategori->nama ?? '-' }}</td><td>{{ $d->kandang->nama_kandang ?? '-' }}</td><td>{{ number_format($d->jumlah, 0, ',', '.') }}</td><td>{{ $d->keterangan ?: '-' }}</td></tr>
@endforeach
</tbody></table>
</body></html>
