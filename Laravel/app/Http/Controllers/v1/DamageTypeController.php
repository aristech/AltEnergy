<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DamageType;
use Validator;
use App\Http\Resources\DamageTypeResource;

class DamageTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->user()->role()->first()->id < 3) {
            return response()->json(["message" => "Ο συγκεκριμένος χρήστης δεν έχει πρόσβαση στο πεδία αυτό"], 401);
        }

        return DamageTypeResource::collection(DamageType::all());
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
        if ($request->user()->role()->first()->id < 3) {
            return response()->json(["message" => "Ο συγκεκριμένος χρήστης δεν έχει πρόσβαση στο πεδία αυτό"], 401);
        }

        $validator = Validator::make(
            $request->all(),
            [
                "name" => "required|string"
            ]
        );

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()]);
        }

        $new_dmg_type = DamageType::create($request->all());
        return response()->json(["message" => "Ο τύπος βλάβης αποθηκεύτηκε επιτυχώς!", "id" => $new_dmg_type->id], 200);
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
    public function destroy(Request $request)
    {
        if ($request->user()->role()->first()->id < 3) {
            return response()->json(["message" => "Ο συγκεκριμένος χρήστης δεν έχει πρόσβαση στο πεδία αυτό"], 401);
        }

        $damage_type_id = $request->id;
        $damage_type = DamageType::where('id', $damage_type_id)->first();
        if (!$damage_type) {
            return response()->json(["message" => "Η συγκεκριμενη βλάβη δεν υπάρχει στο σύστημα!"], 404);
        }
        $damage_type->delete();
        return response()->json(["message" => "Ο συγκεκριμένος τυπος βλάβης διαγράφηκε επιτυχώς!"], 200);
    }
}
