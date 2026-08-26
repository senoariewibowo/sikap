<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Laporan Produksi Telur</title>
<style>body{font-family:Arial,sans-serif;font-size:11px}table{width:100%;border-collapse:collapse;margin-top:10px}th,td{border:1px solid #000;padding:6px}th{background:#f0f0f0}.header{text-align:center;margin-bottom:10px}.summary{margin:10px 0}</style></head>
<body>
<div class="header"><h2>LAPORAN PRODUKSI TELUR</h2><p>Periode: {{ $dari }} s/d {{ $sampai }}</p></div>
<div class="summary"><strong>Total Butir:</strong> {{ number_format($summary->total_butir ?? 0) }}</div>
<table>
<thead><tr><th>No</th><th>Tanggal</th><th>Kandang</th><th>Butir</th><th>Shift</th><th>Status</th></tr></thead>
<tbody>@foreach($data as $i=>$d)<tr><td>{{ $i+1 }}</td><td>{{ $d->tanggal->format('d/m/Y') }}</td><td>{{ $d->kandang->nama_kandang ?? '-' }}</td><td>{{ $d->jumlah_butir }}</td><td>{{ $d->shift ?: '-' }}</td><td>{{ $d->status_setor === 'sudah_disetor' ? 'Sudah Disetor' : 'Belum' }}</td></tr>@endforeach</tbody>
</table></body></html>
