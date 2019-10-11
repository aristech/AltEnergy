<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Eventt;
use App\Http\CustomClasses\v1\EventMod;
use App\Http\Resources\EventResource;
use App\Calendar;

class EventController extends Controller
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
            return response()->json(["message" => "Ο χρήστης με ρόλο ".$request->user()->role()->first()->name." δεν μπορεί να έχει πρόσβαση στα στοιχεία αυτά"],401);
        }

        $events = Eventt::where('status','Μη Ολοκληρωμένο')->get();
        if(count($events) == 0)return response()->json(["message" => "Δεν βρέθηκαν αποτελέσματα"],404);
        return EventResource::collection($events);
    }

    public function history(Request $request)
    {
        $role_id = $request->user()->role()->first()->id;
        if($role_id < 3)
        {
            return response()->json(["message" => "Ο χρήστης με ρόλο ".$request->user()->role()->first()->name." δεν μπορεί να έχει πρόσβαση στα στοιχεία αυτά"],401);
        }

        $events = Eventt::where('status','!=','Μη Ολοκληρωμένο')->get();
        if(count($events) == 0) return response()->json(["message" => "Δεν βρέθηκαν αποτελέσματα"],404);
        return EventResource::collection($events);
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
        $role_id = $request->user()->role()->first()->id;
        if($role_id < 3)
        {
            return response()->json(["message" => "Ο χρήστης με ρόλο ".$request->user()->role()->first()->name." δεν μπορεί να έχει πρόσβαση στα στοιχεία αυτά"],401);
        }

        $event = new EventMod($request);
        return $event->storeEvent();
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
        $role_id = $request->user()->role()->first()->id;
        if($role_id < 3)
        {
            return response()->json(["message" => "Ο χρήστης με ρόλο ".$request->user()->role()->first()->name." δεν μπορεί να έχει πρόσβαση στα στοιχεία αυτά"],401);
        }

        $event = new EventMod($request);
        return $event->updateEvent();
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
        if($role_id < 3)
        {
            return response()->json(["message" => "Ο χρήστης με ρόλο ".$request->user()->role()->first()->name." δεν μπορεί να κάνει την ενέργεια αυτή"],401);
        }

       $event = Eventt::find($request->id);
       if(!$event)
       {
           return response()->json(["message" => "Δεν βρέθηκε το συγκεκριμένο event"],404);
       }
       $event->delete();
       //calendar action
       $calendar = Calendar::where('event_id',$request->id)->first();
       if(!$calendar)$calendar->delete();
       //end calendar
       return response()->json(["message" => "Το event διαγράφτηκε επιτυχώς"],200);
    }

}

