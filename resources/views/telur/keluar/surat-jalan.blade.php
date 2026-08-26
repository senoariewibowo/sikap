<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Surat Jalan - {{ $stokTelur->no_referensi ?: 'SJ' }}</title>
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
                <div class="text-center border-b-2 border-gray-800 pb-4 mb-6">
                    <h1 class="text-xl font-bold tracking-wide uppercase">Surat Jalan</h1>
                    <p class="text-sm text-gray-600">Sistem Informasi Kandang Ayam Petelur — SIKAP</p>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
                    <div>
                        <p class="text-gray-500 text-xs">No. Surat Jalan</p>
                        <p class="font-bold text-lg">{{ $stokTelur->no_referensi ?: '-' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-500 text-xs">Tanggal</p>
                        <p class="font-semibold">{{ \Carbon\Carbon::parse($stokTelur->tanggal)->format('d F Y') }}</p>
                    </div>
                </div>

                <div class="mb-6">
                    <p class="text-gray-500 text-xs uppercase tracking-wider font-semibold mb-1">Gudang Pengirim</p>
                    <div class="border rounded-lg p-4 bg-gray-50 text-sm">
                        @if($stokTelur->gudang)
                            <p class="font-semibold">{{ $stokTelur->gudang->kode_gudang }} — {{ $stokTelur->gudang->nama_gudang }}</p>
                            <p class="text-gray-600">{{ $stokTelur->gudang->lokasi }}</p>
                        @else
                            <p class="font-semibold">-</p>
                        @endif
                    </div>
                </div>

                <div class="mb-6">
                    <p class="text-gray-500 text-xs uppercase tracking-wider font-semibold mb-1">Driver / Pengemudi</p>
                    <p class="text-sm font-medium">{{ $stokTelur->driver ?: '-' }}</p>
                </div>

                <table class="w-full mb-6 text-sm border-collapse">
                    <thead>
                        <tr class="border-y-2 border-gray-800">
                            <th class="py-2 text-left">No</th>
                            <th class="py-2 text-left">Kandang Asal</th>
                            <th class="py-2 text-right">Jumlah (butir)</th>
                            <th class="py-2 text-right">Berat (kg)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stokTelur->details as $i => $d)
                        <tr class="border-b">
                            <td class="py-2">{{ $i + 1 }}</td>
                            <td class="py-2">{{ $d->sortasiDetail->sortasiTelur->kandang->nama_kandang ?? '-' }}</td>
                            <td class="py-2 text-right">{{ number_format($d->jumlah_butir) }}</td>
                            <td class="py-2 text-right">{{ number_format($d->berat_kg, 1) }}</td>
                        </tr>
                        @endforeach
                        <tr class="border-b-2 border-gray-800 font-semibold">
                            <td class="py-3" colspan="2">Total</td>
                            <td class="py-3 text-right">{{ number_format($stokTelur->jumlah_butir) }}</td>
                            <td class="py-3 text-right">{{ number_format($stokTelur->berat_kg, 1) }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="mb-6">
                    <p class="text-gray-500 text-xs uppercase tracking-wider font-semibold mb-1">Keterangan</p>
                    <p class="text-sm">{{ $stokTelur->keterangan ?: '-' }}</p>
                </div>

                <div class="grid grid-cols-3 gap-8 mt-16 pt-4 text-center text-sm">
                    <div class="flex flex-col justify-end min-h-[140px]">
                        <p class="font-semibold mb-2">Pengirim,</p>
                        @if($stokTelur->ttdPengirim)
                            <img src="{{ $stokTelur->ttd_pengirim_img }}" class="mx-auto h-12 mb-1" alt="TTD">
                            <p class="text-xs font-medium">{{ $stokTelur->ttdPengirim->name }}</p>
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($stokTelur->ttd_pengirim_at)->format('d/m/Y H:i') }}</p>
                        @elseif(auth()->user()->hasAnyRole(['petugas_kandang','super_admin']))
                            <form method="POST" action="{{ route('telur.keluar.ttd', $stokTelur) }}" class="no-print" onsubmit="return submitTTD(this, 'pengirim')">
                                @csrf
                                <input type="hidden" name="posisi" value="pengirim">
                                <input type="hidden" name="signature" id="sig_pengirim">
                                <canvas id="canvas_pengirim" width="280" height="80" class="border border-gray-300 rounded mx-auto cursor-crosshair bg-white"></canvas>
                                <div class="mt-1 space-x-1">
                                    <button type="button" onclick="clearCanvas('canvas_pengirim')" class="px-2 py-0.5 text-xs text-red-600 hover:bg-red-50 rounded">Hapus</button>
                                    <button type="submit" class="px-3 py-0.5 text-xs bg-indigo-600 text-white rounded hover:bg-indigo-700">✍ Simpan TTD</button>
                                </div>
                            </form>
                        @else
                            <p class="border-t border-gray-400 pt-2">___________</p>
                        @endif
                    </div>
                    <div class="flex flex-col justify-end min-h-[140px]">
                        <p class="font-semibold mb-2">Penerima,</p>
                        <p class="border-t border-gray-400 pt-2">___________</p>
                        <p class="text-xs text-gray-500">Ttd & Nama</p>
                    </div>
                    <div class="flex flex-col justify-end min-h-[140px]">
                        <p class="font-semibold mb-2">Mengetahui,</p>
                        @if($stokTelur->ttdMengetahui)
                            <img src="{{ $stokTelur->ttd_mengetahui_img }}" class="mx-auto h-12 mb-1" alt="TTD">
                            <p class="text-xs font-medium">{{ $stokTelur->ttdMengetahui->name }}</p>
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($stokTelur->ttd_mengetahui_at)->format('d/m/Y H:i') }}</p>
                        @elseif(auth()->user()->hasRole('super_admin'))
                            <form method="POST" action="{{ route('telur.keluar.ttd', $stokTelur) }}" class="no-print" onsubmit="return submitTTD(this, 'mengetahui')">
                                @csrf
                                <input type="hidden" name="posisi" value="mengetahui">
                                <input type="hidden" name="signature" id="sig_mengetahui">
                                <canvas id="canvas_mengetahui" width="280" height="80" class="border border-gray-300 rounded mx-auto cursor-crosshair bg-white"></canvas>
                                <div class="mt-1 space-x-1">
                                    <button type="button" onclick="clearCanvas('canvas_mengetahui')" class="px-2 py-0.5 text-xs text-red-600 hover:bg-red-50 rounded">Hapus</button>
                                    <button type="submit" class="px-3 py-0.5 text-xs bg-indigo-600 text-white rounded hover:bg-indigo-700">✍ Simpan TTD</button>
                                </div>
                            </form>
                        @else
                            <p class="border-t border-gray-400 pt-2">___________</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-center space-x-3 mt-6 no-print">
            <button onclick="window.print()" class="px-6 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 shadow">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak
            </button>
            <a href="{{ route('telur.keluar.surat-jalan.download', $stokTelur) }}" class="px-6 py-2.5 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 shadow">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download PDF
            </a>
        </div>
    </div>

