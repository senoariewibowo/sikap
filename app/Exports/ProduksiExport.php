<?php
namespace App\Exports;
use App\Models\ProduksiTelur;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProduksiExport implements FromQuery, WithHeadings, WithMapping, WithTitle
{
    protected $kandangId, $dari, $sampai;
    public function __construct($kandangId, $dari, $sampai) { $this->kandangId=$kandangId; $this->dari=$dari; $this->sampai=$sampai; }
    public function query() { return ProduksiTelur::with(['kandang','user','setoran'])->when($this->kandangId,fn($q)=>$q->where('kandang_id',$this->kandangId))->whereBetween('tanggal',[$this->dari,$this->sampai])->orderBy('tanggal'); }
    public function headings(): array { return ['Tanggal','Kandang','Shift','Butir','Berat (kg)','Karpet','Peti','Pecah','Retak','Kopong','Sisa','Status Setor','Input Oleh']; }
    public function map($row): array {
        $s = $row->setoran;
        return [
            $row->tanggal->format('d/m/Y'),
            $row->kandang->nama_kandang ?? '-',
            $row->shift ?: '-',
            $row->jumlah_butir,
            $s->berat ?? '-',
            $s->karpet ?? '-',
            $s->peti ?? '-',
            $s->pecah ?? '-',
            $s->retak ?? '-',
            $s->kopong ?? '-',
            $s->sisa ?? '-',
            $row->status_setor === 'sudah_disetor' ? 'Sudah Disetor' : 'Belum',
            $row->user->name ?? '-',
        ];
    }
    public function title(): string { return 'Produksi Telur'; }
}
