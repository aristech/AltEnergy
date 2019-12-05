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

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role_id = $request->user()->role()->first()->id;
        if ($role_id >= 3) {
            return ServiceManagement::getServices();
        }
        //elseif ($role_id == 2) {
        //     $serv = Service::whereHas('client', function ($query) use ($request) {
        //         $query->where('manager_id', $request->user()->manager_id);
        //     })->get();
        //     return ManagerServiceResource::collection($serv);
        // }
    }

    public function history(Request $request)
    {
        $role_id = $request->user()->role()->first()->id;
        if ($role_id >= 3) {
            return ServiceManagement::getServicesHistory();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    { }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $role_id = $request->user()->role()->first()->id;
        if ($role_id >= 3) {
            $service = new ServiceManagement($request);
            return $service->storeService();
        } else {
            return response()->json(["message" => "Ο χρήστης με ρόλο " . $request->user()->role()->first()->name . " δεν μπορεί να εισάγει services στο σύστημα!"], 401);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($service, Request $request)
    {
        $role_id = $request->user()->role()->first()->id;
        if ($role_id < 2) {
            return response()->json(["message" => "Ο χρήστης με ρόλο " . $request->user()->role()->first()->name . " δεν μπορεί να έχει πρόσβαση στα στοιχεία αυτά"], 401);
        }

        if ($role_id >= 3) {
            $service = Service::find($service);
            if (!$service) return response()->json(["message" => "Δεν βρέθηκε το service στο σύστημα"], 404);

            return ServiceResource::make($service);
        }

        if ($role_id == 2) {
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
        $role_id = $request->user()->role()->first()->id;
        if ($role_id >= 2) {
            $service = new ServiceCalendarUpdate($request, $service);
            return $service->updateService();
        } else {
            return response()->json(["message" => "Ο χρήστης με ρόλο " . $request->user()->role()->first()->title . " δεν μπορεί να έχει πρόσβαση στα στοιχεία αυτά"], 401);
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
        $role_id = $request->user()->role()->first()->id;
        if ($role_id >= 2) {
            $service = new ServiceManagement($request);
            return $service->updateService();
        } else {
            return response()->json(["message" => "Ο χρήστης με ρόλο " . $request->user()->role()->first()->title . " δεν μπορεί να έχει πρόσβαση στα στοιχεία αυτά"], 401);
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
        $role_id = $request->user()->role()->first()->id;
        if ($role_id < 3) {
            return response()->json(["message" => "Χρήστες με δικαίωμα " . $request->user()->role()->first()->title . " δεν μπορεί να πραγματοποιήσει την ενέργεια αυτή!"], 401);
        }

        $service = Service::where('id', $request->id)->first();
        if (!$service) {
            return response()->json(["message" => "Το service με κωδικό " . $request->id . " δεν είναι καταχωρημένo!"], 404);
        }
        $service->delete();


        $calendar = Calendar::where('service_id', $request->id)->first();
        if (!$calendar) $calendar->delete();

        return response()->json(["message" => "Το service με κωδικό " . $request->id . " διαγραφηκε επιτυχώς!"], 200);
    }

    public function remove(Request $request, $serviceId)
    {
        $role_id = $request->user()->role()->first()->id;
        if ($role_id < 3) {
            return response()->json(["message" => "Χρήστες με δικαίωμα " . $request->user()->role()->first()->title . " δεν μπορεί να πραγματοποιήσει την ενέργεια αυτή!"], 401);
        }

        $service = Service::where('id', $serviceId)->first();
        if (!$service) {
            return response()->json(["message" => "Το σέρβις με κωδικό " . $serviceId . " δεν είναι καταχωρημένη!"], 404);
        }

        $service->delete();
        //delete stored entry in calendar
        $calendar = Calendar::where('service_id', $serviceId)->first();
        if ($calendar) {
            $calendar->delete();
            //end delete calendar entry
        }

        return response()->json(["message" => "Το σέρβις με κωδικό " . $serviceId . " διαγραφηκε επιτυχώς!"], 200);
    }
}
