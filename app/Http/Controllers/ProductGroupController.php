<?php

namespace App\Http\Controllers;

use App\Models\ProductGroup;
use Illuminate\Http\Request;

class ProductGroupController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'group_name');
        $order = strtolower($request->get('order', 'asc')) === 'desc' ? 'desc' : 'asc';
        $allowed = ['group_name', 'description', 'products_count'];
        if (!in_array($sort, $allowed, true)) {
            $sort = 'group_name';
        }

        $groups = ProductGroup::withCount('products')
            ->orderBy($sort === 'products_count' ? 'products_count' : $sort, $order)
            ->paginate(10)
            ->withQueryString();

        return view('kasir.group.index', compact('groups', 'sort', 'order'));
    }

    public function create()
    {
        return view('kasir.group.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'group_name' => 'required|string|max:150',
            'description' => 'nullable|string',
        ]);

        ProductGroup::create($request->all());
        return redirect()->route('kasir.group.index')->with('success', 'Grup produk berhasil ditambahkan.');
    }

    public function edit(ProductGroup $group)
    {
        return view('kasir.group.edit', compact('group'));
    }

    public function update(Request $request, ProductGroup $group)
    {
        $request->validate([
            'group_name' => 'required|string|max:150',
            'description' => 'nullable|string',
        ]);

        $group->update($request->all());
        return redirect()->route('kasir.group.index')->with('success', 'Grup produk berhasil diperbarui.');
    }

    public function destroy(ProductGroup $group)
    {
        $group->products()->update(['group_id' => null]);
        $group->delete();
        return redirect()->route('kasir.group.index')->with('success', 'Grup produk berhasil dihapus.');
    }
}
