<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Surat Jalan - {{ $stokTelur->no_referensi ?: 'SJ' }}</title>
<style>
body{font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#1a1a1a;margin:0;padding:20px}
.header{border-bottom:2px solid #333;padding-bottom:12px;margin-bottom:16px;text-align:center}
.header h1{font-size:18px;margin:0 0 4px;text-transform:uppercase;letter-spacing:1px}
.header p{font-size:11px;color:#666;margin:0}
.info{width:100%;margin-bottom:14px}
.info td{vertical-align:top;padding:4px 0}
.info .label{font-size:9px;color:#888;text-transform:uppercase}
.info .value{font-size:14px;font-weight:bold}
.box{border:1px solid #ddd;border-radius:4px;padding:10px;margin-bottom:14px;background:#f9f9f9}
.box .title{font-size:9px;color:#888;text-transform:uppercase;margin:0 0 4px;font-weight:bold}
.box p{margin:2px 0}
table.data{width:100%;border-collapse:collapse;margin-bottom:14px}
table.data th,table.data td{border:1px solid #ccc;padding:6px 8px;text-align:left}
table.data th{background:#f0f0f0;font-size:10px;text-transform:uppercase}
table.data td{font-size:11px}
.detail{width:100%;margin-bottom:14px}
.detail td{vertical-align:top;padding:4px 10px;width:50%}
.ttd{width:100%;margin-top:40px;text-align:center}
.ttd td{width:33%;padding:10px;vertical-align:bottom;height:120px}
.ttd .name{border-top:1px solid #333;padding-top:6px;margin-top:6px;font-size:11px}
.ttd .role{font-size:9px;color:#888}
.ttd img{max-height:50px;display:block;margin:0 auto 4px}
</style>
</head>
<body>

<div class="header">
    <h1>Surat Jalan</h1>
    <p>Sistem Informasi Kandang Ayam Petelur — SIKAP</p>
</div>

<table class="info">
<tr>
    <td><span class="label">No. Surat Jalan</span><br><span class="value">{{ $stokTelur->no_referensi ?: '-' }}</span></td>
    <td style="text-align:right"><span class="label">Tanggal</span><br><span class="value">{{ \Carbon\Carbon::parse($stokTelur->tanggal)->format('d F Y') }}</span></td>
</tr>
</table>

<div class="box">
    <p class="title">Gudang Pengirim</p>
    @if($stokTelur->gudang)
        <p><strong>{{ $stokTelur->gudang->kode_gudang }} — {{ $stokTelur->gudang->nama_gudang }}</strong></p>
        <p style="color:#555">{{ $stokTelur->gudang->lokasi }}</p>
    @else
        <p><strong>-</strong></p>
    @endif
</div>

<div class="box">
    <p class="title">Driver / Pengemudi</p>
    <p><strong>{{ $stokTelur->driver ?: '-' }}</strong></p>
</div>

<table class="data">
<thead><tr><th>No</th><th>Kandang Asal</th><th style="text-align:right">Jumlah (butir)</th><th style="text-align:right">Berat (kg)</th></tr></thead>
<tbody>
@foreach($stokTelur->details as $i => $d)
<tr>
    <td>{{ $i + 1 }}</td>
    <td>{{ $d->sortasiDetail->sortasiTelur->kandang->nama_kandang ?? '-' }}</td>
    <td style="text-align:right">{{ number_format($d->jumlah_butir) }}</td>
    <td style="text-align:right">{{ number_format($d->berat_kg, 1) }}</td>
</tr>
@endforeach
<tr style="font-weight:bold"><td colspan="2">Total</td><td style="text-align:right">{{ number_format($stokTelur->jumlah_butir) }}</td><td style="text-align:right">{{ number_format($stokTelur->berat_kg, 1) }}</td></tr>
</tbody>
</table>

<table class="detail">
<tr>
    <td><span class="label">Keterangan</span><br>{{ $stokTelur->keterangan ?: '-' }}</td>
    <td></td>
</tr>
</table>

<table class="ttd">
<tr>
    <td>
        <strong>Pengirim</strong>
        @if($stokTelur->ttdPengirim)
            @if($stokTelur->ttd_pengirim_img)<img src="{{ $stokTelur->ttd_pengirim_img }}" alt="TTD">@endif
            <div class="name">{{ $stokTelur->ttdPengirim->name }}</div>
            <div class="role">{{ \Carbon\Carbon::parse($stokTelur->ttd_pengirim_at)->format('d/m/Y H:i') }}</div>
        @else
            <div class="name">___________</div>
            <div class="role">Petugas</div>
        @endif
    </td>
    <td>
        <strong>Penerima</strong>
        <div class="name">___________</div>
        <div class="role">Ttd &amp; Nama</div>
    </td>
    <td>
        <strong>Mengetahui</strong>
        @if($stokTelur->ttdMengetahui)
            @if($stokTelur->ttd_mengetahui_img)<img src="{{ $stokTelur->ttd_mengetahui_img }}" alt="TTD">@endif
            <div class="name">{{ $stokTelur->ttdMengetahui->name }}</div>
            <div class="role">{{ \Carbon\Carbon::parse($stokTelur->ttd_mengetahui_at)->format('d/m/Y H:i') }}</div>
        @else
            <div class="name">___________</div>
            <div class="role">Manajer</div>
        @endif
    </td>
</tr>
</table>

</body>
</html>