<script>
(function(){var cvs={};function initCanvas(id){var c=document.getElementById(id);if(!c)return;cvs[id]=c;var ctx=c.getContext('2d');ctx.strokeStyle='#000';ctx.lineWidth=2;ctx.lineCap='round';var drawing=false;function getPos(e){var r=c.getBoundingClientRect();var x=(e.touches?e.touches[0].clientX:e.clientX)-r.left;var y=(e.touches?e.touches[0].clientY:e.clientY)-r.top;return{x:x,y:y};}
function start(e){e.preventDefault();drawing=true;var p=getPos(e);ctx.beginPath();ctx.moveTo(p.x,p.y);}
function move(e){if(!drawing)return;e.preventDefault();var p=getPos(e);ctx.lineTo(p.x,p.y);ctx.stroke();}
function stop(){drawing=false;}
c.addEventListener('mousedown',start);c.addEventListener('mousemove',move);c.addEventListener('mouseup',stop);c.addEventListener('mouseleave',stop);
c.addEventListener('touchstart',start);c.addEventListener('touchmove',move);c.addEventListener('touchend',stop);
}
initCanvas('canvas_pengirim');initCanvas('canvas_mengetahui');
})();
function clearCanvas(id){var c=document.getElementById(id);if(c){c.getContext('2d').clearRect(0,0,c.width,c.height);}}
function submitTTD(form,pos){var c=document.getElementById('canvas_'+pos);if(!c)return true;var img=c.toDataURL('image/png');if(!img||img==='data:,')return confirm('TTD kosong. Tetap simpan tanpa tanda tangan?');document.getElementById('sig_'+pos).value=img;return true;}
</script>
</body>
</html>
