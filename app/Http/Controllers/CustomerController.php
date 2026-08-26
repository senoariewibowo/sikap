<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $customers = Customer::when($search, fn($q) => $q->where('nama_customer', 'like', "%{$search}%")
                ->orWhere('kontak_person', 'like', "%{$search}%"))
            ->orderBy('nama_customer')->paginate(10)->withQueryString();
        return view('customer.index', compact('customers', 'search'));
    }

    public function create()
    {
        return view('customer.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_customer' => 'required|string|max:150|unique:customer,nama_customer',
            'tipe_customer' => 'required|in:agen,pengepul,retail,korporat',
            'alamat' => 'nullable|string|max:200',
            'no_hp' => 'nullable|string|max:20',
            'kontak_person' => 'nullable|string|max:100',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        Customer::create($request->all());
        return redirect()->route('customer.index')->with('success', 'Customer berhasil ditambahkan.');
    }

    public function edit(Customer $customer)
    {
        return view('customer.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'nama_customer' => 'required|string|max:150|unique:customer,nama_customer,' . $customer->id,
            'tipe_customer' => 'required|in:agen,pengepul,retail,korporat',
            'alamat' => 'nullable|string|max:200',
            'no_hp' => 'nullable|string|max:20',
            'kontak_person' => 'nullable|string|max:100',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $customer->update($request->all());
        return redirect()->route('customer.index')->with('success', 'Customer berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customer.index')->with('success', 'Customer dinonaktifkan.');
    }
}
