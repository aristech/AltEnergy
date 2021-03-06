<?php

namespace App\Http\Controllers\v1;

use App\Damage;
use App\Http\Resources\DamageResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\CustomClasses\v1\DamageSuperAdmin;
use App\Http\CustomClasses\v1\DamageCalendarUpdate;
use App\Calendar;

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
        if ($role_id >= 3) {
            return DamageSuperAdmin::getDamages();
        }
    }

    public function history(Request $request)
    {
        $role_id = $request->user()->role()->first()->id;
        if ($role_id >= 3) {
            return DamageSuperAdmin::getDamagesHistory();
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
            $damage = new DamageSuperAdmin($request);
            return $damage->storeDamage();
        } else {
            return response()->json(["message" => "Ο χρήστης αυτός δεν μπορεί να εισάγει βλάβες στο σύστημα!"], 401);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $damage)
    {
        $role_id = $request->user()->role()->first()->id;
        if ($role_id < 2) {
            return response()->json(["message" => "Ο χρήστης με ρόλο " . $request->user()->role()->first()->title . " δεν μπορεί να έχει πρόσβαση στα στοιχεία αυτά"], 401);
        }

        if ($role_id >= 3) {
            $damage = Damage::find($damage);
            if (!$damage) return response()->json(["message" => "Δεν βρέθηκε η βλάβη στο σύστημα"], 404);

            return DamageResource::make($damage);
        }

        if ($role_id == 2) {
            $dmg = Damage::where('id', $damage)->first();
            $manager_id = $dmg['client']['manager']['id'];
            if ($manager_id == $request->user()->manager_id) {
                return DamageResource::make($dmg);
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $damage)
    {
        $role_id = $request->user()->role()->first()->id;
        if ($role_id < 3) {
            return response()->json(["message" => "Ο χρήστης με ρόλο " . $request->user()->role()->first()->title . " δεν μπορεί να έχει πρόσβαση στα στοιχεία αυτά"], 401);
        }
        $damage = new DamageCalendarUpdate($request, $damage);
        return $damage->updateDamage();
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
        if ($role_id < 2) {
            return response()->json(["message" => "Ο χρήστης με ρόλο " . $request->user()->role()->first()->title . " δεν μπορεί να έχει πρόσβαση στα στοιχεία αυτά"], 401);
        }
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
        if ($role_id < 3) {
            return response()->json(["message" => "Χρήστες με δικαίωμα " . $request->user()->role()->first()->name . " δεν μπορεί να πραγματοποιήσει την ενέργεια αυτή!"], 401);
        }

        $damage = Damage::where('id', $request->id)->first();
        if (!$damage) {
            return response()->json(["message" => "Η βλάβη με κωδικό " . $request->id . " δεν είναι καταχωρημένη!"], 404);
        }

        $damage->delete();
        //delete stored entry in calendar
        $calendar = Calendar::where('damage_id', $request->id)->first();
        if ($calendar) {
            $calendar->delete();
            //end delete calendar entry
        }



        return response()->json(["message" => "Η βλάβη με κωδικό " . $request->id . " διαγραφηκε επιτυχώς!"], 200);
    }

    public function remove(Request $request, $damageId)
    {
        $role_id = $request->user()->role()->first()->id;
        if ($role_id < 3) {
            return response()->json(["message" => "Χρήστες με δικαίωμα " . $request->user()->role()->first()->title . " δεν μπορεί να πραγματοποιήσει την ενέργεια αυτή!"], 401);
        }

        $damage = Damage::where('id', $damageId)->first();
        if (!$damage) {
            return response()->json(["message" => "Η βλάβη με κωδικό " . $damageId . " δεν είναι καταχωρημένη!"], 404);
        }

        $damage->delete();
        //delete stored entry in calendar
        $calendar = Calendar::where('damage_id', $damageId)->first();
        if ($calendar) {
            $calendar->delete();
            //end delete calendar entry
        }

        return response()->json(["message" => "Η βλάβη με κωδικό " . $damageId . " διαγραφηκε επιτυχώς!"], 200);
    }
}
