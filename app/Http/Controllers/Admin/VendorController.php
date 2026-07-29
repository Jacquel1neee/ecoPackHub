<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PackingQuantityOption;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::withCount('products')->orderBy('name')->paginate(20);
        return view('admin.vendors.index', compact('vendors'));
    }

    public function create()
    {
        $products = Product::with(['vendors', 'variants'])->orderBy('name')->get();
        $packingQuantityOptions = PackingQuantityOption::where('is_active', true)->orderBy('name')->get();
        return view('admin.vendors.create', compact('products', 'packingQuantityOptions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:vendors',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'products' => 'nullable|array',
            'products.*.selected' => 'nullable|boolean',
            'products.*.quantity' => 'nullable|integer|min:0',
            'products.*.packing_quantity_option_id' => 'nullable|exists:packing_quantity_options,id',
            'products.*.price' => 'nullable|numeric|min:0',
            'variants' => 'nullable|array',
            'variants.*.selected' => 'nullable|boolean',
            'variants.*.quantity' => 'nullable|integer|min:0',
            'variants.*.packing_quantity_option_id' => 'nullable|exists:packing_quantity_options,id',
            'variants.*.price' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $validated) {
            $data = $request->only(['name', 'contact_person', 'email', 'phone', 'address']);
            $data['is_active'] = $request->boolean('is_active');

            $vendor = Vendor::create($data);
            if (! empty($validated['variants'] ?? [])) {
                $this->syncVendorVariants($vendor, $validated['variants'] ?? []);
            } else {
                $this->syncVendorProducts($vendor, $validated['products'] ?? []);
            }
        });

        return redirect()->route('admin.vendors.index')
            ->with('success', 'Vendor created successfully!');
    }

    public function edit(Vendor $vendor)
    {
        $vendor->load('products', 'products.variants');
        $products = Product::with(['vendors', 'variants'])->orderBy('name')->get();
        $packingQuantityOptions = PackingQuantityOption::where('is_active', true)->orderBy('name')->get();
        return view('admin.vendors.edit', compact('vendor', 'products', 'packingQuantityOptions'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:vendors,name,' . $vendor->id,
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'products' => 'nullable|array',
            'products.*.selected' => 'nullable|boolean',
            'products.*.quantity' => 'nullable|integer|min:0',
            'products.*.packing_quantity_option_id' => 'nullable|exists:packing_quantity_options,id',
            'products.*.price' => 'nullable|numeric|min:0',
            'variants' => 'nullable|array',
            'variants.*.selected' => 'nullable|boolean',
            'variants.*.quantity' => 'nullable|integer|min:0',
            'variants.*.packing_quantity_option_id' => 'nullable|exists:packing_quantity_options,id',
            'variants.*.price' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $validated, $vendor) {
            $data = $request->only(['name', 'contact_person', 'email', 'phone', 'address']);
            $data['is_active'] = $request->boolean('is_active');

            $vendor->update($data);
            if (! empty($validated['variants'] ?? [])) {
                $this->syncVendorVariants($vendor, $validated['variants'] ?? []);
            } else {
                $this->syncVendorProducts($vendor, $validated['products'] ?? []);
            }
        });

        return redirect()->route('admin.vendors.index')
            ->with('success', 'Vendor updated successfully!');
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->delete();

        return redirect()->route('admin.vendors.index')
            ->with('success', 'Vendor deleted successfully!');
    }

    private function syncVendorProducts(Vendor $vendor, array $productRows): void
    {
        $syncData = [];

        foreach ($productRows as $productId => $row) {
            if (! filter_var($row['selected'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
                continue;
            }

            if (! array_key_exists('price', $row) || $row['price'] === '' || $row['price'] === null) {
                continue;
            }

            $syncData[$productId] = [
                'price' => (float) $row['price'],
                'quantity' => isset($row['quantity']) && $row['quantity'] !== '' ? (int) $row['quantity'] : null,
                'packing_quantity_option_id' => $row['packing_quantity_option_id'] ?? null,
            ];
        }

        $vendor->products()->sync($syncData);
    }

    private function syncVendorVariants(Vendor $vendor, array $variantRows): void
    {
        $variantIds = collect(array_keys($variantRows))
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        if ($variantIds->isEmpty()) {
            $vendor->products()->sync([]);
            return;
        }

        $variantsById = ProductVariant::query()
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

        $optionIds = collect($variantRows)
            ->pluck('packing_quantity_option_id')
            ->filter(fn ($id) => $id !== null && $id !== '')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $optionsById = PackingQuantityOption::query()
            ->whereIn('id', $optionIds)
            ->get()
            ->keyBy('id');

        $selectedByProduct = [];

        foreach ($variantRows as $variantId => $row) {
            /** @var ProductVariant|null $variant */
            $variant = $variantsById->get((int) $variantId);
            if (! $variant) {
                continue;
            }

            $hasAnyValue = (isset($row['quantity']) && $row['quantity'] !== '')
                || (isset($row['packing_quantity_option_id']) && $row['packing_quantity_option_id'] !== '')
                || (isset($row['price']) && $row['price'] !== '');

            $isSelected = filter_var($row['selected'] ?? false, FILTER_VALIDATE_BOOLEAN) || $hasAnyValue;

            if (! $isSelected) {
                if ((int) $variant->vendor_id === (int) $vendor->id) {
                    $variant->update([
                        'vendor_id' => null,
                        'vendor_price' => null,
                        'vendor_quantity' => null,
                        'packing_quantity_option_id' => null,
                    ]);
                }
                continue;
            }

            if (! array_key_exists('price', $row) || $row['price'] === '' || $row['price'] === null) {
                continue;
            }

            $optionId = $row['packing_quantity_option_id'] ?? null;
            $optionId = ($optionId === '' || $optionId === null) ? null : (int) $optionId;
            $option = $optionId ? $optionsById->get($optionId) : null;
            $quantity = isset($row['quantity']) && $row['quantity'] !== '' ? (int) $row['quantity'] : null;

            $variant->update([
                'vendor_id' => $vendor->id,
                'vendor_price' => (float) $row['price'],
                'vendor_quantity' => $quantity,
                'packing_quantity_option_id' => $optionId,
                'packing_quantity' => $option?->name ?? $variant->packing_quantity,
            ]);

            $selectedByProduct[$variant->product_id][] = [
                'price' => (float) $row['price'],
                'quantity' => $quantity,
                'packing_quantity_option_id' => $optionId,
            ];
        }

        $syncData = [];
        foreach ($selectedByProduct as $productId => $rows) {
            $primary = $rows[0];
            $syncData[$productId] = [
                'price' => $primary['price'],
                'quantity' => $primary['quantity'],
                'packing_quantity_option_id' => $primary['packing_quantity_option_id'],
            ];
        }

        $vendor->products()->sync($syncData);
    }
}