<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Request as RequestModel; // Rename the Request alias to avoid conflicts
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\Auth;
use App\Services\ExcelService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Services\FirebaseNotificationService;
class RequestApiController extends Controller
{
    protected $excelService;

    public function __construct(ExcelService $excelService)
    {
        $this->excelService = $excelService;
    }

    public function export(Request $request): BinaryFileResponse
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $buildingId = $request->input('building_id');

        $query = RequestModel::query();

        if ($startDate && $endDate) {
            $query->whereBetween('request_date', [$startDate, $endDate]);
        }

        if ($buildingId) {
            $query->whereHas('user', function($q) use ($buildingId) {
                $q->where('building_id', $buildingId);
            });
        }
        $requests = $query->get();

    // Prepare data for export
    $exportData = $requests->map(function ($request) {
        return [
             
            'ID' => $request->id,
            'Request Date' => $request->request_date,
            'Available Start Time' => $request->available_start_time,
            'Available End Time' => $request->available_end_time,
            'Status' => $request->status,
            'User Name' => $request->user ? $request->user->name : 'N/A', // Assuming 'name' is the field in the User model
            'Service Name' => $request->service ? $request->service->name : 'N/A', // Assuming 'name' is the field in the Service model
            'Description' => $request->description,
            'Created at' => $request->created_at,
        ];
    })->toArray();

    $filePath = $this->excelService->createExcelFile($exportData);

