<?php

use Illuminate\Http\Request;

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

Route::group(['prefix' => 'v1', 'namespace' => 'v1'], function ()
{
    Route::post('/login', 'AuthController@login');
    Route::post('/signup', 'AuthController@signup');

    Route::group(['middleware' => 'auth:api'], function()
    {
        Route::get('/logout', 'AuthController@logout');
        Route::get('/user', 'AuthController@user');
        Route::post('/createUser','AuthController@createUser');
        Route::put('/edituser','AuthController@editUser');
        Route::get('/allusers','AuthController@allUsers');
        //Role routes
        Route::get('/roles','RoleController@index');
        //Location routes
        Route::get('/locations','LocationController@index');

    });
});
