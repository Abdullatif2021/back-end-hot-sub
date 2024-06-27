<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\Storage;

use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Exception;

class ServiceApiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $services = Service::all();
    
            // Get the language preference from the request headers
            $preferredLanguage = $request->header('language');
    
            // Update the image attribute to have the full URL and adjust the name based on language preference
            $services->each(function ($service) use ($preferredLanguage) {
                if ($service->image) {
                    $service->image = url(Storage::url($service->image));
                }
                // Check if the preferred language is Arabic
              
                // Optionally unset the ar_name to clean up the response
                unset($service->ar_name);
            });
    
            return response()->json([
                'success' => true,
                'message' => 'Services retrieved successfully',
                'data' => $services,
                'status' => 200
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
                'data' => [],
                'status' => 500
            ], 500);
        }
    }
    
    public function show($id)
    {
        $service = Service::findOrFail($id);
        $service->image = url(Storage::url($service->image));
    
        return response()->json([
            'success' => true,
            'data' => $service,
            'status' => 200
        ]);
    }
    
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'ar_name' => 'required|string|max:255',
                'image' => 'required|image|max:2048', 
            ]);
    
            $service = new Service($validatedData);
    
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('services', 'public');
                $service->image = $path;
            }
    
            $service->save();
    
            return response()->json([
                'success' => true,
                'message' => 'Service created successfully',
                'data' => $service,
                'status' => 201
            ], 201);
    
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (Exception $e) {
            
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }
    

    public function update(Request $request, $id)
{
    try {
        $service = Service::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048', // Image validation
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('services', 'public');
            $validatedData['image'] = $path;
        }

        $service->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Service updated successfully',
            'data' => $service,
            'status' => 200
        ], 200);

    } catch (ValidationException $e) {
        return response()->json(['errors' => $e->errors()], 422);
    } catch (Exception $e) {
        return response()->json(['error' => 'An unexpected error occurred'], 500);
    }
}


    public function destroy($id)
    {
        try {
            $service = Service::findOrFail($id);
            $service->delete();
            return response()->json([
                'success' => true,
                'message' => 'Service deleted successfully',
                'status' => 200
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
}
