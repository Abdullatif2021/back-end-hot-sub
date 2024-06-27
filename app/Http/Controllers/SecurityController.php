<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Security;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
class SecurityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    try {
        $admin = Auth::user(); // Get the authenticated admin
        // Assuming an admin can manage multiple buildings, fetch all related building IDs
        $buildingIds = $admin->buildings->pluck('id');

        // If the admin manages at least one building, filter securities by those building IDs
        if ($buildingIds->isNotEmpty()) {
            $securities = Security::with('building')->whereIn('building_id', $buildingIds)->get();
            if (!$securities->isNotEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No buildings managed by the admin or admin does not have the permission to view securities.',
                    'data' => [],
                ], 403); 
            }else {
                return response()->json([
                    'success' => true,
                    'message' => 'Securities retrieved successfully',
                    'data' => $securities,
                ], 200);
            }
        } else {
            $securities = Security::with('building')->get(); // Assuming a 'building' relationship exists

            // If the admin does not manage any buildings, return an empty collection or handle as needed
            return response()->json([
                'success' => true,
                'message' => 'Securities retrieved successfully',
                'data' => $securities,
            ], 200);// 403 Forbidden or another appropriate status code
        }

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Securities retrieved successfully',
        //     'data' => $securities,
        // ], 200);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to retrieve securities',
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
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:securities',
                'password' => 'required|string|min:8',
                'building_id' => 'required|exists:buildings,id', // Ensure the building exists
            ]);
        
            $security = Security::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'building_id' => $request->building_id,
            ]);
    
            // Assign the 'security' role by ID
            $roleId = 4; // ID of the 'security' role
            $security->roles()->attach($roleId); // Attach the role to the security using the role ID
    
            return response()->json([
                'success' => true,
                'message' => 'Security created successfully',
                'data' => $security,
                'status' => 201
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation exceptions
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
                'status' => 422
            ], 422);
        } catch (ModelNotFoundException $e) {
            // Handle model not found exception
            return response()->json([
                'success' => false,
                'message' => 'Model not found',
                'error' => $e->getMessage(),
                'status' => 404
            ], 404);
        } catch (Exception $e) {
            // Handle general exceptions
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $security = Security::with('building')->findOrFail($id);
    
            return response()->json([
                'success' => true,
                'message' => 'Security retrieved successfully',
                'data' => $security,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Security not found',
                'status' => 404
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
     * Show the form for editing the specified resource.
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|max:255|unique:securities,email,' . $id,
                'password' => 'nullable|string|min:8',
                'building_id' => 'sometimes|exists:buildings,id',
            ]);
    
            $security = Security::findOrFail($id);
            
            $security->update([
                'name' => $request->name ?? $security->name,
                'email' => $request->email ?? $security->email,
                'password' => $request->filled('password') ? Hash::make($request->password) : $security->password,
                'building_id' => $request->building_id ?? $security->building_id,
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Security updated successfully',
                'data' => $security,
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
                'message' => 'Security not found',
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
        $security = Security::findOrFail($id);
        $security->delete();

        return response()->json([
            'success' => true,
            'message' => 'Security deleted successfully',
        ], 200);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Security not found',
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
