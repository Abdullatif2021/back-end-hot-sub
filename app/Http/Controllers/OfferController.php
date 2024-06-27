<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Offer;
use Illuminate\Support\Facades\Storage;

use Exception;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    try {
        $offers = Offer::all();

        // Update image URLs for each item
        foreach ($offers as $offer) {
            if ($offer->image) {
                $offer->image = url(Storage::url($offer->image));
            }
        }
        foreach ($offers as $offer) {
            if ($offer->desc_image) {
                $offer->desc_image = url(Storage::url($offer->desc_image));
            }
        }
        foreach ($offers as $offer) {
            if ($offer->mob_image) {
                $offer->mob_image = url(Storage::url($offer->mob_image));
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Offers retrieved successfully',
            'data' => $offers,
        ], 200);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to retrieve Offers',
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
                'desc_image' => 'nullable|image|max:2048',
                'mob_image' => 'nullable|image|max:2048',
            ]);

            // $item = Item::create($request->all());
            $offer = new Offer($validatedData);
    
        // Handle the main image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('offers', 'public');
            $offer->image = $imagePath;
        }

        // Handle the description image upload
        if ($request->hasFile('desc_image')) {
            $descImagePath = $request->file('desc_image')->store('offers/desc_images', 'public');
            $offer->desc_image = $descImagePath;
        }

        // Handle the mobile image upload
        if ($request->hasFile('mob_image')) {
            $mobImagePath = $request->file('mob_image')->store('offers/mob_images', 'public');
            $offer->mob_image = $mobImagePath;
        }
    
            $offer->save();
            return response()->json([
                'success' => true,
                'message' => 'Offer created successfully',
                'data' => $offer,
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
            $offer = Offer::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Offer retrieved successfully',
                'data' => $offer,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Offer not found',
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

            $offer = Offer::findOrFail($id);
            $offer->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Offer updated successfully',
                'data' => $offer,
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
                'message' => 'Offer not found',
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
            $offer = Offer::findOrFail($id);
            $offer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Offer deleted successfully',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Offer not found',
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
