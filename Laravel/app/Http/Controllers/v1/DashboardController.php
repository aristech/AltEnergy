<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Damage;
use App\Service;
use App\Eventt;
use App\Client;
use App\Manager;
use DB;
use App\Http\CustomClasses\v1\IndicatorManagement;
use App\Note;
use Carbon\Carbon;
use App\Http\Resources\DamageResource;
use App\Http\Resources\ServiceResource;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        // $openDamages = Damage::where('status', 'Μη Ολοκληρωμένη')->get()->count();
        // $fixedDamages = Damage::where('status', 'Ολοκληρώθηκε')->get()->count();
        // $cancelledDamages = Damage::where('status', 'Ακυρώθηκε')->get()->count();
        // $totalDamages = $openDamages + $fixedDamages + $cancelledDamages;

        // $openServices = Service::where('status', 'Μη Ολοκληρωμένο')->get()->count();
        // $completedServices = Service::where('status', 'Ολοκληρωμένο')->get()->count();
        // $cancelledServices = Service::where('status', 'Ακυρώθηκε')->get()->count();
        // $totalServices = $openServices + $completedServices + $cancelledServices;

        // $openEvents = Eventt::where('status','Μη Ολοκληρωμένο')->get()->count();
        // $completedEvents = Eventt::where('status','Ολοκληρωμένο')->get()->count();
        // $cancelledEvents = Eventt::where('status','Ακυρώθηκε')->get()->count();
        // $totalEvents = $openEvents + $completedEvents + $cancelledEvents;

        $clients = Client::all()->count();

        $dashboard = new \stdClass();
        // $dashboard->total_damages = $totalDamages;
        // $dashboard->open_damages = $openDamages;
        // $dashboard->fixed_damages = $fixedDamages;
        // $dashboard->cancelled_damages = $cancelledDamages;

        // $dashboard->total_services = $totalServices;
        // $dashboard->open_services = $openServices;
        // $dashboard->fixed_services = $completedServices;
        // $dashboard->cancelled_services = $cancelledServices;

        // $dashboard->notes = Note::all()->count();

        // $dashboard->total_events = $totalEvents;
        // $dashboard->open_events = $openEvents;
        // $dashboard->fixed_events = $completedEvents;
        // $dashboard->cancelled_events = $cancelledEvents;

        $dashboard->registered_clients = $clients;

        $managers = Manager::all()->count();
        $dashboard->registered_managers = $managers;

        $technicians = DB::table('users')
            ->join('role_user', function ($join) {
                $join->on('users.id', '=', 'role_user.user_id')
                    ->where('role_user.role_id', '=', 3);
            })->count();

        $dashboard->registered_technicians = $technicians;

        $administrators = DB::table('users')
            ->join('role_user', function ($join) {
                $join->on('users.id', '=', 'role_user.user_id')
                    ->where('role_user.role_id', '=', 4);
            })->count();

        $dashboard->registered_administrators = $administrators;
        /*start big table*/
        $open_cases = array();
        $pending_damages = Damage::where('status', 'Μη Ολοκληρωμένη')->get();
        foreach ($pending_damages as $pending_damage) {
            $case = DamageResource::make($pending_damage);
            // $case = new \stdClass();
            // $case
            // $case->type = "ΒΛΑΒΗ";
            // $case->name = $pending_damage['type']['name'];
            // $case->client = $pending_damage['client']['firstname'] . " " . $pending_damage['client']['lastname'];
            // $case->appointment_pending = $pending_damage['appointment_pending'] == true ? "ΝΑΙ" : "ΟΧΙ";
            // $case->appointment_start = Carbon::createFromTimeStamp(strtotime($pending_damage['appointment_start']))->format('Y-m-d h:i:sa');
            array_push($open_cases, $case);
        }

        $pending_services = Service::where('status', 'Μη Ολοκληρωμένο')->get();
        foreach ($pending_services as $pending_service) {
            //$case = new \stdClass();
            $case = ServiceResource::make($pending_service);
            // $case->type = "ΣΕΡΒΙΣ";
            // $case->name = $pending_service['type']['name'];
            // $case->client = $pending_service['client']['firstname'] . " " . $pending_service['client']['lastname'];
            // $case->appointment_pending = $pending_service['appointment_pending'] == true ? "ΝΑΙ" : "ΟΧΙ";
            // $case->appointment_start = Carbon::createFromTimeStamp(strtotime($pending_service['appointment_start']))->format('Y-m-d h:i:sa');
            array_push($open_cases, $case);
        }

        $dashboard->open_cases = $open_cases;
        /*end big table*/

        $supplements = array();
        $supplementController =  app('App\Http\Controllers\v1\SupplementController')->index($request)->getContent();
        $supplementController = json_decode($supplementController);
        foreach ($supplementController->data as $supplement) {
            $supplementObj = new \stdClass();
            $supplementObj->supplement = $supplement->supplement;
            $supplementObj->date = $supplement->date;

            array_push($supplements, $supplement);
        }

        $dashboard->supplements = $supplements;


        $indications = new IndicatorManagement();
        $indications->getDamageIndicators();
        $indications->getServiceIndicators();
        //$indications->getEventIndicators();

        if (count($indications->indications) == 0) {
            //$dashboard->uncheckedEvents = "Δεν υπάρχει κάποια καθυστερημένη υποχρέωση";
        } else {
            $dashboard->uncheckedEvents = $indications->indications;
        }

        //$dashboard->uncheckedEvents= $indications->indications;

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
