<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Damage;
use App\Service;
use App\Eventt;
use App\Client;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $openDamages = Damage::where('status','Μη Ολοκληρωμένη')->get()->count();
        $fixedDamages = Damage::where('status','Ολοκληρωμένη')->get()->count();
        $cancelledDamages = Damage::where('status','Ακυρώθηκε')->get()->count();
        $totalDamages = $openDamages + $fixedDamages + $cancelledDamages;

        $openServices = Service::where('status','Μη Ολοκληρωμένο')->get()->count();
        $completedServices = Service::where('status','Ολοκληρωμένο')->get()->count();
        $cancelledServices = Service::where('status','Ακυρώθηκε')->get()->count();
        $totalServices = $openServices + $completedServices + $cancelledServices;

        $openEvents = Service::where('status','Μη Ολοκληρωμένο')->get()->count();
        $completedEvents = Service::where('status','Ολοκληρωμένο')->get()->count();
        $cancelledEvents = Service::where('status','Ακυρώθηκε')->get()->count();
        $totalEvents = $openEvents + $completedEvents + $cancelledEvents;

        $clients = Client::all()->count();

        $dashboard = new \stdClass();
        $dashboard->total_damages = $totalDamages;
        $dashboard->open_damages = $openDamages;
        $dashboard->fixed_damages = $fixedDamages;
        $dashboard->cancelled_damages = $cancelledDamages;

        $dashboard->total_services = $totalServices;
        $dashboard->open_services = $openServices;
        $dashboard->fixed_services = $completedServices;
        $dashboard->cancelled_services = $cancelledServices;

        $dashboard->total_events = $totalEvents;
        $dashboard->open_events = $openEvents;
        $dashboard->fixed_events = $completedEvents;
        $dashboard->cancelled_events = $cancelledEvents;

        $dashboard->registered_clients = $clients;

        // $supplements = array();
        // $supplementController =  app('App\Http\Controllers\v1\SupplementController')->index($request)->getContent();
        // $supplementController = json_decode($supplementController);
        // foreach($supplementController->data as $supplement)
        // {
        //     $supplementObj = new \stdClass();
        //     $supplementObj->supplement = $supplement->supplement;
        //     $supplementObj->date = $supplement->date;

        //     array_push($supplements,$supplement);
        // }

        // $dashboard->supplements = $supplements;
        return response()->json(["data" => $dashboard]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
