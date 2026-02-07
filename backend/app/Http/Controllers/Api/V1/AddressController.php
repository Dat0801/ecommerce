<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = auth('sanctum')->id();
        $type = $request->get('type', 'shipping'); // shipping or billing

        $addresses = Address::where('user_id', $userId)
            ->where('type', $type)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $addresses,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:shipping,billing',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'nullable|string|max:100',
            'is_default' => 'nullable|boolean',
        ]);

        $userId = auth('sanctum')->id();

        return DB::transaction(function () use ($validated, $userId) {
            // If this is set as default, unset other defaults of the same type
            if ($validated['is_default'] ?? false) {
                Address::where('user_id', $userId)
                    ->where('type', $validated['type'])
                    ->update(['is_default' => false]);
            }

            $address = Address::create([
                'user_id' => $userId,
                ...$validated,
                'is_default' => $validated['is_default'] ?? false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Address created successfully',
                'data' => $address,
            ], 201);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $userId = auth('sanctum')->id();
        $address = Address::where('user_id', $userId)->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $address,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'type' => 'sometimes|in:shipping,billing',
            'name' => 'sometimes|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address_line_1' => 'sometimes|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'sometimes|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'sometimes|string|max:20',
            'country' => 'nullable|string|max:100',
            'is_default' => 'nullable|boolean',
        ]);

        $userId = auth('sanctum')->id();
        $address = Address::where('user_id', $userId)->findOrFail($id);

        return DB::transaction(function () use ($validated, $userId, $address) {
            // If this is set as default, unset other defaults of the same type
            if (isset($validated['is_default']) && $validated['is_default']) {
                Address::where('user_id', $userId)
                    ->where('type', $address->type)
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }

            $address->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Address updated successfully',
                'data' => $address->fresh(),
            ]);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $userId = auth('sanctum')->id();
        $address = Address::where('user_id', $userId)->findOrFail($id);
        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully',
        ]);
    }

    /**
     * Set an address as default
     */
    public function setDefault(Request $request, $id)
    {
        $userId = auth('sanctum')->id();
        $address = Address::where('user_id', $userId)->findOrFail($id);

        return DB::transaction(function () use ($userId, $address) {
            // Unset other defaults of the same type
            Address::where('user_id', $userId)
                ->where('type', $address->type)
                ->update(['is_default' => false]);

            // Set this address as default
            $address->update(['is_default' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Address set as default',
                'data' => $address->fresh(),
            ]);
        });
    }
}
