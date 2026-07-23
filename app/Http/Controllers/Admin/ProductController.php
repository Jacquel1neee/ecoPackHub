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
        $products = Product::with(['category', 'variants.vendor'])
                           ->orderBy('created_at', 'desc')
                           ->paginate(20);
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
            'discount_price' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'is_discount_active' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // Variants validation
            'variants.*.size' => 'nullable|string',
            'variants.*.packing_quantity' => 'required|string',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.vendor_price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.vendor_id' => 'required|exists:vendors,id',
        ]);

        $productData = $request->except('image', 'variants', 'vendors');
        $productData['discount_price'] = $request->filled('discount_price') ? $request->discount_price : null;
        $productData['discount_percentage'] = $request->filled('discount_percentage') ? $request->discount_percentage : null;
        $productData['is_discount_active'] = $request->boolean('is_discount_active')
            && ($productData['discount_price'] !== null || $productData['discount_percentage'] !== null);

        $product = new Product($productData);

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
                        'vendor_id' => $variantData['vendor_id'],
                        'size' => $variantData['size'] ?? 'Standard',
                        'packing_quantity' => $variantData['packing_quantity'],
                        'price' => $variantData['price'],
                        'vendor_price' => $variantData['vendor_price'],
                        'stock' => $variantData['stock'] ?? 0,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully with ' . $product->variants->count() . ' variants!');
    }

    public function show(Product $product)
    {
        $product->load('category', 'variants.vendor');
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $vendors = Vendor::where('is_active', true)->get();
        $product->load('variants.vendor');
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
            'discount_price' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'is_discount_active' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // Variants validation
            'variants.*.size' => 'nullable|string',
            'variants.*.packing_quantity' => 'required|string',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.vendor_price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.vendor_id' => 'required|exists:vendors,id',
        ]);

        $productData = $request->except('image', 'variants', 'vendors');
        $productData['discount_price'] = $request->filled('discount_price') ? $request->discount_price : null;
        $productData['discount_percentage'] = $request->filled('discount_percentage') ? $request->discount_percentage : null;
        $productData['is_discount_active'] = $request->boolean('is_discount_active')
            && ($productData['discount_price'] !== null || $productData['discount_percentage'] !== null);

        $product->fill($productData);

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
                        'vendor_id' => $variantData['vendor_id'],
                        'size' => $variantData['size'] ?? 'Standard',
                        'packing_quantity' => $variantData['packing_quantity'],
                        'price' => $variantData['price'],
                        'vendor_price' => $variantData['vendor_price'],
                        'stock' => $variantData['stock'] ?? 0,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully with ' . $product->variants->count() . ' variants!');
    }

    public function destroy(Product $product)
    {
        if ($product->image_path && file_exists(public_path($product->image_path))) {
            unlink(public_path($product->image_path));
        }
        
        $product->variants()->delete();
        $product->vendors()->detach(); // Remove legacy product vendor relationships
        $product->delete();
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully!');
    }
}