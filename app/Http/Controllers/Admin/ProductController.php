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

    public function enhanceImages(Request $request, Product $product)
    {
        $request->validate([
            'ai_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $sourcePath = null;

        if ($request->hasFile('ai_image')) {
            $sourcePath = $this->storeTemporaryImage($request->file('ai_image'));
        } elseif ($product->image_path && file_exists(public_path($product->image_path))) {
            $sourcePath = public_path($product->image_path);
        } elseif ($product->image && file_exists(public_path('uploads/products/' . $product->image))) {
            $sourcePath = public_path('uploads/products/' . $product->image);
        } elseif ($product->image && file_exists(public_path($product->image))) {
            $sourcePath = public_path($product->image);
        }

        if (!$sourcePath || !file_exists($sourcePath)) {
            return response()->json([
                'success' => false,
                'message' => 'Please upload a product image first.',
            ], 422);
        }

        $outputDir = public_path('uploads/products/ai-generated/' . $product->id);
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        foreach (glob($outputDir . '/*') as $file) {
            @unlink($file);
        }

        $variants = $this->generateAiEnhancementVariants($sourcePath, $outputDir, $product);

        $imagePayload = array_map(function (string $path) {
            $relativePath = ltrim(str_replace('\\', '/', str_replace(public_path(), '', $path)), '/');
            return [
                'path' => $relativePath,
                'url' => asset($relativePath),
            ];
        }, $variants);

        return response()->json([
            'success' => true,
            'images' => $imagePayload,
            'message' => 'Generated 4 polished AI-enhanced variations.',
        ]);
    }

    public function applyAiImage(Request $request, Product $product)
    {
        $request->validate([
            'image_path' => 'required|string',
        ]);

        $imagePath = $request->input('image_path');
        $publicPath = null;

        if (Str::startsWith($imagePath, ['http://', 'https://'])) {
            $publicPath = $imagePath;
        } else {
            $publicPath = Str::startsWith($imagePath, '/') ? public_path(ltrim($imagePath, '/')) : public_path($imagePath);
        }

        if ($publicPath && is_string($publicPath) && $publicPath !== $imagePath && file_exists($publicPath)) {
            $relativePath = ltrim(str_replace('\\', '/', str_replace(public_path(), '', $publicPath)), '/');
            $product->image_path = $relativePath;
            $product->image = basename($relativePath);
            $product->save();

            return response()->json([
                'success' => true,
                'image_url' => asset($relativePath),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'The selected image could not be applied.',
        ], 422);
    }

    public function generateDescription(Request $request, ?Product $product = null)
    {
        $request->validate([
            'ai_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $sourcePath = null;

        if ($request->hasFile('ai_image')) {
            $sourcePath = $this->storeTemporaryImage($request->file('ai_image'));
        } elseif ($product->image_path && file_exists(public_path($product->image_path))) {
            $sourcePath = public_path($product->image_path);
        } elseif ($product->image && file_exists(public_path('uploads/products/' . $product->image))) {
            $sourcePath = public_path('uploads/products/' . $product->image);
        }

        if (!$sourcePath || !file_exists($sourcePath)) {
            return response()->json([
                'success' => false,
                'message' => 'Please upload a product image first.',
            ], 422);
        }

        $prompt = 'Analyze this product image and write a short, attractive product description for an e-commerce store. Keep it concise, under 30 words, and suitable for a product listing.';
        $description = $this->generateDescriptionWithOpenAi($sourcePath, $prompt);

        if (!$description) {
            return response()->json([
                'success' => false,
                'message' => 'The AI description could not be generated right now.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'description' => trim($description),
        ]);
    }

    private function storeTemporaryImage($uploadedFile): string
    {
        $directory = public_path('uploads/products/ai-input');
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $fileName = time() . '_' . Str::slug(pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $uploadedFile->getClientOriginalExtension();
        $uploadedFile->move($directory, $fileName);

        return $directory . DIRECTORY_SEPARATOR . $fileName;
    }

    private function generateAiEnhancementVariants(string $sourcePath, string $outputDir, Product $product): array
    {
        $apiKey = config('services.openai.api_key');
        if ($apiKey) {
            try {
                return $this->generateWithOpenAi($sourcePath, $outputDir, $product);
            } catch (\Throwable $e) {
                // Fallback to local rendering if the OpenAI API is unavailable.
            }
        }

        return $this->generateLocalFallbackVariants($sourcePath, $outputDir);
    }

    private function generateDescriptionWithOpenAi(string $sourcePath, string $prompt): ?string
    {
        $apiKey = config('services.openai.api_key');
        if (!$apiKey) {
            return null;
        }

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $prompt,
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => 'data:image/jpeg;base64,' . base64_encode(file_get_contents($sourcePath)),
                            ],
                        ],
                    ],
                ],
            ],
            'max_tokens' => 80,
        ]));

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error || !$response) {
            return null;
        }

        $decoded = json_decode($response, true);
        if (empty($decoded['choices'][0]['message']['content'])) {
            return null;
        }

        return is_array($decoded['choices'][0]['message']['content'])
            ? $decoded['choices'][0]['message']['content'][0]['text'] ?? null
            : $decoded['choices'][0]['message']['content'];
    }

    private function generateWithOpenAi(string $sourcePath, string $outputDir, Product $product): array
    {
        $apiKey = config('services.openai.api_key');
        $model = config('services.openai.image_model', 'gpt-image-1');

        $ch = curl_init('https://api.openai.com/v1/images/edits');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiKey,
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'model' => $model,
            'prompt' => 'Create four polished e-commerce product photography variations for "' . $product->name . '". Keep the product prominent, clean background, professional lighting, commercial style, and high detail.',
            'n' => 4,
            'size' => '1024x1024',
            'image' => new \CURLFile($sourcePath),
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error || !$response) {
            throw new \RuntimeException($error ?: 'OpenAI response was empty.');
        }

        $decoded = json_decode($response, true);
        if (empty($decoded['data']) || !is_array($decoded['data'])) {
            throw new \RuntimeException('OpenAI did not return usable images.');
        }

        $saved = [];
        foreach ($decoded['data'] as $index => $item) {
            if (empty($item['b64_json'])) {
                continue;
            }

            $content = base64_decode($item['b64_json']);
            $fileName = 'variant-' . ($index + 1) . '.png';
            file_put_contents($outputDir . DIRECTORY_SEPARATOR . $fileName, $content);
            $saved[] = $outputDir . DIRECTORY_SEPARATOR . $fileName;
        }

        if (empty($saved)) {
            throw new \RuntimeException('OpenAI response did not include downloadable image data.');
        }

        return $saved;
    }

    private function generateLocalFallbackVariants(string $sourcePath, string $outputDir): array
    {
        $saved = [];

        if (!function_exists('imagecreatefromstring')) {
            copy($sourcePath, $outputDir . DIRECTORY_SEPARATOR . 'variant-1.jpg');
            copy($sourcePath, $outputDir . DIRECTORY_SEPARATOR . 'variant-2.jpg');
            copy($sourcePath, $outputDir . DIRECTORY_SEPARATOR . 'variant-3.jpg');
            copy($sourcePath, $outputDir . DIRECTORY_SEPARATOR . 'variant-4.jpg');
            return [
                $outputDir . DIRECTORY_SEPARATOR . 'variant-1.jpg',
                $outputDir . DIRECTORY_SEPARATOR . 'variant-2.jpg',
                $outputDir . DIRECTORY_SEPARATOR . 'variant-3.jpg',
                $outputDir . DIRECTORY_SEPARATOR . 'variant-4.jpg',
            ];
        }

        $image = @imagecreatefromstring(file_get_contents($sourcePath));
        if (!$image) {
            return $saved;
        }

        $width = imagesx($image);
        $height = imagesy($image);
        $presets = [
            ['brightness' => 18, 'contrast' => 9, 'bg' => [248, 248, 248]],
            ['brightness' => -6, 'contrast' => 12, 'bg' => [255, 251, 244]],
            ['brightness' => 10, 'contrast' => -4, 'bg' => [250, 245, 236]],
            ['brightness' => 14, 'contrast' => 8, 'bg' => [245, 250, 255]],
        ];

        foreach ($presets as $index => $preset) {
            $canvas = imagecreatetruecolor(1024, 1024);
            $bg = imagecolorallocate($canvas, $preset['bg'][0], $preset['bg'][1], $preset['bg'][2]);
            imagefill($canvas, 0, 0, $bg);

            $scaledWidth = 760;
            $scaledHeight = (int) round(($height / $width) * 760);
            $resized = imagecreatetruecolor($scaledWidth, $scaledHeight);
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $scaledWidth, $scaledHeight, $width, $height);
            imagecopy($canvas, $resized, 132, 132, 0, 0, $scaledWidth, $scaledHeight);

            imagefilter($canvas, IMG_FILTER_BRIGHTNESS, $preset['brightness']);
            imagefilter($canvas, IMG_FILTER_CONTRAST, $preset['contrast']);

            $fileName = 'variant-' . ($index + 1) . '.jpg';
            imagejpeg($canvas, $outputDir . DIRECTORY_SEPARATOR . $fileName, 92);
            imagedestroy($canvas);
            imagedestroy($resized);
            $saved[] = $outputDir . DIRECTORY_SEPARATOR . $fileName;
        }

        imagedestroy($image);

        return $saved;
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