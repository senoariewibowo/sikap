<?php
namespace App\Exports;
use App\Models\TransaksiPenjualan;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class PenjualanExport implements FromQuery, WithHeadings, WithMapping, WithTitle
{
    protected $customerId, $dari, $sampai;
    public function __construct($customerId, $dari, $sampai) { $this->customerId=$customerId; $this->dari=$dari; $this->sampai=$sampai; }
    public function query() { return TransaksiPenjualan::with(['customer','stokTelurKeluar'])->when($this->customerId,fn($q)=>$q->where('customer_id',$this->customerId))->whereBetween('tanggal',[$this->dari,$this->sampai])->orderBy('tanggal'); }
    public function headings(): array { return ['Tanggal','Customer','Butir','Berat (kg)','Harga/Satuan','Total','DP','Sisa','Pembayaran','No Invoice']; }
    public function map($row): array { return [$row->tanggal->format('d/m/Y'),$row->customer->nama_customer ?? '-',$row->jumlah_butir,$row->berat_kg,$row->harga_per_satuan,$row->total_harga,$row->dp,$row->total_harga-$row->dp,$row->status_pembayaran=='lunas'?'Lunas':'Belum Lunas',$row->no_invoice ?: '-']; }
    public function title(): string { return 'Penjualan Telur'; }
}
