<?php

namespace App\Http\Controllers;

use App\Models\ProductGroup;
use Illuminate\Http\Request;

class ProductGroupController extends Controller
{
    public function index()
    {
        $groups = ProductGroup::withCount('products')->orderBy('group_name')->paginate(10);
        return view('kasir.group.index', compact('groups'));
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
