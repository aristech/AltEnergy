<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Manager;

class ManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Manager::all();
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
        if($role_id < 4)
        {
            return response()->json(["message" => "Δεν έχετε δικαίωμα να εκτελέσετε την συγκεκριμένη ενέργεια!"],401);
        }

        $validator = Validator::make($request->all(),
        [
            'lastname' => 'required|string',
            'firstname' => 'required|string',
            'telephone' => 'nullable|string',
            'telephone2' => 'nullable|string',
            'mobile' => 'nullable|string'
        ]);

        if($validator->fails())
        {
            $failedRules = $validator->errors()->first();//todo for future: na allaksw
            return response()->json(["message" => $failedRules],422);
        }

        if($request->telephone == null && $request->telephone2 && $request->mobile == null)
        {
            return response()->json(["message" => "τουλάχιστον ένα τηλέφωνο είναι υποχρεώτικο!"],422);
        }

        Manager::create($request->all());

        return response()->json(["message" => "Ο νέος διαχειριστης καταχωρήθηκε επιτυχώς!"],200);
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
        $validator = Validator::make($request->all(),
        [
            'lastname' => 'required|string',
            'firstname' => 'required|string',
            'telephone' => 'nullable|string',
            'telephone2' => 'nullable|string',
            'mobile' => 'nullable|string'
        ]);

        if($validator->fails())
        {
            $failedRules = $validator->errors()->first();//todo for future: na allaksw
            return response()->json(["message" => $failedRules],422);
        }

        if($request->telephone == null && $request->telephone2 && $request->mobile == null)
        {
            return response()->json(["message" => "τουλάχιστον ένα τηλέφωνο είναι υποχρεώτικο!"],422);
        }


        $manager = Manager::where('id',$request->id)->first();
        if(!$manager)
        {
            return response()->json(["message" => "Δεν υπάρχει ο συγκεκριμένος πελάτης με κωδικό ".$request->id],404);
        }

        $manager->update($request->except(['id']));

        return response()->json(["message" => "Ο νέος διαχειριστής καταχωρήθηκε επιτυχώς!"],200);
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
