<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ConsultantController;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

//******************** Cosultant Routes ***********************************
Route::group(['middleware' => 'api' , 'prefix' => 'consultant'], function ($router) {
    Route::post('/login'                        , [ConsultantController::class, 'login']);
    Route::post('/register'                     , [ConsultantController::class, 'register']);
    Route::get('/user-profile'                  , [ConsultantController::class, 'userProfile']); 
    Route::get('/subcategories'                 , [ConsultantController::class, 'subcategories']); 
    Route::get('/waiting-reservation'           , [ConsultantController::class, 'waitingReservation']);
    Route::get('/old-reservation'               , [ConsultantController::class, 'oldReservation']);
    Route::get('/coming-reservation'            , [ConsultantController::class, 'comingReservation']);
    Route::get('/rejected-reservation'          , [ConsultantController::class, 'rejectedReservation']);
    Route::get('/reject/{id}'                   , [ConsultantController::class, 'reject']);
    Route::get('/accept/{id}'                   , [ConsultantController::class, 'accept']);
});

//******************** User Routes ***********************************
Route::group(['middleware' => 'api', 'prefix' => 'user'], function ($router) {
    Route::post('/login'                        , [userController::class, 'login']);
    Route::post('/register'                     , [userController::class, 'register']);
    Route::get('/user-profile'                  , [userController::class, 'userProfile']);
    Route::post('/update-user-profile'          , [userController::class, 'UpdateUserProfile']);
    Route::post('/addReview/{id}'               , [userController::class, 'addReview']); 
    Route::get('/home'                          , [userController::class, 'home']); 
    Route::post('/slider'                       , [userController::class, 'slider']); 
    Route::get('/subcategories/{id}'            , [userController::class, 'subcategories']); 
    Route::get('/getConsultants/{id}'           , [userController::class, 'getConsultants']);
    Route::get('/blogs/{id}'                    , [userController::class, 'blogs'])->name('blogs'); 
    Route::get('/blog-details/{id}'             , [userController::class, 'blogDetails']);
    Route::get('/consultantInfo/{id}'           , [userController::class, 'consultantInfo']);
    Route::get('/consultantReviews/{id}'        , [userController::class, 'consultantReviews']);
    Route::get('/consultantProfile/{id}'        , [userController::class, 'ConsultantProfile']);
    Route::get('/available-appointment'         , [userController::class, 'availableAppointment']);
    Route::post('/make-reservation'             , [userController::class, 'makeReservation']);
    Route::get('/waiting-reservation'           , [userController::class, 'waitingReservation']);
    Route::get('/old-reservation'               , [userController::class, 'oldReservation']);
    Route::get('/coming-reservation'            , [userController::class, 'comingReservation']);
});

//******************** Common Routes ***********************************
Route::group(['middleware' => 'api'], function ($router) {
    Route::post('verifyCode'                    , [ApiController::class, 'verifyCode']);
    Route::post('resetPassword'                 , [ApiController::class, 'resetPassword']);
    Route::post('sendForgetCode'                , [ApiController::class, 'sendForgetCode']);
    Route::post('/logout'                       , [ApiController::class, 'logout']);
    Route::get('/delAccount'                    , [ApiController::class, 'delAccount']); 
    Route::get('/terms-conditions'              , [ApiController::class, 'termsConditions']);  
    Route::get('/aboutUs'                       , [ApiController::class, 'aboutUs']);
    Route::post('/contactUs'                    , [ApiController::class, 'contactUs']);
});