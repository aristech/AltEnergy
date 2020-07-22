<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

//if all goes wrong uncomment logout and remove active logout
Route::group(['prefix' => 'v1', 'namespace' => 'v1', 'middleware' => 'cors'], function () {

    Route::post('/login', 'AuthController@login');
    Route::post('/signup', 'AuthController@signup');
    // Route::post('/test', 'TestController@test');

    Route::get('/projects', 'ProjectController@index');
    Route::get('/projects/{projectId}', 'ProjectController@show');
    Route::post('/projects', 'ProjectController@store');
    Route::put('/projects', 'ProjectController@update');
    Route::delete('/projects', 'ProjectController@destroy');
    Route::delete('/projects/{projectId}', 'ProjectController@remove');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('/appointment', 'FreeAppointmentController@index');

        Route::get('/user', 'AuthController@user');
        Route::get('/logout', 'AuthController@logout');


        Route::group(['middleware' => 'active'], function () {


            Route::group(['middleware' => 'admin-only'], function () {
                Route::post('/users', 'AuthController@createUser');
                Route::put('/users', 'AuthController@editUser');
                Route::get('/users', 'AuthController@allUsers');
                Route::delete('/users', 'AuthController@deleteUser');

                Route::get('/clients', 'ClientController@index');
                Route::get('/clients/{client}', 'ClientController@show');
                Route::post('/clients', 'ClientController@store');
                Route::put('/clients', 'ClientController@update');
                Route::delete('/clients', 'ClientController@destroy');

                Route::get('/managers', 'ManagerController@index');
                Route::get('/managers/{manager}', 'ManagerController@show');
                Route::post('/managers', 'ManagerController@store');
                Route::put('/managers', 'ManagerController@update');
                Route::delete('/managers', 'ManagerController@destroy');

                Route::post('/damages', 'DamageController@store');
                Route::delete('/damages', 'DamageController@destroy');
                Route::delete('/damages/{damageId}', 'DamageController@remove');

                Route::post('/services', 'ServiceController@store');
                Route::delete('/services/{serviceId}', 'ServiceController@remove');
                Route::delete('/services', 'ServiceController@destroy');

                Route::get('/files/{id}', 'FileController@index');
                Route::post('/files/{id}', 'FileController@store');
                Route::get('/files/{id}/{file}', 'FileController@show');
                Route::delete('/files/{id}/{file}', 'FileController@destroy');
                Route::post('/files/{id}/upload', 'FileController@upload');

                Route::get('/reminder', 'ReminderController@index');
                Route::post('/importclients', 'ImportClientsController@import');

                Route::get('/settings', 'SettingsController@index');

                Route::get('/settings/scanner', 'ScannerSettingsController@index');
                Route::get('/settings/scanner/{id}', 'ScannerSettingsController@show');
                Route::post('/settings/scanner', 'ScannerSettingsController@store');
                Route::put('/settings/scanner/{id}', 'ScannerSettingsController@update');
                Route::delete('/settings/scanner/{id}', 'ScannerSettingsController@destroy');

                Route::get('/settings/bullets', 'BulletController@index');
                Route::get('/settings/bullets/{bult}', 'BulletController@show');
                Route::post('/settings/bullets', 'BulletController@store');
                Route::put('/settings/bullets/{bullet}', 'BulletController@update');
                Route::delete('/settings/bullets/{bullet}', 'BulletController@destroy');

                //
                Route::get('/offerTexts', 'OfferTextController@index');
                Route::get('/offerTexts/{offerText}', 'OfferTextController@show');
                Route::post('/offerTexts', 'OfferTextController@store');
                Route::put('/offerTexts/{offerText}', 'OfferTextController@update');
                Route::delete('/offerTexts', 'OfferTextController@destroy');
                //

                Route::get('/offers', 'NewOfferController@index');
                Route::post('/offers', 'NewOfferController@store');
                // Route::get('/offers/{offer}/{status}', 'OfferController@edit');
                Route::get('/offers-file/{offer}', 'NewOfferController@file');

                Route::get('/convert-offer/{offerId}', 'NewOfferController@convertToProject');
            });



            //Route::get('/logout', 'AuthController@logout');
            Route::group(['middleware' => 'admin-and-techs'], function () {
                Route::get('/manufacturers', 'ManufacturerController@index');
                Route::post('/manufacturers', 'ManufacturerController@store');
                Route::delete('/manufacturers', 'ManufacturerController@destroy');

                Route::get('/manufacturers/{manufacturer}/marks', 'MarkController@index');
                Route::post('/manufacturers/{manufacturer}/marks', 'MarkController@store');
                Route::delete('/manufacturers/{manufacturer}/marks', 'MarkController@destroy');

                Route::get('/manufacturers/{manufacturer}/marks/{mark}/devices', 'DeviceController@index');
                Route::post('/manufacturers/{manufacturer}/marks/{mark}/devices', 'DeviceController@store');
                Route::delete('/manufacturers/{manufacturer}/marks/{mark}/devices', 'DeviceController@destroy');

                Route::post('/vcfexport', 'VcfController@export');

                Route::get('/tech', 'TechController@index');

                Route::get('/damagetypes', 'DamageTypeController@index');
                Route::post('/damagetypes', 'DamageTypeController@store');
                Route::put('/damagetypes/{damagetype}', 'DamageTypeController@update');
                Route::delete('/damagetypes', 'DamageTypeController@destroy');

                Route::get('/damages', 'DamageController@index');
                Route::get('/damagehistory', 'DamageController@history');


                Route::get('/services', 'ServiceController@index');
                Route::get('/servicehistory', 'ServiceController@history');


                Route::get('/notes/{note}', 'NotesController@show');
                Route::post('/notes', 'NotesController@store');
                Route::put('/notes/{note}', 'NotesController@update');
                Route::delete('/notes/{note}', 'NotesController@destroy');

                Route::get('/servicetypes', 'ServiceTypeController@index');
                Route::post('/servicetypes', 'ServiceTypeController@store');
                Route::delete('/servicetypes', 'ServiceTypeController@destroy');


                Route::get('/projects', 'ProjectController@index');
                Route::get('/projects/{projectId}', 'ProjectController@show');
                Route::post('/projects', 'ProjectController@store');
                Route::put('/projects', 'ProjectController@update');
                Route::put('/projects/{projectId}', 'ProjectController@edit');
                Route::delete('/projects', 'ProjectController@destroy');
                Route::delete('/projects/{projectId}', 'ProjectController@remove');


                Route::get('/supplements', 'SupplementController@index');

                Route::get('/indications', 'IndicatorsController@index');
            });




            //Role routes
            Route::get('/roles', 'RoleController@index');


            //cases including and manager
            Route::get('/damages/{damage}', 'DamageController@show');
            Route::put('/damages', 'DamageController@update');
            Route::put('/damages/{damage}', 'DamageController@edit');
            //end cases including and manager

            Route::post('/searchclients', 'SearchController@searchClients');
            Route::post('/searchtechs', 'SearchController@searchTechs');
            Route::post('/searchmanagers', 'SearchController@searchManagers');
            Route::post('/searchmanu', 'SearchController@searchManufacturers');
            Route::get('/searchmarks', 'SearchController@searchMarks');
            Route::post('/searchdevices', 'SearchController@searchDevices');
            Route::post('/searchdamagetypes', 'SearchController@searchDamageTypes');
            Route::post('/searchservicetypes', 'SearchController@searchServiceTypes');

            Route::get('/searchclientmarks/{client_id}', 'SearchController@searchClientMarks');

            // Route::get('/events','EventController@index');
            // Route::get('/eventhistory','EventController@history');
            // Route::get('/events/{event}','EventController@show');
            // Route::post('/events','EventController@store');
            // Route::put('/events','EventController@update');
            // Route::delete('/events','EventController@destroy');

            //cases including the manager pt2
            Route::get('/services/{service}', 'ServiceController@show');
            Route::put('/services', 'ServiceController@update');
            Route::put('/services/{service}', 'ServiceController@edit');
            //end cases including the manager pt2
            Route::get('/dashboard', 'DashboardController@index');

            Route::get('/calendar', 'CalendarController@index');


            Route::get('/appointmentCalendar', 'FreeAppointmentController@indexTwo');
            Route::get('/appointments', 'FreeAppointmentController@index');
            Route::get('/appointments/{appointment}', 'FreeAppointmentController@show');
            Route::post('/appointments', 'FreeAppointmentController@store');
            Route::put('/appointments', 'FreeAppointmentController@edit');
            Route::put('/appointments/{appointment}', 'FreeAppointmentController@update');
            Route::delete('/appointments/{appointment}', 'FreeAppointmentController@destroy');


            // Route::group(['middleware' => 'admin-only'], function () {
            //     Route::get('/reminder', 'ReminderController@index');
            //     Route::post('/importclients', 'ImportClientsController@import');

            //     Route::get('/settings', 'SettingsController@index');

            //     Route::get('/settings/scanner', 'ScannerSettingsController@index');
            //     Route::get('/settings/scanner/{id}', 'ScannerSettingsController@show');
            //     Route::post('/settings/scanner', 'ScannerSettingsController@store');
            //     Route::put('/settings/scanner/{id}', 'ScannerSettingsController@update');
            //     Route::delete('/settings/scanner/{id}', 'ScannerSettingsController@destroy');
            // });
        });
    });
});
