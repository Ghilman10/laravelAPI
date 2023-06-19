<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ApiAuthController;

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

Route::group(['middleware' => 'auth:api'], function(){
  
    Route::post('create-user', [ApiAuthController::class, 'createUser']);    
    Route::post('delete-user/{id}', [ApiAuthController::class, 'deleteUser']);
    Route::post('update-user/{id}', [ApiAuthController::class, 'updateUser']);    
    

});
 
Route::post('register', [ApiAuthController::class, 'register']);
Route::post('login', [ApiAuthController::class, 'login']);
Route::get('show-users', [ApiAuthController::class, 'showUsers']);  
