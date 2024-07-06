<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Storage;

use Exception;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    try {
        // Get the number of items per page from the request or default to 10
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');
        $sortColumn = $request->input('sort_column', 'id');
        $sortDirection = $request->input('sort_direction', 'asc');

        // Query the items with search, sorting and pagination
        $query = Item::with('category')
            ->where('en_name', 'LIKE', "%$search%")
            ->orWhere('fr_name', 'LIKE', "%$search%")
            ->orWhere('price', 'LIKE', "%$search%")
            ->orWhereHas('category', function ($query) use ($search) {
                $query->where('en_name', 'LIKE', "%$search%");
            });

        // Apply sorting
        $query->orderBy($sortColumn, $sortDirection);

        // Paginate the result
        $items = $query->paginate($perPage);

        // Update image URLs for each item
        foreach ($items as $item) {
            if ($item->image) {
                $item->image = url(Storage::url($item->image));
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Items retrieved successfully',
            'data' => $items,
        ], 200);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to retrieve items',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'en_name' => 'required|string|max:255',
                'fr_name' => 'required|string|max:255',
                'en_ingredients' => 'required|string',
                'fr_ingredients' => 'required|string',
                'price' => 'required|numeric',
                'image' => 'nullable|image|max:2048',
            ]);

            // $item = Item::create($request->all());
            $item = new Item($validatedData);
    
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('items', 'public');
                $item->image = $path;
            }
    
            $item->save();
            return response()->json([
                'success' => true,
                'message' => 'Item created successfully',
                'data' => $item,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
{
    try {
        $item = Item::with('category')->findOrFail($id);

        // Update image URL for the item
        if ($item->image) {
            $item->image = url(Storage::url($item->image));
        }

        return response()->json([
            'success' => true,
            'message' => 'Item retrieved successfully',
            'data' => $item,
        ], 200);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Item not found',
        ], 404);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An unexpected error occurred',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'category_id' => 'sometimes|exists:categories,id',
                'en_name' => 'sometimes|string|max:255',
                'fr_name' => 'sometimes|string|max:255',
                'en_ingredients' => 'sometimes|string',
                'fr_ingredients' => 'sometimes|string',
                'price' => 'sometimes|numeric',
                'image' => 'nullable|image|max:2048',
            ]);
    
            // Find the item by ID
            $item = Item::findOrFail($id);
    
            // Handle image upload if present
            if ($request->hasFile('image')) {
                // Delete the old image if it exists
                if ($item->image) {
                    Storage::disk('public')->delete($item->image);
                }
                // Store the new image
                $path = $request->file('image')->store('items', 'public');
                $validatedData['image'] = $path;
            }
    
            // Update the item with validated data
            $item->update($validatedData);
    
            // Return the updated item
            return response()->json([
                'success' => true,
                'message' => 'Item updated successfully',
                'data' => $item,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $item = Item::findOrFail($id);
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item deleted successfully',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
