<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\aboutUsImages;
use Illuminate\Support\Facades\Storage;

use Exception;

class AboutUsImagesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    try {
        $images = aboutUsImages::all();

        // Update image URLs for each item
        foreach ($images as $image) {
            if ($image->image) {
                $image->image = url(Storage::url($image->image));
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Images retrieved successfully',
            'data' => $images,
        ], 200);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to retrieve Images',
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
                'name' => 'sometimes|string|max:255',
                'image' => 'nullable|image|max:2048',
            ]);

            // $item = Item::create($request->all());
            $image = new aboutUsImages($validatedData);
    
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('images', 'public');
                $image->image = $path;
            }
    
            $image->save();
            return response()->json([
                'success' => true,
                'message' => 'Image created successfully',
                'data' => $image,
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
            $image = aboutUsImages::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Image retrieved successfully',
                'data' => $image,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Image not found',
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
            $request->validate([
              'name' => 'sometimes|string|max:255',
              
                'image' => 'nullable|image|max:2048',
            ]);

            $image = aboutUsImages::findOrFail($id);
            $image->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Image updated successfully',
                'data' => $image,
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
                'message' => 'Image not found',
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
            $image = aboutUsImages::findOrFail($id);
            $image->delete();

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Image not found',
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
