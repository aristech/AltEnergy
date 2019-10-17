<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Client;
use Validator;
use App\Manager;
use App\Http\Resources\ClientResource;
use App\Http\CustomClasses\v1\Greeklish;
use App\Http\CustomClasses\v1\SendMail;


class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mail = new SendMail();
        $mail->getDamages();
        $mail->getEvents();
        $mail->createMessage();
        $mail->sendMail();

        return $mail->message;
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
            'afm' => 'nullable|string',
            'doy' => 'nullable|string',
            'arithmos_gnostopoihshs' => 'nullable|string' ,
            'arithmos_meletis' => 'nullable|string' ,
            'arithmos_hkasp' => 'nullable|string',
            'arithmos_aitisis' => 'nullable|string',
            'plithos_diamerismaton' => 'nullable|string',
            'dieuthinsi_paroxis' => 'nullable|string',
            'kw_oikiako' => 'nullable|string',
            'kw' => 'nullable|string',
            'levitas' => 'nullable|string',
            'telephone' => 'nullable|string',
            'telephone2' => 'nullable|string',
            'mobile' => 'nullable|string',
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

        if($request->telephone == null && $request->telephone2 == null && $request->mobile == null)
        {
            return response()->json(["message" => "Τουλάχιστον έαν τηλέφωνο είναι υποχρεωτικό!"],422);
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
                return response()->json(["message" => "Ο συγκεκριμένος διαχειριστής δεν είναι καταχωρημένος στο σύστημα"],404);
            }
        }


        $lastClient = Client::latest()->first();

        $foldername = $request->lastname."_".$request->firstname."_".($lastClient->id + 1);
        $foldername = Greeklish::remove_accent($foldername);//conversion to greeklish
        $request->request->add(["foldername" => $foldername]);

        $client = Client::create($request->all());


        //mkdir(storage_path()."/Clients/".$client->foldername);
        if(!$folder_created = mkdir(storage_path()."/Clients/".$client->foldername))
        {
             return response()->json(["message" => "Δεν μπόρεσε να δημιουργηθεί φάκελος για τον πελάτη ".$request->lastname." ".$request->firstname],500);
        }

        return response()->json(["message" => "Ο νέος χρήστης καταχωρήθηκε επιτυχώς!"],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $client)
    {
        $role_id = $request->user()->role()->first()->id;
        if($role_id < 4 || $request->user()->active == false)
        {
            return response()->json(["message" => "Δεν έχετε δικαίωμα να εκτελέσετε την συγκεκριμένη ενέργεια!"],401);
        }

        $client = Client::find($client);

        if(!$client)
        {
            return response()->json(["message" => "Δεν υπάρχει ο πελάτης που αναζητείτε!"],404);
        }

        return ClientResource::make($client);
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
            'id' => 'required|integer',
            'lastname' => 'required|string',
            'firstname' => 'required|string',
            'afm' => 'nullable|string',
            'doy' => 'nullable|string',
            'arithmos_gnostopoihshs' => 'nullable|string' ,
            'arithmos_meletis' => 'nullable|string' ,
            'arithmos_hkasp' => 'nullable|string',
            'arithmos_aitisis' => 'nullable|string',
            'plithos_diamerismaton' => 'nullable|string',
            'dieuthinsi_paroxis' => 'nullable|string',
            'kw_oikiako' => 'nullable|string',
            'kw' => 'nullable|string',
            'levitas' => 'nullable|string',
            'telephone' => 'nullable|string',
            'telephone2' => 'nullable|string',
            'mobile' => 'nullable|string',
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

        if($request->telephone == null && $request->telephone2 == null && $request->mobile == null)
        {
            return response()->json(["message" => "Τουλάχιστον ένα τηλέφωνο είναι υποχρεωτικό!"],422);
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

        if($request->email != null)
        {
            $email = Client::where('email',$request->email)->where('id',"!=",$request->id)->first();

            if($email)
            {
                return response()->json(["message" => "Το mail αυτο χρησιμοποιείται από άλλο χρήστη"],422);
            }
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
    public function destroy(Request $request)
    {
        $role_id = $request->user()->role()->first()->id;
        if($role_id < 4 || $request->user()->active == false)
        {
            return response()->json(["message" => "Δεν έχετε δικαίωμα να εκτελέσετε την συγκεκριμένη ενέργεια!"],401);
        }

        $validator = Validator::make($request->all(),
        [
            'id' => 'required|integer'
        ]);

        if($validator->fails())
        {
            $failedRules = $validator->errors()->first();//todo for future: na allaksw
            return response()->json(["message" => $failedRules],422);
        }

       $client = Client::where('id',$request->id)->first();
       if(!$client)
       {
            return response()->json(["message" => "Ο πελάτης που θέλετε να διαγράψετε δεν υπάρχει στο σύστημα!"],404);
       }

       $client->delete();

       $folder = storage_path('/Clients/'.$client->foldername);

        //Get a list of all of the file names in the folder.
        $files = glob($folder . '/*');

        //Loop through the file list.
        foreach($files as $file){
            //Make sure that this is a file and not a directory.
            if(is_file($file)){
                //Use the unlink function to delete the file.
                unlink($file);
            }
        }

       rmdir($folder);

       return response()->json(["message" => "Ο χρήστης διαγράφηκε επιτυχώς!"],200);

    }
}
