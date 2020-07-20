<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\OfferText;
use Validator;

class OfferTextController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return OfferText::all();
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
        $validator = Validator::make($request->all(), [
            "type" => "required",
            "upper_text" => "required",
            "lower_text" => "required"
        ]);

        if ($validator->fails()) {
            $failedRules = $validator->errors()->first(); //todo for future: na allaksw
            return response()->json(["message" => $failedRules], 422);
        }

        OfferText::create($request->all());
        return response()->json(["message" => "Το νέο κείμενο αποθηκεύτηκε επιτυχώς!"], 200);
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
    public function update(Request $request, $offerText)
    {
        $validator = Validator::make($request->all(), [
            "type" => "required",
            "upper_text" => "required",
            "lower_text" => "required"
        ]);

        if ($validator->fails()) {
            $failedRules = $validator->errors()->first(); //todo for future: na allaksw
            return response()->json(["message" => $failedRules], 422);
        }

        $offerText = OfferText::where('id', $offerText)->first();
        if (!$offerText) {
            return response()->json(["message" => "Δεν βρέθηκε το κείμενο προς αλλαγή"], 404);
        }

        $offerText->update($request->all());


        return response()->json(["message" => "Το κείμενο ενημερώθηκε επιτυχώς!"], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $selected_offer_text = OfferText::where('id', $request->id)->first();
        if (!$selected_offer_text) {
            return response()->json(["message" => "Δεν βρέθηκε το κείμενο προσφοράς που θέλετε να διαγραψετε"], 404);
        }

        $selected_offer_text->delete();
        return response()->json(["message" => "Το κείμενο διαγράφτηκε επιτυχώς"], 200);
    }
}
