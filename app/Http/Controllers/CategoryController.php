<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Exception;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categories = Category::all();

            return response()->json([
                'success' => true,
                'message' => 'Categories retrieved successfully',
                'data' => $categories,
            ], 200);
        } catch (Exception $e) {
            return $this->errorResponse($e, 500);
        }
    }
    public function categoriesWithItems()
    {
        try {
            $categories = Category::with('items')->get();

            return response()->json([
                'success' => true,
                'message' => 'Categories with items retrieved successfully',
                'data' => $categories,
            ], 200);
        } catch (Exception $e) {
            return $this->errorResponse($e, 500);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'index' => 'required|integer|min:0|max:255',
                                'en_name' => 'required|string|max:255',
                'fr_name' => 'required|string|max:255',
            ]);

            $category = Category::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'data' => $category,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse($e, 422);
        } catch (Exception $e) {
            return $this->errorResponse($e, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $category = Category::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Category retrieved successfully',
                'data' => $category,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse($e, 404);
        } catch (Exception $e) {
            return $this->errorResponse($e, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
    try {
        // Validate incoming request data
        $request->validate([
            'index' => 'sometimes|integer|min:0|max:255',
            'en_name' => 'sometimes|required|string|max:255',
            'fr_name' => 'sometimes|required|string|max:255',
        ]);

        // Find the category by ID, if not found, it will throw a ModelNotFoundException
        $category = Category::findOrFail($id);

        // Update the category with validated data
        $category->update($request->only(['index', 'en_name', 'fr_name']));

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'data' => $category,
        ], 200);
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Handle validation exceptions
        return $this->errorResponse($e, 422);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        // Handle model not found exception
        return $this->errorResponse($e, 404);
    } catch (Exception $e) {
        // Handle any other exceptions
        return $this->errorResponse($e, 500);
    }
}
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Find the category by ID
            $category = Category::findOrFail($id);
    
            // Check if the category has any associated items
            if ($category->items()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category cannot be deleted because it has associated items.',
                ], 400);
            }
    
            // Delete the category if no items are associated with it
            $category->delete();
    
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse($e, 404);
        } catch (Exception $e) {
            return $this->errorResponse($e, 500);
        }
    }

    /**
     * Error response.
     */
    private function errorResponse($e, $statusCode)
    {
        return response()->json([
            'success' => false,
            'message' => $e instanceof \Illuminate\Validation\ValidationException ? 'Validation error' : 'An unexpected error occurred',
            'error' => $e->getMessage(),
        ], $statusCode);
    }
}