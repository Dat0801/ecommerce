<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\VariantAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VariantAttributeController extends Controller
{
    /**
     * Display a listing of variant attributes.
     */
    public function index()
    {
        $attributes = VariantAttribute::where('is_active', true)
            ->with('values')
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $attributes,
        ]);
    }

    /**
     * Store a newly created variant attribute (Admin).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:variant_attributes,slug',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if (!isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $attribute = VariantAttribute::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Variant attribute created successfully',
            'data' => $attribute->load('values'),
        ], 201);
    }

    /**
     * Update the specified variant attribute (Admin).
     */
    public function update(Request $request, $id)
    {
        $attribute = VariantAttribute::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|unique:variant_attributes,slug,' . $id,
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $attribute->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Variant attribute updated successfully',
            'data' => $attribute->fresh()->load('values'),
        ]);
    }

    /**
     * Remove the specified variant attribute (Admin).
     */
    public function destroy($id)
    {
        $attribute = VariantAttribute::findOrFail($id);
        $attribute->delete();

        return response()->json([
            'success' => true,
            'message' => 'Variant attribute deleted successfully',
        ]);
    }

    /**
     * Add value to attribute (Admin).
     */
    public function addValue(Request $request, $id)
    {
        $attribute = VariantAttribute::findOrFail($id);

        $validated = $request->validate([
            'value' => 'required|string|max:255',
            'display_value' => 'nullable|string|max:255',
            'color_code' => 'nullable|string|max:7',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $value = $attribute->values()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Attribute value added successfully',
            'data' => $value,
        ], 201);
    }
}
