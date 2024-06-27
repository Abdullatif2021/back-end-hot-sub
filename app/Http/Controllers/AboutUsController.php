<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AboutUs;
use Exception;

class AboutUsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $aboutUs = AboutUs::all();

            return response()->json([
                'success' => true,
                'message' => 'AboutUs retrieved successfully',
                'data' => $aboutUs,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve aboutUs',
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
            $request->validate([
                'title' => 'required|string',
                'description' => 'required|string',
                'en_title' => 'required|string',
                'en_description' => 'required|string',
                'fr_title' => 'required|string',
                'fr_description' => 'required|string',
                
            ]);

            $aboutUs = AboutUs::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'AboutUs created successfully',
                'data' => $aboutUs,
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
            $aboutUs = AboutUs::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'AboutUs retrieved successfully',
                'data' => $aboutUs,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'AboutUs not found',
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
                'title' => 'required|string',
                'description' => 'required|string',
                'en_title' => 'required|string',
                'en_description' => 'required|string',
                'fr_title' => 'required|string',
                'fr_description' => 'required|string',
                
            ]);

            $aboutUs = AboutUs::findOrFail($id);
            $aboutUs->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'AboutUs updated successfully',
                'data' => $aboutUs,
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
                'message' => 'AboutUs not found',
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
            $aboutUs = AboutUs::findOrFail($id);
            $aboutUs->delete();

            return response()->json([
                'success' => true,
                'message' => 'AboutUs deleted successfully',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'AboutUs not found',
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
