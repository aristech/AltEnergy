<?php

namespace App\Http\Controllers\v1;

use App\Damage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\CustomClasses\v1\DamageSuperAdmin;

class DamageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role_id = $request->user()->role()->first()->id;
        if($role_id >= 5)
        {
            return DamageSuperAdmin::getDamages();
        }
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
        $role_id = $request->user()->role()->first()->id;
        if($role_id >= 4)
        {
            $damage = new DamageSuperAdmin($request);
            return $damage->storeDamage();
        }
        else
        {
            return response()->json(["message" => "Ο χρήστης με ρόλο ".$request->user()->role()->first()->name." δεν μπορεί να εισάγει βλάβες στο σύστημα!"],401);
        }

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
        $damage = new DamageSuperAdmin($request);
        return $damage->updateDamage();
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
        if($role_id < 4)
        {
            return response()->json(["message" => "Χρήστες με δικαίωμα ".$request->user()->role()->first()->name." δεν μπορεί να πραγματοποιήσει την ενέργεια αυτή!"],401);
        }

        $damage = Damage::where('id',$request->id)->first();
        if(!$damage)
        {
            return response()->json(["message" => "Η ζημιά που αναζητείτε δεν είναι καταχωρημένη!"],404);
        }

        $damage->delete();
        return response()->json(["message" => "Η βλάβη διαγραφηκε επιτυχώς!"],200);
    }
}
