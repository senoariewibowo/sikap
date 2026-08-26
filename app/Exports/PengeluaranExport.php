<?php

namespace App\Exports;

use App\Models\Pengeluaran;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class PengeluaranExport implements FromQuery, WithHeadings, WithMapping, WithTitle
{
    protected $kategoriId, $kandangId, $dari, $sampai;

    public function __construct($kategoriId, $kandangId, $dari, $sampai)
    {
        $this->kategoriId = $kategoriId;
        $this->kandangId = $kandangId;
        $this->dari = $dari;
        $this->sampai = $sampai;
    }

    public function query()
    {
        return Pengeluaran::with(['kategori', 'kandang'])
            ->when($this->kategoriId, fn($q) => $q->where('kategori_pengeluaran_id', $this->kategoriId))
            ->when($this->kandangId, fn($q) => $q->where('kandang_id', $this->kandangId))
            ->whereBetween('tanggal', [$this->dari, $this->sampai])
            ->orderBy('tanggal');
    }

    public function headings(): array
    {
        return ['Tanggal', 'Kategori', 'Kandang', 'Jumlah (Rp)', 'Keterangan'];
    }

    public function map($row): array
    {
        return [
            $row->tanggal->format('d/m/Y'),
            $row->kategori->nama ?? '-',
            $row->kandang->nama_kandang ?? '-',
            $row->jumlah,
            $row->keterangan ?: '-',
        ];
    }

    public function title(): string
    {
        return 'Pengeluaran';
    }
}
