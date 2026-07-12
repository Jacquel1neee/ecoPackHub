<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category', 'variants', 'vendors')->get();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $vendors = Vendor::where('is_active', true)->get();
        return view('admin.products.create', compact('categories', 'vendors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'code' => 'required|unique:products',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'material' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // Variants validation
            'variants.*.size' => 'nullable|string',
            'variants.*.packing_quantity' => 'required|string',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
            // Vendors validation
            'vendors' => 'nullable|array',
            'vendors.*.id' => 'exists:vendors,id',
            'vendors.*.price' => 'required|numeric|min:0',
            'vendors.*.is_preferred' => 'boolean',
        ]);

        $product = new Product($request->except('image', 'variants', 'vendors'));

        if ($request->hasFile('image')) {
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('uploads/products'), $imageName);
            $product->image_path = 'uploads/products/' . $imageName;
            $product->image = $request->file('image')->getClientOriginalName();
        }

        $product->save();

        // Save variants
        if ($request->has('variants')) {
            foreach ($request->variants as $variantData) {
                if (!empty($variantData['packing_quantity']) && isset($variantData['price'])) {
                    $product->variants()->create([
                        'size' => $variantData['size'] ?? 'Standard',
                        'packing_quantity' => $variantData['packing_quantity'],
                        'price' => $variantData['price'],
                        'stock' => $variantData['stock'] ?? 0,
                    ]);
                }
            }
        }

        // Attach vendors
        if ($request->has('vendors')) {
            foreach ($request->vendors as $vendorData) {
                if (!empty($vendorData['id']) && isset($vendorData['price'])) {
                    $product->vendors()->attach($vendorData['id'], [
                        'price' => $vendorData['price'],
                        'is_preferred' => $vendorData['is_preferred'] ?? false,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully with ' . $product->variants->count() . ' variants and ' . $product->vendors->count() . ' vendors!');
    }

    public function show(Product $product)
    {
        $product->load('category', 'variants', 'vendors');
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $vendors = Vendor::where('is_active', true)->get();
        $product->load('variants', 'vendors');
        return view('admin.products.edit', compact('product', 'categories', 'vendors'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'code' => 'required|unique:products,code,' . $product->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'material' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // Variants validation
            'variants.*.size' => 'nullable|string',
            'variants.*.packing_quantity' => 'required|string',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
            // Vendors validation
            'vendors' => 'nullable|array',
            'vendors.*.id' => 'exists:vendors,id',
            'vendors.*.price' => 'required|numeric|min:0',
            'vendors.*.is_preferred' => 'boolean',
        ]);

        $product->fill($request->except('image', 'variants', 'vendors'));

        if ($request->hasFile('image')) {
            if ($product->image_path && file_exists(public_path($product->image_path))) {
                unlink(public_path($product->image_path));
            }
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('uploads/products'), $imageName);
            $product->image_path = 'uploads/products/' . $imageName;
            $product->image = $request->file('image')->getClientOriginalName();
        }

        $product->save();

        // ===== Update Variants =====
        // Delete existing variants and recreate
        $product->variants()->delete();

        if ($request->has('variants')) {
            foreach ($request->variants as $variantData) {
                if (!empty($variantData['packing_quantity']) && isset($variantData['price'])) {
                    $product->variants()->create([
                        'size' => $variantData['size'] ?? 'Standard',
                        'packing_quantity' => $variantData['packing_quantity'],
                        'price' => $variantData['price'],
                        'stock' => $variantData['stock'] ?? 0,
                    ]);
                }
            }
        }

        // ===== Update Vendors =====
        if ($request->has('vendors')) {
            $syncData = [];
            foreach ($request->vendors as $vendorData) {
                if (!empty($vendorData['id']) && isset($vendorData['price'])) {
                    $syncData[$vendorData['id']] = [
                        'price' => $vendorData['price'],
                        'is_preferred' => $vendorData['is_preferred'] ?? false,
                    ];
                }
            }
            $product->vendors()->sync($syncData);
        } else {
            $product->vendors()->detach();
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully with ' . $product->variants->count() . ' variants and ' . $product->vendors->count() . ' vendors!');
    }

    public function destroy(Product $product)
    {
        if ($product->image_path && file_exists(public_path($product->image_path))) {
            unlink(public_path($product->image_path));
        }
        
        $product->variants()->delete();
        $product->vendors()->detach(); // Remove vendor relationships
        $product->delete();
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully!');
    }
}