<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Exception;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Log;
class UserApiController extends Controller
{
    public function index()
    {
        try {
            $authUser = Auth::user();
            Log::info('Authenticated User', ['user' => $authUser]);

            // Check if the authenticated user is a super admin
            if ($authUser->hasRole('superadmin')) {
                // Fetch all users for a super admin
                $users = User::with('requests')->get();
                $count = $users->count();

                return response()->json([
                    'success' => true,
                    'message' => 'All users retrieved successfully',
                    'data' => $users,
                    'count' => $count,
                    'status' => 200
                ], 200);
            } elseif ($authUser->hasRole('admin')) {
                // Retrieve all building IDs managed by the admin
                $buildingIds = $authUser->buildings->pluck('id');

                // Check if the admin manages any buildings
                if ($buildingIds->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Admin does not manage any buildings',
                        'status' => 400
                    ], 400);
                }

                // Fetch users associated with those buildings
                $users = User::with('requests')->whereIn('building_id', $buildingIds)->get();
                $count = $users->count();

                return response()->json([
                    'success' => true,
                    'message' => 'Users of managed buildings retrieved successfully',
                    'data' => $users,
                    'count' => $count,
                    'status' => 200
                ], 200);
            } else {
                // Return error for users without admin or super admin roles
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                    'status' => 403
                ], 403);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
   

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|max:255',
                'number' => 'required',
                'email' => 'required|email|unique:users',
                'gender' => 'required',
                'apartment_number' => 'required|integer',
                'password' => 'required',
                'building_id' => 'required|exists:buildings,id'
            ]);
    
            $validatedData['password'] = bcrypt($validatedData['password']);
            $user = User::create($validatedData);
            $roleId = 3; 
            $user->roles()->attach($roleId); 
            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user,
                'status' => 201
            ], 201);
    
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
                'status' => 422
            ], 422);
    
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database operation failed',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
    
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = User::with('requests')->findOrFail($id);
    
            return response()->json([
                'success' => true,
                'message' => 'User retrieved successfully',
                'data' => $user,
                'status' => 200
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'status' => 404
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
    

    // Update a user by ID
    public function update(Request $request, $id)
{
    try {
        // Find the user by ID, throw an exception if not found
        $user = User::findOrFail($id);

        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'sometimes|max:255',
            'number' => 'sometimes',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'gender' => 'sometimes',
            'fcm' => 'sometimes',
            'apartment_number' => 'sometimes|integer',
            'password' => 'sometimes',
            'building_id' => 'sometimes|exists:buildings,id'
        ]);

        // If password is provided, hash it
        if (isset($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        }

        // Update the user with validated data
        $user->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user,
            'status' => 200
        ], 200);

    } catch (ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors' => $e->errors(),
            'status' => 422
        ], 422);

    } catch (QueryException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Database operation failed',
            'error' => $e->getMessage(),
            'status' => 500
        ], 500);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An unexpected error occurred',
            'error' => $e->getMessage(),
            'status' => 500
        ], 500);
    }
}
public function profile(Request $request)
{
    try {
        // Retrieve the authenticated user
        $user = $request->user();

        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'sometimes|max:255',
            'number' => 'sometimes',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'gender' => 'sometimes',
            'fcm' => 'sometimes',
            'apartment_number' => 'sometimes|integer',
            'password' => 'sometimes',
            'building_id' => 'sometimes|exists:buildings,id'
        ]);

     

        // Update the user with validated data
        $user->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user,
            'status' => 200
        ], 200);

    } catch (ValidationException $e) {
        // Handle validation exception
        return response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors' => $e->errors(),
            'status' => 422
        ], 422);

    } catch (QueryException $e) {
        // Handle query exception
        return response()->json([
            'success' => false,
            'message' => 'Database operation failed',
            'error' => $e->getMessage(),
            'status' => 500
        ], 500);

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
    // Delete a user by ID
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
    
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully',
                'status' => 200
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'status' => 404
            ], 404);
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
