<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Damage;
use Carbon\Carbon;
use App\Service;

class SupplementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role_id = $request->user()->role()->first()->id;
        if($role_id < 3)
        {
            return response()->json(["message" => "Δεν μπορείτε να έχετε πρόσβαση στα στοιχεία αυτά"],401);
        }

        $supplements = array();
        $damages = Damage::where('status','Μη Ολοκληρωμένη')->where('supplement','!=',null)->get();

        $now = Carbon::now();
        $startweek = strtotime($now->startOfWeek());
        $endweek = strtotime($now->endOfWeek());
        foreach($damages as $damage)
        {
            $appointment = $damage->appointment_start;
            // $appointment_temp = str_replace("T"," ",$appointment);
            // $split_appointment = explode(".",$appointment_temp);
            // $appointmentDisplay = $split_appointment[0];
            $appointmentDisplay = date("F j, Y, g:i a",strtotime($appointment));
            // $apointment = explode(" ",$damage->appointment_start);
            // $appointment = $apointment[0]." ".$apointment[1]." ".$apointment[2]." ".$apointment[3]." ".$apointment[4]." ".$apointment[5];
            if($damage->appointment_start != null && strtotime($appointment) >= $startweek && strtotime($appointment) <= $endweek)
            {
                $supplement = new \stdClass();
                $supplement->supplement = $damage->supplement;
                $supplement->date = $appointmentDisplay;//if all goes wrong display $damage->appointment_start

                array_push($supplements,$supplement);
            }
        }

        $services = Service::where('status','Μη Ολοκληρωμένο')->where('supplements','!=',null)->get();
        foreach($services as $service)
        {
            $appointment = $damage->appointment_start;
            $appointmentDisplay = date("F j, Y, g:i a",strtotime($appointment));
            // $apointment = explode(" ",$service->appointment_start);
            // $appointment = $apointment[0]." ".$apointment[1]." ".$apointment[2]." ".$apointment[3]." ".$apointment[4]." ".$apointment[5];
            if($service->appointment_start != null && strtotime($appointment) >= $startweek && strtotime($appointment) <= $endweek)
            {
                $supplement = new \stdClass();
                $supplement->supplement = $service->supplement;
                $supplement->date = $appointmentDisplay; //if all goes south replace with $service->appointment_start

                array_push($supplements,$supplement);
            }
        }
        return response()->json(["data"=>$supplements],200);
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
