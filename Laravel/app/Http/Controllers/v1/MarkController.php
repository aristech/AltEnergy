<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mark;
use App\Manufacturer;
use App\Http\Resources\MarkResource;
use Validator;


class MarkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $manufacturer)
    {
        $role_id = $request->user()->role()->first()->id;
        if($role_id < 3)
        {
            return response()->json(["message" => "Δεν έχετε δικαίωμα να εκτελέσετε την συγκεκριμένη ενέργεια!"],401);
        }

        $manu_id = $manufacturer;

        $marks = Mark::whereHas('manufacturer', function($query) use ($manu_id)
        {
            $query->where('id',$manu_id);

        })
        ->get();

        return MarkResource::collection($marks);

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
    public function store(Request $request, $manufacturer)
    {
        $role_id = $request->user()->role()->first()->id;
        if($role_id < 3)
        {
            return response()->json(["message" => "Δεν έχετε δικαίωμα να εκτελέσετε την συγκεκριμένη ενέργεια!"],401);
        }

        $validator = Validator::make($request->all(),
        [
            "name" => "required|string"
        ]);

        if($validator->fails())
        {
            return response()->json(["message" => $validator->errors()->first()],422);
        }

        $manufacturer = Manufacturer::find($manufacturer);
        if(!$manufacturer)
        {
            return response()->json(["message" => "Ο κατασκευαστής αυτος δεν βρέθηκε!"],404);
        }

        $input = array("name" => $request->name, "manufacturer_id" => $manufacturer->id);

        Mark::create($input);
        return response()->json(["message" => "Tο νέο μοντέλο καταχωρήθηκε επιτυχώς!"],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $manufacturer)
    {

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
    public function destroy(Request $request, $manufacturer)
    {
        $role_id = $request->user()->role()->first()->id;
        if($role_id < 3)
        {
            return response()->json(["message" => "Δεν έχετε δικαίωμα να εκτελέσετε την συγκεκριμένη ενέργεια!"],401);
        }

        $validator = Validator::make($request->all(),
        [
            "id" => "required"
        ]);

        if($validator->fails())
        {
            return response()->json(["message" => $validator->errors()->first()],422);
        }

        $mark = Mark::whereHas('manufacturer',function($query) use ($manufacturer)
        {
            $query->where('id',$manufacturer);
        })
        ->find($request->id);

        if(!$mark)
        {
            return response()->json(["message" => "Δεν υπάρχει το συγκεκριμένο μοντέλο!"],404);
        }

        $mark->delete();
        return response()->json(["message" => "Tο μοντελο διαγράφηκε επιτυχώς!"],200);
    }
}
