<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Damage;
use Carbon\Carbon;
use App\Http\Resources\SupplementResource;

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
        $startweek = strtotime($now->startOfWeek()->format('Y-m-d H:i'));
        $endweek = strtotime($now->endOfWeek()->format('Y-m-d H:i'));
        foreach($damages as $damage)
        {
            if($damage->appointment_start != null && strtotime($damage->appointment_start))
            $supplement = new \stdClass();
            $supplement->supplement = $damage->supplement;
            //$supplement->date = $damage->appointment_start;

            array_push($supplements,$supplement);
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
