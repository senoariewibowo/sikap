<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Struk {{ $transaksi->no_struk }}</title>
<style>
    @page { size: 80mm auto; margin: 0; }
    html, body { margin: 0; padding: 0; }
    body {
        width: 72mm;
        margin: 0 auto;
        font-family: 'Courier New', Courier, monospace;
        font-size: 11px;
        line-height: 1.4;
        color: #000;
        background: #fff;
    }
    .wrap { padding: 2mm; }
    .center { text-align: center; }
    .toko { font-size: 15px; font-weight: bold; }
    .muted { font-size: 10px; }
    .row { display: flex; justify-content: space-between; gap: 2mm; }
    .name { word-break: break-word; }
    .total { font-size: 13px; font-weight: bold; }
    hr { border: none; border-top: 1px dashed #000; margin: 2mm 0; }
    .btn-print {
        display: block; width: 60mm; margin: 3mm auto; padding: 2mm 0;
        font-family: sans-serif; font-size: 12px; border: 1px solid #000; background: #eee; cursor: pointer;
    }
    @media print {
        .btn-print { display: none; }
    }
</style>
</head>
<body>
<div class="wrap">
    <p class="toko center">TOKO ANDI</p>
    <p class="center muted">Jl. Muarabakau <br>Telp: 08137-7780-479</p>
    <hr>
    <div class="row"><span>{{ $transaksi->no_struk }}</span><span>{{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d/m/Y H:i') }}</span></div>
    <div class="row"><span>Kasir: {{ $transaksi->user->name ?? '-' }}</span></div>
    @if($transaksi->nama_pembeli)
    <div class="row"><span>Pembeli: {{ $transaksi->nama_pembeli }}</span></div>
    @endif
    <hr>

    @foreach($transaksi->details as $d)
    <div class="row">
        <span class="name">{{ $d->product_name }}</span>
    </div>
    <div class="row">
        <span>  {{ $d->qty }} x {{ number_format($d->harga, 0, ',', '.') }}</span>
        <span>{{ number_format($d->subtotal, 0, ',', '.') }}</span>
    </div>
    @endforeach

    <hr>
    <div class="row"><span>TOTAL</span><span class="total">Rp {{ number_format($transaksi->total, 0, ',', '.') }}</span></div>
    <div class="row"><span>METODE</span><span>{{ strtoupper($transaksi->metode_pembayaran) }}</span></div>
    <div class="row"><span>DIBAYAR</span><span>Rp {{ number_format($transaksi->dp, 0, ',', '.') }}</span></div>
    @if($transaksi->kembalian > 0)
    <div class="row"><span>KEMBALI</span><span>Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</span></div>
    @endif
    @if($transaksi->kekurangan > 0)
    <div class="row"><span>KEKURANGAN</span><span class="total">Rp {{ number_format($transaksi->kekurangan, 0, ',', '.') }}</span></div>
    <div class="row"><span>STATUS</span><span>{{ strtoupper($transaksi->status_pembayaran) }}</span></div>
    @else
    <div class="row"><span>STATUS</span><span>LUNAS</span></div>
    @endif
    <hr>
    <p class="center">Terima kasih sudah berbelanja</p>
    <p class="center muted">Barang yang sudah dibeli<br>tidak dapat dikembalikan</p>
</div>

<button class="btn-print" onclick="window.print()">Cetak Struk</button>

@if(request()->has('print'))
<script>window.addEventListener('load', function () { window.print(); });</script>
@endif
</body>
</html>
