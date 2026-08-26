<?php

namespace App\Exports;

use App\Models\StokPakan;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class PakanExport implements FromQuery, WithHeadings, WithMapping, WithTitle
{
    protected $kandangId, $dari, $sampai;

    public function __construct($kandangId, $dari, $sampai)
    {
        $this->kandangId = $kandangId;
        $this->dari = $dari;
        $this->sampai = $sampai;
    }

    public function query()
    {
        return StokPakan::with(['jenisPakan', 'kandang', 'user'])
            ->when($this->kandangId, fn($q) => $q->where('kandang_id', $this->kandangId))
            ->whereBetween('tanggal', [$this->dari, $this->sampai])
            ->orderBy('tanggal');
    }

    public function headings(): array
    {
        return ['Tanggal', 'Jenis', 'Kategori', 'Tipe', 'Jumlah', 'Satuan', 'Kandang', 'Keterangan', 'Input Oleh'];
    }

    public function map($row): array
    {
        return [
            $row->tanggal->format('d/m/Y'),
            $row->jenisPakan->nama ?? '-',
            $row->jenisPakan->kategori ?? '-',
            $row->tipe == 'masuk' ? 'Masuk' : 'Keluar',
            $row->jumlah_kg,
            $row->jenisPakan->satuan ?? 'kg',
            $row->kandang->nama_kandang ?? 'Gudang Pusat',
            $row->keterangan ?: '-',
            $row->user->name ?? '-',
        ];
    }

    public function title(): string
    {
        return 'Penggunaan Pakan';
    }
}
