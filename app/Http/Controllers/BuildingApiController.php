<?php

namespace App\Http\Controllers;
use App\Models\Building;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BuildingApiController extends Controller
{
       // Fetch all buildings and their users
       public function index()
       {
           return Building::with('users')->get();
       }
       public function count()
    {
        try {
            $count = Building::count();
            return response()->json([
                'success' => true,
                'message' => 'Building count retrieved successfully.',
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
                'building_number' => 'required|integer',
                'number_of_apartments' => 'required|integer',
                'number_of_floors' => 'required|integer',
                'admin_id' => 'required|exists:admins,id', 
            ]);

            $building = Building::create($validatedData);
            return response()->json($building, 201);

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
   
       // Fetch a specific building and its users by ID
       public function show($id)
       {
           return Building::with('users')->findOrFail($id);
       }
   
       public function update(Request $request, $id)
{
    try {
        $building = Building::findOrFail($id);
        $building->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Building updated successfully',
            'data' => $building,
            'status' => 200
        ], 200);

    } catch (ValidationException $e) {
        return response()->json(['errors' => $e->errors()], 422);
    } catch (QueryException $e) {
        return response()->json(['error' => 'Database operation failed'], 500);
    } catch (Exception $e) {
        return response()->json(['error' => 'An unexpected error occurred'], 500);
    }
}
public function destroy($id)
{
    try {
        $building = Building::findOrFail($id);
        $building->delete();

        return response()->json([
            'success' => true,
            'message' => 'Building deleted successfully',
            'status' => 200
        ], 200);

    } catch (ModelNotFoundException $e) {
        return response()->json(['error' => 'Building not found'], 404);
    } catch (QueryException $e) {
        return response()->json(['error' => 'Database operation failed'], 500);
    } catch (Exception $e) {
        return response()->json(['error' => 'An unexpected error occurred'], 500);
    }
}

}
