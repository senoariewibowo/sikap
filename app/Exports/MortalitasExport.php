<?php

namespace App\Exports;

use App\Models\PopulasiAyam;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class MortalitasExport implements FromQuery, WithHeadings, WithMapping, WithTitle
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
        return PopulasiAyam::with(['kandang', 'user'])
            ->when($this->kandangId, fn($q) => $q->where('kandang_id', $this->kandangId))
            ->whereBetween('tanggal', [$this->dari, $this->sampai])
            ->where(function ($q) {
                $q->where('jumlah_mati', '>', 0)->orWhere('jumlah_afkir', '>', 0);
            })
            ->orderBy('tanggal');
    }

    public function headings(): array
    {
        return ['Tanggal', 'Kandang', 'Mati', 'Afkir', 'Keterangan', 'Input Oleh'];
    }

    public function map($row): array
    {
        return [
            $row->tanggal->format('d/m/Y'),
            $row->kandang->nama_kandang ?? '-',
            $row->jumlah_mati,
            $row->jumlah_afkir,
            $row->keterangan ?: '-',
            $row->user->name ?? '-',
        ];
    }

    public function title(): string
    {
        return 'Mortalitas Ayam';
    }
}
