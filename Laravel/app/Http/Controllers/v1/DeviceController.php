<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Device;
use App\Mark;
use Validator;
use App\Http\Resources\DeviceResource;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role_id = $request->user()->role()->first()->id;
        if($role_id < 4 || $request->user()->active == false)
        {
            return response()->json(["message" => "Δεν έχετε δικαίωμα να εκτελέσετε την συγκεκριμένη ενέργεια!"],401);
        }

        $validator = Validator::make($request->all(),
        ["mark_id" => "required|integer"]);

        if($validator->fails())
        {
            return response()->json(["message" => $validator->errors()->first()],404);
        }

        $mark_id = $request->mark_id;

        $devs = Device::whereHas('mark', function($query) use ($mark_id)
        {
            $query->where('mark_id',$mark_id);

        })
        ->get();

        return DeviceResource::collection(Device::all());
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
        if($role_id < 4 || $request->user()->active == false)
        {
            return response()->json(["message" => "Δεν έχετε δικαίωμα να εκτελέσετε την συγκεκριμένη ενέργεια!"],401);
        }

        $validator = Validator::make($request->all(),
        [
            "name" => "required|string",
            "mark_id" => "required|integer"
        ]);

        $mark = Mark::where('id',$request->mark_id)->first();
        if($mark)
        {
            return response()->json(["message" => "Δεν ύπαρχει το συγκεκριμένο μοντέλο"],200);
        }

        $input = array(["name" => $request->name]);

        $device = Device::create($input);
        DeviceMark::create(["device_id" => $device->id, "mark_id" => $request->mark_id]);

        return response()->json(["message" => "Η νέα συσκευή καταχωρήθηκε επιτυχώς!"],200);
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
        $role_id = $request->user()->role()->first()->id;
        if($role_id < 4 || $request->user()->active == false)
        {
            return response()->json(["message" => "Δεν έχετε δικαίωμα να εκτελέσετε την συγκεκριμένη ενέργεια!"],401);
        }

        $validator = Validator::make($request->all(),
        [
            "name" => "required|string",
            "mark_id" => "required|integer"
        ]);

        $mark = Mark::where('id',$request->mark_id)->first();
        if($mark)
        {
            return response()->json(["message" => "Δεν ύπαρχει το συγκεκριμένο μοντέλο"],200);
        }

        $input = array(["name" => $request->name]);
        $device = Device::create($input);
        DeviceMark::create(["device_id" => $device->id, "mark_id" => $request->mark_id]);

        return response()->json(["message" => "Η συσκευή διεγράφη επιτυχώς!"],200);
    }
}
