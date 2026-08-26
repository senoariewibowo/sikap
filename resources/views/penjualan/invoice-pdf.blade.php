<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Invoice - {{ $transaksi->no_invoice ?: 'INV' }}</title>
<style>
body{font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#1a1a1a;margin:0;padding:20px}
.header{border-bottom:2px solid #333;padding-bottom:12px;margin-bottom:16px}
.header h1{font-size:18px;margin:0;text-transform:uppercase;letter-spacing:1px}
.header .sub{font-size:11px;color:#666}
.badge{display:inline-block;padding:2px 10px;border-radius:12px;font-size:10px;font-weight:bold}
.badge.lunas{background:#d4edda;color:#155724}
.badge.belum{background:#f8d7da;color:#721c24}
.flex{display:flex;justify-content:space-between;align-items:flex-start}
.info{width:100%;margin-bottom:12px}
.info td{padding:4px 0}
.info .label{font-size:9px;color:#888;text-transform:uppercase}
.box{border:1px solid #ddd;border-radius:4px;padding:10px;margin-bottom:14px;background:#f9f9f9}
.box .title{font-size:9px;color:#888;text-transform:uppercase;margin:0 0 4px;font-weight:bold}
.box p{margin:2px 0}
table.data{width:100%;border-collapse:collapse;margin-bottom:14px}
table.data th,table.data td{border:1px solid #ccc;padding:6px 8px;text-align:left}
table.data th{background:#f0f0f0;font-size:10px;text-transform:uppercase}
table.data td{font-size:11px}
.summary{width:100%;margin-bottom:14px}
.summary td{padding:3px 0;font-size:12px}
.summary .total{font-size:16px;font-weight:bold}
.summary .label{color:#888}
.summary .sisa{color:#c00;font-weight:bold}
.summary .lunas{color:#0a0}
.ttd{width:100%;margin-top:40px;text-align:center}
.ttd td{width:50%;padding:10px;vertical-align:bottom;height:100px}
.ttd .name{border-top:1px solid #333;padding-top:6px;margin-top:6px;font-size:11px}
.ttd .role{font-size:9px;color:#888}
.ttd img{max-height:50px;display:block;margin:0 auto 4px}
</style>
</head>
<body>

<div class="flex header">
    <div>
        <h1>Invoice</h1>
        <p class="sub">SIKAP — Peternakan Ayam Petelur</p>
    </div>
    <span class="badge {{ $transaksi->status_pembayaran=='lunas'?'lunas':'belum' }}">{{ $transaksi->status_pembayaran=='lunas'?'LUNAS':'BELUM LUNAS' }}</span>
</div>

<table class="info">
<tr>
    <td><span class="label">No. Invoice</span><br><span style="font-size:14px;font-weight:bold">{{ $transaksi->no_invoice ?: '-' }}</span></td>
    <td style="text-align:right"><span class="label">Tanggal</span><br><span style="font-weight:bold">{{ $transaksi->tanggal->format('d F Y') }}</span></td>
</tr>
</table>

<div class="box">
    <p class="title">Customer</p>
    <p><strong>{{ $transaksi->customer->nama_customer ?? '-' }}</strong></p>
    <p style="font-size:10px;color:#555">{{ ucfirst($transaksi->customer->tipe_customer ?? '') }}</p>
    @if($transaksi->customer->alamat)<p style="font-size:10px;color:#555">{{ $transaksi->customer->alamat }}</p>@endif
    @if($transaksi->customer->no_hp)<p style="font-size:10px;color:#555">{{ $transaksi->customer->no_hp }}</p>@endif
</div>

<table class="data">
<thead><tr><th>Deskripsi</th><th style="text-align:right">Jumlah</th><th style="text-align:right">Butir</th><th style="text-align:right">Berat (kg)</th><th style="text-align:right">Harga/Satuan</th><th style="text-align:right">Subtotal</th></tr></thead>
<tbody>
<tr>
    <td><strong>Telur ({{ str_replace('_', ' ', $transaksi->satuan) }})</strong></td>
    <td style="text-align:right">{{ number_format($transaksi->jumlah_satuan) }}</td>
    <td style="text-align:right">{{ number_format($transaksi->jumlah_butir) }}</td>
    <td style="text-align:right">{{ number_format($transaksi->berat_kg, 1) }}</td>
    <td style="text-align:right">Rp {{ number_format($transaksi->harga_per_satuan, 0, ',', '.') }}</td>
    <td style="text-align:right;font-weight:bold">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
</tr>
</tbody>
</table>

<table class="summary">
<tr><td class="label" width="40%">Total</td><td class="total">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td></tr>
<tr><td class="label">DP / Uang Muka</td><td style="color:#0a0">Rp {{ number_format($transaksi->dp, 0, ',', '.') }}</td></tr>
<tr><td class="label">Sisa Tagihan</td>
    <td class="{{ ($transaksi->total_harga-$transaksi->dp)>0?'sisa':'lunas' }}">
        {{ ($transaksi->total_harga-$transaksi->dp)>0?'Rp '.number_format($transaksi->total_harga-$transaksi->dp,0,',','.'):'LUNAS' }}
    </td>
</tr>
</table>

<div style="font-size:10px;color:#888;margin-bottom:14px">
    @if($transaksi->stokDetails && $transaksi->stokDetails->isNotEmpty())
        @foreach($transaksi->stokDetails as $sd)
            <strong>{{ $sd->stokTelurKeluar->gudang->nama_gudang ?? '-' }}:</strong> {{ number_format($sd->jumlah_butir) }} butir &middot; SJ: {{ $sd->stokTelurKeluar->no_referensi ?? '-' }}<br>
        @endforeach
    @elseif($transaksi->stokTelurKeluar && $transaksi->stokTelurKeluar->gudang)
        <strong>Gudang:</strong> {{ $transaksi->stokTelurKeluar->gudang->nama_gudang }} &nbsp;
        <strong>Surat Jalan:</strong> {{ $transaksi->stokTelurKeluar->no_referensi }}
    @endif
</div>

<table class="ttd">
<tr>
    <td>
        <strong>Petugas</strong>
        @if($transaksi->ttdPetugas)
            @if($transaksi->ttd_petugas_img)<img src="{{ $transaksi->ttd_petugas_img }}" alt="TTD">@endif
            <div class="name">{{ $transaksi->ttdPetugas->name }}</div>
            <div class="role">{{ \Carbon\Carbon::parse($transaksi->ttd_petugas_at)->format('d/m/Y H:i') }}</div>
        @else
            <div class="name">___________</div>
        @endif
    </td>
    <td>
        <strong>Customer</strong>
        <div class="name">___________</div>
        <div class="role">{{ $transaksi->customer->nama_customer ?? '' }}</div>
    </td>
</tr>
</table>

</body>
</html>
