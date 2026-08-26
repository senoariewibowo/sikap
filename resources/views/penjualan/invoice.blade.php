<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice - {{ $transaksi->no_invoice ?: 'INV' }}</title>
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            body { background: #fff !important; }
            .no-print { display: none !important; }
            @page { margin: 15mm; size: A4; }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased print:bg-white">
    <div class="max-w-2xl mx-auto py-10 px-4">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden print:shadow-none print:border-2 print:border-gray-800">
            <div class="p-8 print:p-6">
                <div class="flex justify-between items-start border-b-2 border-gray-800 pb-4 mb-6">
                    <div>
                        <h1 class="text-xl font-bold tracking-wide uppercase">Invoice</h1>
                        <p class="text-sm text-gray-600">SIKAP — Peternakan Ayam Petelur</p>
                    </div>
                    <div class="text-right">
                        <span class="px-3 py-1 text-xs font-bold rounded-full {{ $transaksi->status_pembayaran == 'lunas' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $transaksi->status_pembayaran == 'lunas' ? 'LUNAS' : 'BELUM LUNAS' }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
                    <div>
                        <p class="text-gray-500 text-xs">No. Invoice</p>
                        <p class="font-bold text-lg">{{ $transaksi->no_invoice ?: '-' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-500 text-xs">Tanggal</p>
                        <p class="font-semibold">{{ $transaksi->tanggal->format('d F Y') }}</p>
                    </div>
                </div>

                <div class="mb-6">
                    <p class="text-gray-500 text-xs uppercase tracking-wider font-semibold mb-1">Customer</p>
                    <div class="border rounded-lg p-4 bg-gray-50 text-sm">
                        <p class="font-semibold">{{ $transaksi->customer->nama_customer ?? '-' }}</p>
                        <p class="text-xs text-gray-500">{{ ucfirst($transaksi->customer->tipe_customer ?? '') }}</p>
                        @if($transaksi->customer->alamat)
                            <p class="text-gray-600 text-xs mt-1">{{ $transaksi->customer->alamat }}</p>
                        @endif
                        @if($transaksi->customer->no_hp)
                            <p class="text-gray-600 text-xs">{{ $transaksi->customer->no_hp }}</p>
                        @endif
                    </div>
                </div>

                <table class="w-full mb-6 text-sm border-collapse">
                    <thead>
                        <tr class="border-y-2 border-gray-800">
                            <th class="py-2 text-left">Deskripsi</th>
                            <th class="py-2 text-right">Jumlah</th>
                            <th class="py-2 text-right">Butir</th>
                            <th class="py-2 text-right">Berat (kg)</th>
                            <th class="py-2 text-right">Harga/Satuan</th>
                            <th class="py-2 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b">
                            <td class="py-3 font-medium">Telur ({{ str_replace('_', ' ', $transaksi->satuan) }})</td>
                            <td class="py-3 text-right">{{ number_format($transaksi->jumlah_satuan) }}</td>
                            <td class="py-3 text-right">{{ number_format($transaksi->jumlah_butir) }}</td>
                            <td class="py-3 text-right">{{ number_format($transaksi->berat_kg, 1) }}</td>
                            <td class="py-3 text-right">Rp {{ number_format($transaksi->harga_per_satuan, 0, ',', '.') }}</td>
                            <td class="py-3 text-right font-semibold">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="grid grid-cols-2 gap-6 mb-6 text-sm">
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Total</span>
                            <span class="font-bold text-lg">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">DP / Uang Muka</span>
                            <span class="text-green-600 font-medium">Rp {{ number_format($transaksi->dp, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between border-t border-gray-300 pt-2">
                            <span class="text-gray-700 font-semibold">Sisa Tagihan</span>
                            <span class="font-bold {{ ($transaksi->total_harga - $transaksi->dp) > 0 ? 'text-red-600' : 'text-green-600' }}">
                                {{ ($transaksi->total_harga - $transaksi->dp) > 0 ? 'Rp ' . number_format($transaksi->total_harga - $transaksi->dp, 0, ',', '.') : 'LUNAS' }}
                            </span>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div>
                            <span class="text-gray-500 text-xs">Metode Pembayaran</span>
                            <p class="font-semibold">{{ ucfirst($transaksi->metode_pembayaran ?? 'Tunai') }}</p>
                        </div>
                        @if($transaksi->catatan_pembayaran)
                        <div>
                            <span class="text-gray-500 text-xs">Catatan Pembayaran</span>
                            <p class="text-gray-700">{{ $transaksi->catatan_pembayaran }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="mb-6 text-xs text-gray-500">
                        @if($transaksi->stokDetails && $transaksi->stokDetails->isNotEmpty())
                            @foreach($transaksi->stokDetails as $sd)
                            <p><strong class="text-gray-700">{{ $sd->stokTelurKeluar->gudang->nama_gudang ?? '-' }}:</strong> {{ number_format($sd->jumlah_butir) }} butir &middot; SJ: {{ $sd->stokTelurKeluar->no_referensi ?? '-' }}</p>
                            @endforeach
                        @elseif($transaksi->stokTelurKeluar && $transaksi->stokTelurKeluar->gudang)
                            <p><strong class="text-gray-700">Gudang:</strong> {{ $transaksi->stokTelurKeluar->gudang->nama_gudang }}</p>
                            <p><strong class="text-gray-700">Surat Jalan:</strong> {{ $transaksi->stokTelurKeluar->no_referensi }}</p>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-8 mt-16 pt-4 text-center text-sm">
                    <div class="flex flex-col justify-end min-h-[140px]">
                        <p class="font-semibold mb-2">Petugas,</p>
                        @if($transaksi->ttdPetugas)
                            @if($transaksi->ttd_petugas_img)
                                <img src="{{ $transaksi->ttd_petugas_img }}" class="mx-auto h-12 mb-1" alt="TTD">
                            @endif
                            <p class="text-xs font-medium">{{ $transaksi->ttdPetugas->name }}</p>
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($transaksi->ttd_petugas_at)->format('d/m/Y H:i') }}</p>
                        @elseif(auth()->user()->hasAnyRole(['petugas_kandang','super_admin','driver']))
                            <form method="POST" action="{{ route('penjualan.ttd', $transaksi) }}" class="no-print" onsubmit="return submitTTD(this, 'invoice')">
                                @csrf
                                <input type="hidden" name="signature" id="sig_invoice">
                                <canvas id="canvas_invoice" width="280" height="80" class="border border-gray-300 rounded mx-auto cursor-crosshair bg-white"></canvas>
                                <div class="mt-1 space-x-1">
                                    <button type="button" onclick="clearCanvas('canvas_invoice')" class="px-2 py-0.5 text-xs text-red-600 hover:bg-red-50 rounded">Hapus</button>
                                    <button type="submit" class="px-3 py-0.5 text-xs bg-indigo-600 text-white rounded hover:bg-indigo-700">✍ Simpan TTD</button>
                                </div>
                            </form>
                        @else
                            <p class="border-t border-gray-400 pt-2">___________</p>
                        @endif
                    </div>
                    <div class="flex flex-col justify-end min-h-[140px]">
                        <p class="font-semibold mb-2">Customer,</p>
                        <p class="border-t border-gray-400 pt-2">___________</p>
                        <p class="text-xs text-gray-500">{{ $transaksi->customer->nama_customer ?? '' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-center space-x-3 mt-6 no-print">
            <button onclick="window.print()" class="px-6 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 shadow">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak
            </button>
            <a href="{{ route('penjualan.invoice.download', $transaksi) }}" class="px-6 py-2.5 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 shadow">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download PDF
            </a>
        </div>
    </div>

<script>
(function(){var c=document.getElementById('canvas_invoice');if(!c)return;var ctx=c.getContext('2d');ctx.strokeStyle='#000';ctx.lineWidth=2;ctx.lineCap='round';var drawing=false;
function getPos(e){var r=c.getBoundingClientRect();var x=(e.touches?e.touches[0].clientX:e.clientX)-r.left;var y=(e.touches?e.touches[0].clientY:e.clientY)-r.top;return{x:x,y:y};}
function start(e){e.preventDefault();drawing=true;var p=getPos(e);ctx.beginPath();ctx.moveTo(p.x,p.y);}
function move(e){if(!drawing)return;e.preventDefault();var p=getPos(e);ctx.lineTo(p.x,p.y);ctx.stroke();}
function stop(){drawing=false;}
c.addEventListener('mousedown',start);c.addEventListener('mousemove',move);c.addEventListener('mouseup',stop);c.addEventListener('mouseleave',stop);
c.addEventListener('touchstart',start);c.addEventListener('touchmove',move);c.addEventListener('touchend',stop);
})();
function clearCanvas(id){var c=document.getElementById(id);if(c){c.getContext('2d').clearRect(0,0,c.width,c.height);}}
function submitTTD(form,pos){var c=document.getElementById('canvas_'+pos);if(!c)return true;var img=c.toDataURL('image/png');if(!img||img==='data:,')return confirm('TTD kosong. Tetap simpan tanpa tanda tangan?');document.getElementById('sig_'+pos).value=img;return true;}
</script>
</body>
</html>
