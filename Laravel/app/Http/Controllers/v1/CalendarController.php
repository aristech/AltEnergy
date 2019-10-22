<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\CalendarResource;
use App\Calendar;
use App\Damage;
use App\Eventt;

class CalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->user()->role()->first()->id < 3 )
        {
            return response()->json(["message" => "Ο χρήστης αυτός δεν μπορεί να έχει πρόσβαση στο ημερολόγιο"],401);
        }
        return CalendarResource::collection(Calendar::all());
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
    public function update(Request $request)
    {
        // $type = $request->type;
        // $id = $request->event_id;
        // $start = $request->start;
        // $end = $request->end;

        // switch ($type) {
        //     case "damages":
        //        $damage = Damage::find($id);
        //        if(!$damage)
        //        {
        //           return response()->json(["message" => "Δεν βρέθηκε η βλάβη"],404);
        //        }
        //        $damage->update(["appointment_start"=>$start,"appointment_end"=>$end]);
        //        return response()->json(["message" => "Εγινε μετακίνηση στο ραντεβού για τις ".$end],200);
        //        break;
        //     case "events":
        //         code to be executed if n=label2;
        //         break;
        //     case label3:
        //         code to be executed if n=label3;
        //         break;
        //     ...
        //     default:
        //         return response()->json(["message" => "Δεν επιτρέπεται η ενέργεια"],401);
        // }
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
