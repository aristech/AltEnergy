<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Client;
use Validator;
use App\Manager;
use App\Http\Resources\ClientResource;


class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ClientResource::collection(Client::all());
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

        //return $role_id;

        if($role_id < 4 || $request->user()->active == false)
        {
            return response()->json(["message" => "Δεν έχετε δικαίωμα να εκτελέσετε την συγκεκριμένη ενέργεια!"],401);
        }

        $validator = Validator::make($request->all(),
        [
            'lastname' => 'required|string',
            'firstname' => 'required|string',
            'afm' => 'required|string',
            'doy' => 'required|string',
            'telephone' => 'required|string',
            'telephone2' => 'required|string',
            'mobile' => 'required|string',
            'address' => 'required|string',
            'zipcode' => 'required|string',
            'location' => 'required|string',
            'level' =>'required|string',
            'manager_id' => 'nullable|integer',
            'email' => 'nullable|string|email'

        ]);

        if($validator->fails())
        {
            $failedRules = $validator->errors()->first();//todo for future: na allaksw
            return response()->json(["message" => $failedRules],422);
        }

        if($request->email != null)
        {
            $client = Client::where('email',$request->email)->first();

            if($client)
            {
                return response()->json(["message" => "Υπάρχει ήδη πελάτης με το email ".$request->email],422);
            }
        }

        if($request->manager_id != null)
        {
            $manager = Manager::find($request->manager_id);
            if(!$manager)
            {
                return response()->json(["message" => "Ο συγκεκριμένος διαχειριστής δεν είναι καταψχωρημένος στο σύστημα"],404);
            }
        }


        Client::create($request->all());

        return response()->json(["message" => "Ο νέος χρήστης καταχωρήθηκε επιτυχώς!"],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $role_id = $request->user()->role()->first()->id;
        if($role_id < 4 || $request->user()->active == false)
        {
            return response()->json(["message" => "Δεν έχετε δικαίωμα να εκτελέσετε την συγκεκριμένη ενέργεια!"],401);
        }

        $client = Client::where('id',$request->id)->first();
        if(!$client)
        {
            return response()->json(["message" => "Δεν υπάρχει ο χρήστης που αναζητείτε!"],404);
        }

        return $client;


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
        if($role_id < 4 || $request->user()->active == false)
        {
            return response()->json(["message" => "Δεν έχετε δικαίωμα να εκτελέσετε την συγκεκριμένη ενέργεια!"],401);
        }

        $validator = Validator::make($request->all(),
        [
            'lastname' => 'required|string',
            'firstname' => 'required|string',
            'afm' => 'required|string',
            'doy' => 'required|string',
            'telephone' => 'required|string',
            'telephone2' => 'required|string',
            'mobile' => 'required|string',
            'address' => 'required|string',
            'zipcode' => 'required|string',
            'location' => 'required|string',
            'level' =>'required|string',
            'manager_id' => 'nullable|integer'
        ]);

        if($validator->fails())
        {
            $failedRules = $validator->errors()->first();//todo for future: na allaksw
            return response()->json(["message" => $failedRules],422);
        }

        if($request->manager_id != null)
        {
            $manager = Manager::find($request->manager_id);
            if(!$manager)
            {
                return response()->json(["message" => "Ο συγκεκριμένος διαχειριστής δεν είναι καταψχωρημένος στο σύστημα"],404);
            }
        }

        $client = Client::where('id',$request->id)->first();
        if(!$client)
        {
            return response()->json(["message" => "Δεν υπάρχει ο συγκεκριμένος πελάτης με κωδικό ".$request->id],404);
        }

        $email = Client::where('email',$request->email)->where('id',"!=",$request->id)->first();

        if($client->email != $request->email || $email)
        {
            return response()->json(["message" => "Το mail αυτο χρησιμοποιείται από άλλο χρήστη"],422);
        }

        $client->update($request->except(['id']));

        return response()->json(["message" => "Τα στοιχεία πελάτη ενημερώθηκαν επιτυχώς!"],200);
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
