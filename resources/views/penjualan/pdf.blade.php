<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Laporan Penjualan</title>
<style>body{font-family:Arial,sans-serif;font-size:10px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #000;padding:5px}th{background:#f0f0f0}.header{text-align:center;margin-bottom:10px}</style></head>
<body>
<div class="header"><h2>LAPORAN PENJUALAN TELUR</h2><p>Periode: {{ $dari }} s/d {{ $sampai }}</p></div>
<table>
<thead><tr><th>No</th><th>Tanggal</th><th>Customer</th><th>Butir</th><th>Harga/Butir</th><th>Total</th><th>Pembayaran</th></tr></thead>
<tbody>
@foreach($data as $i=>$d)
<tr><td>{{ $i+1 }}</td><td>{{ $d->tanggal->format('d/m/Y') }}</td><td>{{ $d->customer->nama_customer ?? '-' }}</td><td>{{ $d->jumlah_butir }}</td><td>{{ number_format($d->harga_per_satuan, 0, ',', '.') }}</td><td>{{ number_format($d->total_harga, 0, ',', '.') }}</td><td>{{ $d->status_pembayaran=='lunas'?'Lunas':'Belum Lunas' }}</td></tr>
@endforeach
</tbody></table>
</body></html>
