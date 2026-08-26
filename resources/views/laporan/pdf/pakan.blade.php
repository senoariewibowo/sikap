<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Laporan Penggunaan Pakan</title>
<style>body{font-family:Arial,sans-serif;font-size:11px}table{width:100%;border-collapse:collapse;margin-top:10px}th,td{border:1px solid #000;padding:6px}th{background:#f0f0f0}.header{text-align:center}</style></head>
<body>
<div class="header"><h2>LAPORAN PENGGUNAAN PAKAN & OBAT</h2><p>Periode: {{ $dari }} s/d {{ $sampai }}</p></div>
<table>
<thead><tr><th>No</th><th>Tanggal</th><th>Jenis</th><th>Tipe</th><th>Jumlah</th><th>Kandang</th><th>Keterangan</th></tr></thead>
<tbody>
@foreach($data as $i => $d)
<tr><td>{{ $i+1 }}</td><td>{{ $d->tanggal->format('d/m/Y') }}</td><td>{{ $d->jenisPakan->nama ?? '-' }}</td><td>{{ $d->tipe=='masuk'?'Masuk':'Keluar' }}</td><td>{{ $d->jumlah_kg }} {{ $d->jenisPakan->satuan ?? '' }}</td><td>{{ $d->kandang->nama_kandang ?? 'Gudang Pusat' }}</td><td>{{ $d->keterangan ?: '-' }}</td></tr>
@endforeach
</tbody></table>
</body></html>
