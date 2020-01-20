<?php

namespace App\Http\Controllers\v1;

use App\Service;
use App\Http\Resources\ServiceResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\CustomClasses\v1\ServiceManagement;
use App\Http\CustomClasses\v1\ServiceCalendarUpdate;
use App\Calendar;
use App\Http\Resources\ManagerDamageResource;
use App\Http\Resources\ManagerServiceResource;
use Validator;
use App\Http\CustomClasses\v1\AuthorityClass;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $role_id = $request->user()->role()->first()->id;
        // if ($role_id >= 3) {
        return ServiceManagement::getServices();
        //}
        //elseif ($role_id == 2) {
        //     $serv = Service::whereHas('client', function ($query) use ($request) {
        //         $query->where('manager_id', $request->user()->manager_id);
        //     })->get();
        //     return ManagerServiceResource::collection($serv);
        // }
    }

    public function history(Request $request)
    {
        // $role_id = $request->user()->role()->first()->id;
        // if ($role_id >= 3) {
        return ServiceManagement::getServicesHistory();
        //}
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $role_id = $request->user()->role()->first()->id;
        // if ($role_id >= 3) {
        $service = new ServiceManagement($request);
        return $service->storeService();
        // } else {
        //     return response()->json(["message" => "Ο χρήστης με ρόλο " . $request->user()->role()->first()->name . " δεν μπορεί να εισάγει services στο σύστημα!"], 401);
        // }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($service, Request $request)
    {
        $highest_role = AuthorityClass::getAuthorityLevel($request);
        //$role_id = $request->user()->role()->first()->id;
        if ($highest_role < 2) {
            return response()->json(["message" => "Δεν μπορείτε να έχετε πρόσβαση στα στοιχεία αυτά"], 401);
        }

        if ($highest_role >= 3) {
            $service = Service::find($service);
            if (!$service) return response()->json(["message" => "Δεν βρέθηκε το service στο σύστημα"], 404);

            return ServiceResource::make($service);
        }

        if ($highest_role == 2) {
            $serv = Service::where('id', $service)->first();
            $manager_id = $serv['client']['manager']['id'];
            if ($manager_id == $request->user()->manager_id) {
                return ServiceResource::make($serv);
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $service)
    {
        $highest_role = AuthorityClass::getAuthorityLevel($request);
        //$role_id = $request->user()->role()->first()->id;
        if ($highest_role >= 2) {
            $service = new ServiceCalendarUpdate($request, $service);
            return $service->updateService();
        } else {
            return response()->json(["message" => "Δεν μπορείτε να έχετε πρόσβαση στα στοιχεία αυτά"], 401);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //$role_id = $request->user()->role()->first()->id;
        $highest_role = AuthorityClass::getAuthorityLevel($request);
        if ($highest_role >= 2) {
            $service = new ServiceManagement($request);
            return $service->updateService();
        } else {
            return response()->json(["message" => "Δεν μπορείτε να κάνετε αυτή την ενέργεια"], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        // $role_id = $request->user()->role()->first()->id;
        // if ($role_id < 3) {
        //     return response()->json(["message" => "Δεν μπορείτε να πραγματοποιήσετε την ενέργεια αυτή!"], 401);
        // }

        $service = Service::where('id', $request->id)->first();
        if (!$service) {
            return response()->json(["message" => "Το service αυτό δεν είναι καταχωρημένo στο σύστημα!"], 404);
        }
        $service->delete();


        $calendar = Calendar::where('service_id', $request->id)->first();
        if (!$calendar) $calendar->delete();

        return response()->json(["message" => "Το service διαγραφηκε επιτυχώς!"], 200);
    }

    public function remove(Request $request, $serviceId)
    {
        // $role_id = $request->user()->role()->first()->id;
        // if ($role_id < 3) {
        //     return response()->json(["message" => "Δεν μπορείτε να πραγματοποιήσει την ενέργεια αυτή!"], 401);
        // }

        $service = Service::where('id', $serviceId)->first();
        if (!$service) {
            return response()->json(["message" => "Το σέρβις δεν βρέθηκε στο σύστημα!"], 404);
        }

        $service->delete();
        //delete stored entry in calendar
        $calendar = Calendar::where('service_id', $serviceId)->first();
        if ($calendar) {
            $calendar->delete();
            //end delete calendar entry
        }

        return response()->json(["message" => "Το σέρβις διαγραφηκε επιτυχώς!"], 200);
    }
}
