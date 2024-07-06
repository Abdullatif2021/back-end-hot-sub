<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminApiController;
use App\Http\Controllers\BuildingApiController;
use App\Http\Controllers\RequestApiController;
use App\Http\Controllers\ServiceApiController;
use App\Http\Controllers\UserApiController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\AuthController; 
use App\Http\Controllers\SecurityController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\AboutUsImagesController;
use App\Http\Controllers\AboutUsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\DB; // Add this line

/*
|--------------------------------------------------------------------------
| Login & Logout
|--------------------------------------------------------------------------
*/

Route::post('login',  [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('logout',  [AuthController::class, 'logout']);

/*
|--------------------------------------------------------------------------
| Complaints CRUD
|--------------------------------------------------------------------------

*/
Route::middleware(['auth:sanctum'])->group(function () {

    Route::middleware(['role:admin|superadmin'])->group(function () {

        Route::apiResource('complaints', ComplaintController::class);

        Route::get('building-complaints', [ComplaintController::class, 'complaintsByBuilding']);

        Route::put('complaints/{id}/status', [ComplaintController::class, 'updateStatus']);

    });

    Route::middleware(['role:user'])->group(function () {

        Route::post('complaints', [ComplaintController::class, 'store']);

    });

});

/*
|--------------------------------------------------------------------------
| Admins CRUD
|--------------------------------------------------------------------------

*/

Route::post('admins', [AdminApiController::class, 'store']);
Route::middleware(['auth:sanctum'])->group(function () {
    
    Route::middleware(['role:superadmin'])->group(function () {

        
        Route::get('admins/count', [AdminApiController::class, 'count']);
        
        Route::get('admins', [AdminApiController::class, 'index']);

        Route::get('admins/{id}', [AdminApiController::class, 'show']);

        Route::put('admins/{id}', [AdminApiController::class, 'update']);

        Route::delete('admins/{id}', [AdminApiController::class, 'destroy']);
    });
});
/*
|--------------------------------------------------------------------------
| Offer CRUD
|--------------------------------------------------------------------------
*/

// Route::middleware(['auth:sanctum'])->group(function () {

//     Route::middleware(['role:superadmin'])->group(function () {

        Route::get('offers/count', [OfferController::class, 'count']);

        Route::put('offers/{id}', [OfferController::class, 'update']);

        Route::get('offers', [OfferController::class, 'index']);

        Route::post('offers', [OfferController::class, 'store']);

        Route::get('offers/{id}', [OfferController::class, 'show']);

        Route::delete('offers/{id}', [OfferController::class, 'destroy']);
//     });
// });
/*
|--------------------------------------------------------------------------
| Settings CRUD
|--------------------------------------------------------------------------
*/
// Route::middleware(['auth:sanctum'])->group(function () {
//     Route::middleware(['role:superadmin'])->group(function () {
        Route::get('settings', [SettingController::class, 'index']);
        Route::post('settings', [SettingController::class, 'store']);
        Route::get('settings/{id}', [SettingController::class, 'show']);
        Route::put('settings/{id}', [SettingController::class, 'update']);
        Route::delete('settings/{id}', [SettingController::class, 'destroy']);
//     });
// });
/*
|--------------------------------------------------------------------------
| About Us Images CRUD
|--------------------------------------------------------------------------
*/

// Route::middleware(['auth:sanctum'])->group(function () {

//     Route::middleware(['role:superadmin'])->group(function () {

Route::get('images/count', [AboutUsImagesController::class, 'count']);

Route::put('images/{id}', [AboutUsImagesController::class, 'update']);

Route::get('images', [AboutUsImagesController::class, 'index']);

Route::post('images', [AboutUsImagesController::class, 'store']);

Route::get('images/{id}', [AboutUsImagesController::class, 'show']);

Route::delete('images/{id}', [AboutUsImagesController::class, 'destroy']);
//     });
// });
/*
|--------------------------------------------------------------------------
| About Us  CRUD
|--------------------------------------------------------------------------
*/

// Route::middleware(['auth:sanctum'])->group(function () {

//     Route::middleware(['role:superadmin'])->group(function () {

Route::get('aboutus/count', [AboutUsController::class, 'count']);

Route::put('aboutus/{id}', [AboutUsController::class, 'update']);

Route::get('aboutus', [AboutUsController::class, 'index']);

Route::post('aboutus', [AboutUsController::class, 'store']);

Route::get('aboutus/{id}', [AboutUsController::class, 'show']);

Route::delete('aboutus/{id}', [AboutUsController::class, 'destroy']);
//     });
// });
/*
|--------------------------------------------------------------------------
| Category CRUD
|--------------------------------------------------------------------------
*/

// Route::middleware(['auth:sanctum'])->group(function () {

//     Route::middleware(['role:superadmin'])->group(function () {

        Route::get('categories/count', [CategoryController::class, 'count']);

        Route::put('categories/{id}', [CategoryController::class, 'update']);

        Route::get('categories', [CategoryController::class, 'index']);

        Route::post('categories', [CategoryController::class, 'store']);

        Route::get('categories/{id}', [CategoryController::class, 'show']);

        Route::delete('categories/{id}', [CategoryController::class, 'destroy']);
//     });
// });

/*
|--------------------------------------------------------------------------
| Item CRUD
|--------------------------------------------------------------------------
*/

// Route::middleware(['auth:sanctum'])->group(function () {

//     Route::middleware(['role:superadmin'])->group(function () {

        Route::get('items/count', [ItemController::class, 'count']);

        Route::post('items/{id}', [ItemController::class, 'update']);

        Route::get('items', [ItemController::class, 'index']);

        Route::post('items', [ItemController::class, 'store']);

        Route::get('items/{id}', [ItemController::class, 'show']);

        Route::delete('items/{id}', [ItemController::class, 'destroy']);
//     });
// });
/*
|--------------------------------------------------------------------------
| Buildings CRUD
|--------------------------------------------------------------------------

*/

Route::middleware(['auth:sanctum'])->group(function () {

    Route::middleware(['role:superadmin'])->group(function () {

        Route::get('buildings/count', [BuildingApiController::class, 'count']);

        Route::put('buildings/{id}', [BuildingApiController::class, 'update']);

        Route::get('buildings', [BuildingApiController::class, 'index']);

        Route::post('buildings', [BuildingApiController::class, 'store']);

        Route::get('buildings/{id}', [BuildingApiController::class, 'show']);

        Route::delete('buildings/{id}', [BuildingApiController::class, 'destroy']);
    });
});

/*
|--------------------------------------------------------------------------
| Services CRUD
|--------------------------------------------------------------------------

*/
Route::get('service', [ServiceApiController::class, 'index']);

Route::get('service/{id}', [ServiceApiController::class, 'show']);
Route::post('service', [ServiceApiController::class, 'store']);
Route::post('service', [ServiceApiController::class, 'store']);

Route::middleware(['auth:sanctum'])->group(function () {



    Route::middleware(['role:superadmin'])->group(function () {


        Route::put('service/{id}', [ServiceApiController::class, 'update']);

        Route::delete('service/{id}', [ServiceApiController::class, 'destroy']);
    });

});

/*
|--------------------------------------------------------------------------
| Users CRUD
|--------------------------------------------------------------------------

*/

Route::middleware(['auth:sanctum'])->group(function () {

    Route::middleware(['role:admin|superadmin'])->group(function () {
        Route::get('users', [UserApiController::class, 'index']);
        Route::post('/users', [UserApiController::class, 'store']);
        Route::get('users/{id}', [UserApiController::class, 'show']);
        Route::put('users/{id}', [UserApiController::class, 'update']);
        Route::delete('users/{id}', [UserApiController::class, 'destroy']);
    });
    Route::middleware(['role:user'])->group(function () {
        Route::get('users', [UserApiController::class, 'index']);
        Route::put('profile', [UserApiController::class, 'profile']);
        Route::post('/users', [UserApiController::class, 'store']);
        Route::get('users/{id}', [UserApiController::class, 'show']);
        Route::put('users/{id}', [UserApiController::class, 'update']);
        Route::delete('users/{id}', [UserApiController::class, 'destroy']);
    });

});

/*
|--------------------------------------------------------------------------
| securities API Routes
|--------------------------------------------------------------------------

*/
Route::middleware(['auth:sanctum'])->group(function () {

    Route::middleware(['role:admin|superadmin'])->group(function () {

        Route::get('securities', [SecurityController::class, 'index']);
        Route::post('securities', [SecurityController::class, 'store']);
        Route::get('securities/{id}', [SecurityController::class, 'show']);
        Route::put('securities/{id}', [SecurityController::class, 'update']);
        Route::delete('securities/{id}', [SecurityController::class, 'destroy']);
    });

});

/*

|--------------------------------------------------------------------------
| Requests CRUD
|--------------------------------------------------------------------------

*/
Route::get('/db-check', function () {
    dd(DB::connection()->getConfig('database'));
});
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/export-requests', [RequestApiController::class, 'export']);
    Route::get('requests', [RequestApiController::class, 'index']); 

    
    
    Route::middleware(['role:user'])->group(function () {
        Route::post('create-request', [RequestApiController::class, 'store']);
        Route::get('my-requests', [RequestApiController::class, 'userRequests']); 
        Route::get('my-requests/{id}', [RequestApiController::class, 'showUserRequest']); 
        Route::get('my-notifications', [RequestApiController::class, 'showUserNotifications']); 

    });

    // Route::middleware(['role:admin'])->group(function () {
    //     Route::get('admin/requests', [RequestApiController::class, 'index_2']); 
    //     Route::get('admin/requests/{id}', [RequestApiController::class, 'show']); 
    //     Route::put('admin/requests/{id}/update', [RequestApiController::class, 'update']);
    //     Route::delete('requests/{id}', [RequestApiController::class, 'destroy']); 
    //     Route::get('admin/requests/status-counts', [RequestApiController::class, 'indexStatusCountForAdmin']);
    // }); 
    
    Route::middleware(['role:admin|superadmin'])->group(function () {
        Route::get('requests/status-counts', [RequestApiController::class, 'indexStatusCountForSuperadmin']);
        Route::get('requests/{id}', [RequestApiController::class, 'show']); 
        Route::put('requests/{id}/update', [RequestApiController::class, 'update']);
        Route::delete('requests/{id}', [RequestApiController::class, 'destroy']); 
    });

});
