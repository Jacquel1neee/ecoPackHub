<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PackingQuantityOption;
use Illuminate\Http\Request;

class PackingQuantityOptionController extends Controller
{
    public function index()
    {
        $options = PackingQuantityOption::orderBy('name')->paginate(20);
        return view('admin.packing-quantity-options.index', compact('options'));
    }

    public function create()
    {
        return view('admin.packing-quantity-options.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:packing_quantity_options,name',
            'is_active' => 'nullable|boolean',
        ]);

        PackingQuantityOption::create([
            'name' => $validated['name'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.packing-quantity-options.index')
            ->with('success', 'Packing quantity option created successfully!');
    }

    public function edit(PackingQuantityOption $packingQuantityOption)
    {
        return view('admin.packing-quantity-options.edit', compact('packingQuantityOption'));
    }

    public function update(Request $request, PackingQuantityOption $packingQuantityOption)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:packing_quantity_options,name,' . $packingQuantityOption->id,
            'is_active' => 'nullable|boolean',
        ]);

        $packingQuantityOption->update([
            'name' => $validated['name'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.packing-quantity-options.index')
            ->with('success', 'Packing quantity option updated successfully!');
    }

    public function destroy(PackingQuantityOption $packingQuantityOption)
    {
        $packingQuantityOption->delete();

        return redirect()->route('admin.packing-quantity-options.index')
            ->with('success', 'Packing quantity option deleted successfully!');
    }
}
