<?php

namespace App\Http\Controllers;
use App\Models\Admin; // Import the Admin model at the top
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Exception;
class AdminApiController extends Controller
{
    public function index()
{
    try {
        $admins = Admin::has('buildings')->with('buildings')->get();

        return response()->json([
            'success' => true,
            'message' => 'Admins with buildings retrieved successfully.',
            'data' => $admins,
            'status' => 200
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage(),
            'data' => [],
            'status' => 500
        ]);
    }
}

public function count()
{
    try {
        $count = Admin::has('buildings')->count();

        return response()->json([
            'success' => true,
            'message' => 'Count of admins with buildings retrieved successfully.',
            'data' => ['count' => $count],
            'status' => 200
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage(),
            'data' => [],
            'status' => 500
        ]);
    }
}

     public function store(Request $request)
     {
         try {
             // Validate the request data
             $validatedData = $request->validate([
                 'name' => 'required|string|max:255',
                 'email' => 'required|string|email|max:255|unique:admins',
                 'phone_number' => 'required|string|max:255',
                 'password' => 'required|string|min:6',
             ]);
     
             // Hash the password
             $validatedData['password'] = bcrypt($validatedData['password']);
     
             // Create the admin and return it
             $admin = Admin::create($validatedData);

             $roleId = 1; 
             $admin->roles()->attach($roleId); 
             return response()->json($admin, 201);
     
         } catch (ValidationException $e) {
             // Handle validation exceptions
             return response()->json(['errors' => $e->errors()], 422);
     
         } catch (QueryException $e) {
             // Handle database related exceptions
             return response()->json(['error' => 'Database operation failed'], 500);
     
         } catch (Exception $e) {
             // Handle general exceptions
             return response()->json(['error' => 'An unexpected error occurred'], 500);
         }
     }
     public function show($id)
{
    try {
        $admin = Admin::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Admin retrieved successfully',
            'data' => $admin,
            'status' => 200
        ], 200);
    } catch (ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Admin not found',
            'status' => 404
        ], 404);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An unexpected error occurred',
            'status' => 500
        ], 500);
    }
}

 
public function update(Request $request, $id)
{
    try {
        $admin = Admin::findOrFail($id);
        $admin->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Admin updated successfully',
            'data' => $admin,
            'status' => 200
        ], 200);
    } catch (ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Admin not found',
            'status' => 404
        ], 404);
    }catch (QueryException $e) {
        // Handle database related exceptions
        return response()->json(['error' => 'Database operation failed'], 500);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An unexpected error occurred',
            'status' => 500
        ], 500);
    }
}

 
public function destroy($id)
{
    try {
        $admin = Admin::findOrFail($id);
        $admin->delete();

        return response()->json([
            'success' => true,
            'message' => 'Admin deleted successfully',
            'status' => 200
        ], 200);
    } catch (ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Admin not found',
            'status' => 404
        ], 404);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An unexpected error occurred',
            'status' => 500
        ], 500);
    }
}

}
