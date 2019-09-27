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

Route::group(['prefix' => 'v1', 'namespace' => 'v1', 'middleware' => 'cors'], function ()
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
        Route::get('/clients','ClientController@index');
        Route::post('/clients','ClientController@store');
        Route::put('/clients','ClientController@update');

        Route::get('/managers','ManagerController@index');
        Route::post('/managers','ManagerController@store');
        Route::put('/managers','ManagerController@update');

        Route::get('/manufacturers','ManufacturerController@index');
        Route::post('/manufacturers','ManufacturerController@store');
        Route::delete('/manufacturers','ManufacturerController@destroy');

        Route::get('/marks','MarkController@index');
        Route::post('/marks','MarkController@store');
        Route::delete('/marks','MarkController@destroy');

        Route::get('/devices','DeviceController@index');
        Route::post('/devices','DeviceController@store');
        Route::delete('/devices','DeviceController@destroy');

        Route::post('/vcfexport','VcfController@export');

        Route::get('/tech','TechController@index');

        Route::get('/damagetypes','DamageTypeController@index');
        Route::post('/damagetypes','DamageTypeController@store');
        Route::delete('/damagetypes','DamageTypeController@destroy');

        Route::get('/damages','DamageController@index');
        Route::post('/damages','DamageController@store');
        Route::put('/damages','DamageController@update');
        Route::delete('/damages','DamageController@destroy');

    });
});
