<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductGroup;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $groupId = $request->get('group_id');

        $products = Product::with(['group', 'prices'])
            ->when($search, fn($q) => $q->where('product_name', 'like', "%{$search}%")
                ->orWhere('barcode', 'like', "%{$search}%"))
            ->when($groupId, fn($q) => $q->where('group_id', $groupId))
            ->orderBy('product_name')->paginate(10)->withQueryString();

        $groups = ProductGroup::orderBy('group_name')->get();

        return view('kasir.product.index', compact('products', 'groups', 'search', 'groupId'));
    }

    public function create()
    {
        $groups = ProductGroup::orderBy('group_name')->get();
        return view('kasir.product.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateProduct($request);

        $product = Product::create([
            'barcode' => $request->barcode ?: null,
            'product_name' => $request->product_name,
            'modal' => $request->modal,
            'group_id' => $request->group_id ?: null,
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->syncPrices($product, $request);

        return redirect()->route('kasir.product.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $product->load('prices');
        $groups = ProductGroup::orderBy('group_name')->get();
        return view('kasir.product.edit', compact('product', 'groups'));
    }

    public function update(Request $request, Product $product)
    {
        $this->validateProduct($request, $product->id);

        $product->update([
            'barcode' => $request->barcode ?: null,
            'product_name' => $request->product_name,
            'modal' => $request->modal,
            'group_id' => $request->group_id ?: null,
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->syncPrices($product, $request);

        return redirect()->route('kasir.product.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('kasir.product.index')->with('success', 'Produk berhasil dihapus.');
    }

    private function validateProduct(Request $request, ?int $productId = null): array
    {
        $rules = [
            'barcode' => 'nullable|string|max:100|unique:products,barcode' . ($productId ? ',' . $productId : ''),
            'product_name' => 'required|string|max:150',
            'modal' => 'nullable|numeric|min:0',
            'group_id' => 'nullable|exists:product_groups,id',
            'is_active' => 'nullable|boolean',
            'tier' => 'array',
            'tier.*' => 'nullable|numeric|min:0',
            'min_qty' => 'array',
            'min_qty.*' => 'nullable|integer|min:1',
        ];

        return $request->validate($rules);
    }

    private function syncPrices(Product $product, Request $request): void
    {
        $product->prices()->delete();

        $tiers = $request->input('tier', []);
        $minQtys = $request->input('min_qty', []);

        foreach ($tiers as $index => $harga) {
            if ($harga === null || $harga === '') continue;
            $product->prices()->create([
                'tier' => $index + 1,
                'min_qty' => (int) ($minQtys[$index] ?? 1),
                'harga' => (float) $harga,
            ]);
        }
    }
}
