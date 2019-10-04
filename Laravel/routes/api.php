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

    Route::post('/upload','FileController@store');

    Route::group(['middleware' => 'auth:api'], function()
    {
        Route::group(['middleware' => 'active'],function()
        {
            Route::get('/logout', 'AuthController@logout');
            Route::get('/user', 'AuthController@user');


            Route::post('/users','AuthController@createUser');
            Route::put('/users','AuthController@editUser');
            Route::get('/users','AuthController@allUsers');
            Route::delete('/users','AuthController@deleteUser');


            //Role routes
            Route::get('/roles','RoleController@index');
            //Location routes
            Route::get('/clients','ClientController@index');
            Route::post('/clients','ClientController@store');
            Route::put('/clients','ClientController@update');
            Route::delete('/clients','ClientController@destroy');

            Route::get('/managers','ManagerController@index');
            Route::post('/managers','ManagerController@store');
            Route::put('/managers','ManagerController@update');
            Route::delete('/managers','ManagerController@destroy');

            Route::get('/manufacturers','ManufacturerController@index');
            Route::post('/manufacturers','ManufacturerController@store');
            Route::delete('/manufacturers','ManufacturerController@destroy');

            Route::get('/manufacturers/{manufacturer}/marks','MarkController@index');
            Route::post('/manufacturers/{manufacturer}/marks','MarkController@store');
            Route::delete('/manufacturers/{manufacturer}/marks','MarkController@destroy');

            Route::get('/manufacturers/{manufacturer}/marks/{mark}/devices','DeviceController@index');
            Route::post('/manufacturers/{manufacturer}/marks/{mark}/devices','DeviceController@store');
            Route::delete('/manufacturers/{manufacturer}/marks/{mark}/devices','DeviceController@destroy');

            Route::post('/vcfexport','VcfController@export');

            Route::get('/tech','TechController@index');

            Route::get('/damagetypes','DamageTypeController@index');
            Route::post('/damagetypes','DamageTypeController@store');
            Route::delete('/damagetypes','DamageTypeController@destroy');

            Route::get('/damages','DamageController@index');
            Route::post('/damages','DamageController@store');
            Route::put('/damages','DamageController@update');
            Route::delete('/damages','DamageController@destroy');

            Route::post('/searchclients','SearchController@searchClients');
            Route::post('/searchtechs','SearchController@searchTechs');
            Route::post('/searchmanagers','SearchController@searchManagers');
            Route::post('/searchmanu','SearchController@searchManufacturers');
            Route::post('/searchmarks','SearchController@searchMarks');
            Route::post('/searchdevices','SearchController@searchDevices');

            Route::post('/files/{id}','FileController@store');
            Route::get('/files/{id}','FileController@index');
            Route::get('/files/{id}/{file}','FileController@index');

        });
    });
});
