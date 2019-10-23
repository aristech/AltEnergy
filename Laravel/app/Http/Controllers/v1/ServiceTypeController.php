<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ServiceType;
use Validator;
use App\Http\Resources\ServiceTypeResource;

class ServiceTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->user()->role()->first()->id < 3)
        {
           return response()->json(["message" => "Ο συγκεκριμένος χρήστης δεν έχει πρόσβαση στο πεδία αυτό"],401);
        }

        return ServiceTypeResource::collection(ServiceType::all());

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
        if($request->user()->role()->first()->id < 3)
        {
           return response()->json(["message" => "Ο συγκεκριμένος χρήστης δεν έχει πρόσβαση στο πεδία αυτό"],401);
        }

        $validator = Validator::make($request->all(),
        [
            "name" => "required|string"
        ]);

        if($validator->fails())
        {
            return response()->json(["message"=>$validator->errors()->first()]);
        }

        ServiceType::create($request->all());
        return response()->json(["message" => "Ο τύπος service αποθηκεύτηκε επιτυχώς!"],200);
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
        if($request->user()->role()->first()->id < 3)
        {
           return response()->json(["message" => "Ο συγκεκριμένος χρήστης δεν έχει πρόσβαση στο πεδία αυτό"],401);
        }

        $service_type_id = $request->id;
        $service_type = ServiceType::where('id',$service_type_id)->first();
        if(!$service_type)
        {
            return response()->json(["message" => "Ο συγκεκριμένος τύπος service δεν υπάρχει στο σύστημα!"],404);
        }
        $service_type->delete();
        return response()->json(["message" => "Ο συγκεκριμένος τύπος service διαγράφηκε επιτυχώς!"],200);
    }
}
