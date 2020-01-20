<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Manager;
use App\User;
use Validator;
use App\Http\Resources\ManagerResource;

class ManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $role_id = $request->user()->role()->first()->id;
        // if ($role_id < 4) {
        //     return response()->json(["message" => "Δεν επιτρέπεται η εμφάνιση των διαχειριστών!"], 401);
        // }
        return ManagerResource::collection(Manager::all());
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
        // $role_id = $request->user()->role()->first()->id;
        // if ($role_id < 4 || $request->user()->active == false) {
        //     return response()->json(["message" => "Δεν έχετε δικαίωμα να εκτελέσετε την συγκεκριμένη ενέργεια!"], 401);
        // }

        $validator = Validator::make(
            $request->all(),
            [
                'lastname' => 'required|string',
                'firstname' => 'required|string',
                'telephone' => 'nullable|string',
                'telephone2' => 'nullable|string',
                'mobile' => 'nullable|string',
                'email' => 'nullable|string|email'
            ]
        );

        if ($validator->fails()) {
            $failedRules = $validator->errors()->first(); //todo for future: na allaksw
            return response()->json(["message" => $failedRules], 422);
        }

        if ($request->email != null) {
            $manager = Manager::where('email', $request->email)->first();

            if ($manager) {
                return response()->json(["message" => "Υπάρχει ήδη πελάτης με το email " . $request->email], 422);
            }
        }

        if ($request->telephone == null && $request->telephone2 == null && $request->mobile == null) {
            return response()->json(["message" => "τουλάχιστον ένα τηλέφωνο είναι υποχρεώτικο!"], 422);
        }

        Manager::create($request->all());

        return response()->json(["message" => "Ο νέος διαχειριστης καταχωρήθηκε επιτυχώς!"], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $manager)
    {
        // $role_id = $request->user()->role()->first()->id;
        // if ($role_id < 4) {
        //     return response()->json(["message" => "Δεν επιτρέπεται η εμφάνιση των διαχειριστών!"], 401);
        // }

        $manager = Manager::find($manager);

        if (!$manager) {
            return response()->json(["message" => "Δεν βρέθηκε ο συγκεκριμένος διαχειριστής!"], 404);
        }
        return ManagerResource::make($manager);
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
        // $role_id = $request->user()->role()->first()->id;
        // if ($role_id < 4 || $request->user()->active == false) {
        //     return response()->json(["message" => "Δεν έχετε δικαίωμα να εκτελέσετε την συγκεκριμένη ενέργεια!"], 401);
        // }

        $validator = Validator::make(
            $request->all(),
            [
                'lastname' => 'required|string',
                'firstname' => 'required|string',
                'telephone' => 'nullable|string',
                'telephone2' => 'nullable|string',
                'mobile' => 'nullable|string',
                'email' => 'nullable|string|email'
            ]
        );

        if ($validator->fails()) {
            $failedRules = $validator->errors()->first(); //todo for future: na allaksw
            return response()->json(["message" => $failedRules], 422);
        }

        if ($request->telephone == null && $request->telephone2 && $request->mobile == null) {
            return response()->json(["message" => "τουλάχιστον ένα τηλέφωνο είναι υποχρεώτικο!"], 422);
        }

        $manager = Manager::where('id', $request->id)->first();
        if (!$manager) {
            return response()->json(["message" => "Δεν υπάρχει ο συγκεκριμένος πελάτης με κωδικό " . $request->id], 404);
        }

        $email = Manager::where('email', $request->email)->where('id', "!=", $request->id)->first();

        if ($manager->email != $request->email || $email) {
            return response()->json(["message" => "Το mail αυτο χρησιμοποιείται από άλλο διαχειριστή"], 422);
        }

        $manager->update($request->except(['id']));

        return response()->json(["message" => "Ο διαχειριστής με κωδικο " . $request->id . " ενημερώθηκε επιτυχώς!"], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        // $role_id = $request->user()->role()->first()->id;
        // if ($role_id < 4 || $request->user()->active == false) {
        //     return response()->json(["message" => "Δεν έχετε δικαίωμα να εκτελέσετε την συγκεκριμένη ενέργεια!"], 401);
        // }

        $validator = Validator::make(
            $request->all(),
            [
                'id' => 'required|integer'
            ]
        );

        if ($validator->fails()) {
            $failedRules = $validator->errors()->first(); //todo for future: na allaksw
            return response()->json(["message" => $failedRules], 422);
        }

        $manager = Manager::where('id', $request->id)->first();
        if (!$manager) {
            return response()->json(["message" => "Ο διαχειριστής που θέλετε να διαγράψετε δεν υπάρχει στο σύστημα!"], 404);
        }

        User::where('manager_id', $manager['id'])->delete();
        $manager->delete();

        return response()->json(["message" => "Ο διαχειριστής διαγράφηκε επιτυχώς!"], 200);
    }
}
