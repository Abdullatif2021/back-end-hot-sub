<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    // Fetch all complaints
    public function index()
    {
        try {
            $complaints = Complaint::with('user')->get();
            return response()->json([
                'success' => true,
                'data' => $complaints,
                'message' => 'Complaints retrieved successfully',
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

    // Fetch complaints based on building ID
    public function complaintsByBuilding(Request $request)
{
    try {
        $authAdmin = Auth::user(); // Authenticated admin

        // Retrieve all building IDs managed by the admin
        $buildingIds = $authAdmin->buildings->pluck('id');

        // Fetch complaints for users in those buildings
        $complaints = Complaint::whereHas('user', function ($query) use ($buildingIds) {
            $query->whereIn('building_id', $buildingIds);
        })->get();

        return response()->json([
            'success' => true,
            'data' => $complaints,
            'message' => 'Complaints retrieved successfully',
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

    // Update complaint status
    public function updateStatus(Request $request, $id)
    {
        try {
            $complaint = Complaint::findOrFail($id);
            $complaint->update(['status' => $request->status]);
            return response()->json([
                'success' => true,
                'data' => $complaint,
                'message' => 'Complaint status updated successfully',
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
    public function store(Request $request)
    {
        try {
            // Validate the incoming request
            $this->validate($request, [
                'type' => 'required|string|max:255',
                'description' => 'required|string',
            ]);

            // Retrieve the authenticated user
            $user = Auth::user();

            // Create a new complaint using the authenticated user's ID
            $complaint = new Complaint();
            $complaint->type = $request->type;
            $complaint->description = $request->description;
            $complaint->user_id = $user->id; // Set the user ID from the authenticated user
            // Populate other fields as necessary
            $complaint->save();

            return response()->json([
                'success' => true,
                'data' => $complaint,
                'message' => 'Complaint added successfully',
                'status' => 201
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
                'status' => 422
            ], 422);
        } catch (\Exception $e) {
            // Handle general errors
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
    // Delete a complaint
    public function destroy($id)
    {
        try {
            $complaint = Complaint::findOrFail($id);
            $complaint->delete();
            return response()->json([
                'success' => true,
                'message' => 'Complaint deleted successfully',
                'status' => 200
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Complaint not found',
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

    // Add other necessary methods...
}

