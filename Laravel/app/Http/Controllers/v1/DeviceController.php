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
    public function index(Request $request, $manufacturer, $mark)
    {
        $role_id = $request->user()->role()->first()->id;
        if($role_id < 3)
        {
            return response()->json(["message" => "Δεν έχετε δικαίωμα να εκτελέσετε την συγκεκριμένη ενέργεια!"],401);
        }

        $mark_id = $mark;

        $devs = Device::whereHas('mark', function($query) use ($mark_id)
        {
            $query->where('id',$mark_id);

        })->whereHas('mark.manufacturer',function($query) use ($manufacturer)
        {
            $query->where('id',$manufacturer);
        })
        ->get();

        return DeviceResource::collection($devs);
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
    public function store(Request $request, $manufacturer, $mark)
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

        $mark = Mark::whereHas("manufacturer",function($query) use ($manufacturer)
        {
            $query->where('id',$manufacturer);
        })
        ->find($mark);

        if(!$mark)
        {
            return response()->json(["message" => "Δεν ύπαρχει το συγκεκριμένο μοντέλο"],200);
        }

        $input = array("name" => $request->name, "mark_id" => $mark->id);

        $device = Device::create($input);

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
    public function destroy(Request $request, $manufacturer, $mark)
    {
        $role_id = $request->user()->role()->first()->id;
        if($role_id < 3)
        {
            return response()->json(["message" => "Δεν έχετε δικαίωμα να εκτελέσετε την συγκεκριμένη ενέργεια!"],401);
        }

        $validator = Validator::make($request->all(),
        [
            "id" => "required|integer"
        ]);

        if($validator->fails())
        {
            return response()->json(["message" => $validator->errors()->first()],422);
        }

        $device = Device::whereHas("mark",function($query) use ($mark)
        {
            $query->where('id',$mark);
        })
        ->whereHas("mark.manufacturer",function($query) use ($manufacturer)
        {
            $query->where('id',$manufacturer);
        })
        ->find($request->id);

        if(!$device)
        {
            return response()->json(["message" => "Η συγκεκριμένη συσκευή δεν υπάρχει στο σύστημα"],404);
        }

        $device->delete();

        return response()->json(["message" => "Η συσκευή διεγράφη επιτυχώς!"],200);
    }
}
