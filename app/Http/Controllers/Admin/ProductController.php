<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category', 'variants')->get();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
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
        ]);

        $product = new Product($request->except('image', 'variants'));

        if ($request->hasFile('image')) {
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('uploads/products'), $imageName);
            $product->image_path = 'uploads/products/' . $imageName;
            $product->image = $request->file('image')->getClientOriginalName();
        }

        $product->save();

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

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully with ' . $product->variants->count() . ' variants!');
    }

    public function show(Product $product)
    {
        $product->load('category', 'variants');
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $product->load('variants');
        return view('admin.products.edit', compact('product', 'categories'));
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
            'variants.*.size' => 'nullable|string',
            'variants.*.packing_quantity' => 'required|string',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
        ]);

        $product->fill($request->except('image', 'variants'));

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

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully with ' . $product->variants->count() . ' variants!');
    }

    public function destroy(Product $product)
    {
        if ($product->image_path && file_exists(public_path($product->image_path))) {
            unlink(public_path($product->image_path));
        }
        
        $product->variants()->delete();
        $product->delete();
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully!');
    }
}