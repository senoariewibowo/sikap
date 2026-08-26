<?php

namespace App\Http\Controllers;

use App\Models\HargaTelur;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HargaTelurController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $sort = $request->get('sort', 'tanggal_mulai_berlaku');
        $order = $request->get('order', 'desc');
        $allowedSorts = ['harga', 'satuan', 'tanggal_mulai_berlaku', 'customer'];

        if (!in_array($sort, $allowedSorts)) $sort = 'tanggal_mulai_berlaku';
        if (!in_array($order, ['asc', 'desc'])) $order = 'desc';

        $query = HargaTelur::with('customer');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('satuan', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn($c) => $c->where('nama_customer', 'like', "%{$search}%"));
            });
        }
        if ($sort === 'customer') {
            $query->leftJoin('customer', 'harga_telur.customer_id', '=', 'customer.id')
                  ->orderBy('customer.nama_customer', $order)
                  ->select('harga_telur.*');
        } else {
            $query->orderBy($sort, $order);
        }
        $hargas = $query->paginate(20)->withQueryString();

        $hargaButir = HargaTelur::whereNull('customer_id')->where('satuan', 'per_butir')->orderBy('tanggal_mulai_berlaku', 'desc')->first();
        $hargaKg = HargaTelur::whereNull('customer_id')->where('satuan', 'per_kg')->orderBy('tanggal_mulai_berlaku', 'desc')->first();
        $hargaKarpet = HargaTelur::whereNull('customer_id')->where('satuan', 'per_karpet')->orderBy('tanggal_mulai_berlaku', 'desc')->first();
        $hargaPeti = HargaTelur::whereNull('customer_id')->where('satuan', 'per_peti')->orderBy('tanggal_mulai_berlaku', 'desc')->first();
        return view('harga.index', compact('hargas', 'hargaButir', 'hargaKg', 'hargaKarpet', 'hargaPeti', 'search', 'sort', 'order'));
    }

    public function create()
    {
        $customers = Customer::where('status', 'aktif')->orderBy('nama_customer')->get();
        return view('harga.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $customerId = $request->filled('customer_id') ? $request->customer_id : null;
        $request->validate([
            'harga' => 'required|numeric|min:0',
            'satuan' => [
                'required',
                'in:per_butir,per_kg,per_karpet,per_peti',
                Rule::unique('harga_telur', 'satuan')->where(fn ($q) => $q->where('customer_id', $customerId)),
            ],
            'customer_id' => 'nullable|exists:customer,id',
            'tanggal_mulai_berlaku' => 'required|date',
        ], [
            'satuan.unique' => 'Harga untuk kombinasi customer dan satuan ini sudah ada.',
        ]);
        $data = $request->all();
        $data['customer_id'] = $customerId;
        $data['created_by'] = auth()->id();
        HargaTelur::create($data);
        return redirect()->route('harga.index')->with('success', 'Harga telur berhasil ditambahkan.');
    }

    public function edit(HargaTelur $harga)
    {
        $customers = Customer::where('status', 'aktif')->orderBy('nama_customer')->get();
        return view('harga.edit', compact('harga', 'customers'));
    }

    public function update(Request $request, HargaTelur $harga)
    {
        $customerId = $request->filled('customer_id') ? $request->customer_id : null;
        $request->validate([
            'harga' => 'required|numeric|min:0',
            'satuan' => [
                'required',
                'in:per_butir,per_kg,per_karpet,per_peti',
                Rule::unique('harga_telur', 'satuan')->where(fn ($q) => $q->where('customer_id', $customerId))->ignore($harga->id),
            ],
            'customer_id' => 'nullable|exists:customer,id',
            'tanggal_mulai_berlaku' => 'required|date',
        ], [
            'satuan.unique' => 'Harga untuk kombinasi customer dan satuan ini sudah ada.',
        ]);
        $data = $request->all();
        $data['customer_id'] = $customerId;
        $harga->update($data);
        return redirect()->route('harga.index')->with('success', 'Harga berhasil diperbarui.');
    }

    public function destroy(HargaTelur $harga) { $harga->delete(); return redirect()->route('harga.index')->with('success', 'Harga dihapus.'); }
}
