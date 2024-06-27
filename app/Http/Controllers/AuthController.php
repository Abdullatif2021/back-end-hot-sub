<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;

use App\Models\User; 

use App\Models\Admin;

use App\Models\Security;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

  
    public function login(Request $request)
{
    try {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        $credentials = $request->only('email', 'password');
    
        // Manually authenticate for admin
        $admin = Admin::where('email', $credentials['email'])->first();
        if ($admin && Hash::check($credentials['password'], $admin->password)) {
            $token = $admin->createToken('AdminApp')->plainTextToken;
            $buildingIds = $admin->buildings()->pluck('id');
            return response()->json([
                'success' => true,
                'message' => 'Login successful as Admin',
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'user' => $admin,
                    'role_id' => $admin->roles->first()->id,
                    'buildings' => $buildingIds
                ]
            ], 200);
        }
    
        // Manually authenticate for security
        $security = Security::where('email', $credentials['email'])->first();
        if ($security && Hash::check($credentials['password'], $security->password)) {
            $token = $security->createToken('SecurityApp')->plainTextToken;
            return response()->json([
                'success' => true,
                'message' => 'Login successful as Security',
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'user' => $security,
                    'role_id' => $security->roles->first()->id,
                ]
            ], 200);
        }
    
        // Manually authenticate for user
        $user = User::where('email', $credentials['email'])->first();
        if ($user && Hash::check($credentials['password'], $user->password)) {
            $token = $user->createToken('MyApp')->plainTextToken;
            return response()->json([
                'success' => true,
                'message' => 'Login successful as User',
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'user' => $user,
                    'role_id' => $user->roles->first()->id,
                ]
            ], 200);
        }
    
        // If none of the conditions are met
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials',
            'error' => 'No matching user found with the provided credentials.'
        ], 401);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Server error occurred',
            'error' => $e->getMessage(),
            'status' => 500
        ], 500);
    }
}

    public function logout(Request $request)
    {
        // For API tokens (both users and admins)
        if ($request->user('admin')) {
            $request->user('admin')->currentAccessToken()->delete();
        } elseif ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }
    
        // For stateful authentication (web)
        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
    
        return response()->json([
            "success" => true,
            "message" => 'Logged Out',
            "data" => [],
            "count" => 0,
            "status" => 200
        ], 200);
    }
}