    return response()->download($filePath, 'requests.xlsx')->deleteFileAfterSend(true);
}
    
    public function index(Request $request)
    {
        try {
            $authUser = Auth::user(); 
            $query = RequestModel::with('user');
    
            if ($authUser->hasRole('admin')) {

                $buildingIds = $authUser->buildings->pluck('id');
                $query->whereHas('user', function ($q) use ($buildingIds) {
                    $q->whereIn('building_id', $buildingIds);
                });
            } elseif ($authUser->hasRole('superadmin')) {

            }
    
          
            if ($request->has('building_id') && $authUser->hasRole('super admin')) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('building_id', $request->building_id);
                });
            }
    
          
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
    
           
            if ($request->has('search')) {
                $search = $request->search;
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
    
            $requests = $query->paginate(10);
    
            return response()->json([
                "success" => true,
                "message" => "Requests retrieved successfully",
                "data" => $requests->items(),
                "count" => $requests->total(),
                "per_page" => $requests->perPage(),
                "current_page" => $requests->currentPage(),
                "total_pages" => $requests->lastPage(),
                "status" => 200
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => 'An error occurred: ' . $e->getMessage(),
                "data" => [],
                "count" => 0,
                "status" => 500
            ], 500);
        }
    }
    
    // Fetch all requests and their users
    public function index_2(Request $request)
    {
        try {
            $authAdmin = Auth::user(); // Authenticated admin
    
            // Retrieve all building IDs managed by the admin
            $buildingIds = $authAdmin->buildings->pluck('id');
    
            // Start building the query
            $query = RequestModel::with('user')
                ->whereHas('user', function ($query) use ($buildingIds) {
                    $query->whereIn('building_id', $buildingIds);
                });
    
            // Filter by status if provided
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
    
            // Search filter on users if provided
            if ($request->has('search')) {
                $search = $request->search;
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
    
            // Execute the query with pagination
            $requests = $query->paginate(10); // Adjust the pagination as needed
    
            return response()->json([
                "success" => true,
                "message" => "Requests retrieved successfully",
                "data" => $requests->items(),
                "count" => $requests->total(),
                "per_page" => $requests->perPage(),
                "current_page" => $requests->currentPage(),
                "total_pages" => $requests->lastPage(),
                "status" => 200
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => 'An error occurred: ' . $e->getMessage(),
                "data" => [],
                "count" => 0,
                "status" => 500
            ], 500);
        }
    }
    
    public function indexStatusCountForSuperadmin()
    {
        try {
            $statusCounts = RequestModel::groupBy('status')
                ->selectRaw('status, COUNT(*) as count')
                ->get();
    
            return response()->json([
                'success' => true,
                'message' => 'Status counts retrieved successfully',
                'data' => $statusCounts,
                'status' => 200
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
    public function indexStatusCountForAdmin()
{
    try {
        $authAdmin = Auth::user();
        $buildingIds = $authAdmin->buildings->pluck('id');

        $statusCounts = RequestModel::whereHas('user', function ($query) use ($buildingIds) {
            $query->whereIn('building_id', $buildingIds);
        })
        ->groupBy('status')
        ->selectRaw('status, COUNT(*) as count')
        ->get();

        return response()->json([
            'success' => true,
            'message' => 'Status counts retrieved successfully for managed buildings',
            'data' => $statusCounts,
            'status' => 200
        ], 200);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage(),
            'status' => 500
        ], 500);
    }
}

    // Fetch a specific request and its user by ID
    public function show(Request $request, $id)
    {
        try {
            $requestItem = RequestModel::with('user')->findOrFail($id);
    
          
                return response()->json([
                    "success" => true,
                    "message" => 'Request retrieved successfully.',
                    "data" => $requestItem,
                    "count" => 1,
                    "status" => 200
                ], 200);
           
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => 'An error occurred: ' . $e->getMessage(),
                "data" => [],
                "count" => 0,
                "status" => 500
            ], 500);
        }
    }
    // Fetch all requests created by the authenticated user
    public function userRequests(Request $request)
    {
        try {
            $requests = RequestModel::with('user')
                ->where('user_id', $request->user()->id)
                ->get();
    
            return response()->json([
                "success" => true,
                "message" => 'User-specific requests retrieved successfully.',
                "data" => $requests,
                "count" => count($requests),
                "status" => 200
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => 'An error occurred: ' . $e->getMessage(),
                "data" => [],
                "count" => 0,
                "status" => 500
            ], 500);
        }
    }
    // Fetch a specific request created by the authenticated user
    public function showUserRequest(Request $request, $id)
    {
        try {
            $requestItem = RequestModel::with('user')
                ->where('user_id', $request->user()->id)
                ->findOrFail($id);
    
            return response()->json([
                "success" => true,
                "message" => 'User-specific request retrieved successfully.',
                "data" => $requestItem,
                "count" => 1,
                "status" => 200
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => 'An error occurred: ' . $e->getMessage(),
                "data" => [],
                "count" => 0,
                "status" => 500
            ], 500);
        }
    }
    
    public function store(Request $request)
    {
        try {
            // Validate the incoming request data
            $validated = $request->validate([
                'available_start_time' => 'required|date',
                'available_end_time' => 'required|date|after:available_start_time',
                'service_id' => 'required|integer|exists:services,id',
                'description' => 'required|string',
                'image' => 'sometimes|file|image|max:5000', // Optional, file, must be an image, max 5MB
                // You can add 'request_date' validation if you decide it should follow a specific format
                // 'request_date' => 'sometimes|date',
            ]);

            // Set default status to 'pending'
            $status = 'pending';

            // Handle file upload if provided
            $imagePath = null;
            if ($request->hasFile('image')) {
                $filePath = $request->file('image')->store('images/requests', 'public');
                $imagePath = Storage::disk('public')->url($filePath);
            }

            // Create the Service Request
            $serviceRequest = RequestModel::create([
                'request_date' => 'null', // Now handled by validation if needed
                'available_time' => 'null',
                'available_start_time' => $validated['available_start_time'],
                'available_end_time' => $validated['available_end_time'],
                'status' => $status,
                'service_id' => $validated['service_id'],
                'description' => $validated['description'],
                'image' => $imagePath,
                // Assuming 'user_id' needs to be set, typically from authenticated user
                'user_id' => auth()->id(), // or however you obtain the current user's ID
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request created successfully',
                'data' => $serviceRequest
            ], 201); // 201 Created
         
        }  catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
                'status' => 422
            ], 422);
    
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database operation failed',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
    // Update a request by ID
    public function update(Request $request, $id)
{
    try {
        $requestModel = RequestModel::findOrFail($id);

        // If you have specific fields to update, use $request->only([...])
        $requestModel->update($request->all());
        if ($request->has('status')) {
            // Additional logic for when status is updated
            // For example, you might want to send a notification, log the change, etc.
            $this->handleStatusChange($requestModel);
        }
        return response()->json([
            'success' => true,
            'message' => 'Request updated successfully',
            'data' => $requestModel,
            'status' => 200
        ], 200);

    } catch (ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Request not found',
            'status' => 404
        ], 404);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage(),
            'status' => 500
        ], 500);
    }
}
protected function handleStatusChange($requestModel)
{
    $firebaseService = new FirebaseNotificationService();

    // Specify the device token, notification title, and body
    $to = "cCIaoqnoRPydbjYky2c9Lp:APA91bHMMO8GMJeV5v9j5oHiNfWxPd5F2cS-LElTj5Pm_A9i3qkuH45wWV-x0zdSrjpHcpZ2vhca5CldQ1uOjS3eUnOy2HwcfZzPCFkz4e4Jewh_dv1bIL9cHeErO1bAiMJM-zUJe9hs"; // Assuming you have device tokens stored against users
    $notification = [
        'title' => 'Status Update',
        'body' => 'Your request status has been updated.',
    ];
    $data = [
        'extraInformation' => 'Anything you want to pass along with the notification',
    ];

    $response = $firebaseService->sendNotification($to, $notification, $data);

    // Check the response to determine if the notification was sent successfully
    if (isset($response['success']) && $response['success'] == 1) {
        // Log success or perform further actions
        \Log::info('Notification sent successfully', ['response' => $response]);
    } else {
        // Log failure or handle it accordingly
        \Log::error('Failed to send notification', ['response' => $response]);
    }
}

    // Delete a request by ID
    public function destroy($id)
    {
        try {
            $requestModel = RequestModel::findOrFail($id);
            $requestModel->delete();
            return response()->json(['message' => 'Request deleted'], 200);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    // Handle validation exceptions
    private function handleValidationException(ValidationException $e)
    {
        return response()->json([
            'errors' => $e->errors(),
            'message' => 'Validation error',
            'status' => 422
        ], 422);
    }

    // Handle general exceptions
    private function handleException(Exception $e)
    {
        return response()->json([
            'message' => 'An error occurred',
            'error' => $e->getMessage(),
            'status' => 500
        ], 500);
    }
}
