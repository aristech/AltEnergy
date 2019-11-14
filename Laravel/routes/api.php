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
            Route::get('/clients/{client}','ClientController@show');
            Route::post('/clients','ClientController@store');
            Route::put('/clients','ClientController@update');
            Route::delete('/clients','ClientController@destroy');

            Route::get('/managers','ManagerController@index');
            Route::get('/managers/{manager}','ManagerController@show');
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
            Route::get('/damages/{damage}','DamageController@show');
            Route::get('/damagehistory','DamageController@history');
            Route::post('/damages','DamageController@store');
            Route::put('/damages','DamageController@update');
            Route::delete('/damages','DamageController@destroy');
            Route::put('/damages/{damage}','DamageController@edit');
            Route::delete('/damages/{damageId}','DamageController@remove');

            Route::post('/searchclients','SearchController@searchClients');
            Route::post('/searchtechs','SearchController@searchTechs');
            Route::post('/searchmanagers','SearchController@searchManagers');
            Route::post('/searchmanu','SearchController@searchManufacturers');
            Route::post('/searchmarks','SearchController@searchMarks');
            Route::post('/searchdevices','SearchController@searchDevices');
            Route::post('/searchdamagetypes','SearchController@searchDamageTypes');

            // Route::get('/events','EventController@index');
            // Route::get('/eventhistory','EventController@history');
            // Route::get('/events/{event}','EventController@show');
            // Route::post('/events','EventController@store');
            // Route::put('/events','EventController@update');
            // Route::delete('/events','EventController@destroy');

            Route::get('/notes/{note}', 'NotesController@show');
            Route::post('/notes','NotesController@store');
            Route::put('/notes/{note}','NotesController@update');
            Route::delete('/notes/{note}','NotesController@destroy');


            Route::get('/files/{id}','FileController@index');
            Route::post('/files/{id}','FileController@store');
            Route::get('/files/{id}/{file}','FileController@show');
            Route::delete('/files/{id}/{file}','FileController@destroy');
            Route::post('/files/{id}/upload','FileController@upload');

            Route::get('/servicetypes','ServiceTypeController@index');
            Route::post('/servicetypes','ServiceTypeController@store');
            Route::delete('/servicetypes','ServiceTypeController@destroy');

            Route::get('/services','ServiceController@index');
            Route::get('/services/{service}','ServiceController@show');
            Route::get('/servicehistory','ServiceController@history');
            Route::post('/services','ServiceController@store');
            Route::put('/services','ServiceController@update');
            Route::delete('/services','ServiceController@destroy');

            Route::get('/dashboard','DashboardController@index');

            Route::get('/calendar','CalendarController@index');

            Route::get('/supplements','SupplementController@index');

            Route::get('/indications','IndicatorsController@index');
        });
    });
});
