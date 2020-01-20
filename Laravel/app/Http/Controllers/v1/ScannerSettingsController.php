<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ScannerSettings;
use App\Http\CustomClasses\v1\Greeklish;
use Validator;
use App\Http\Resources\ScannerSettingsResource;

class ScannerSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(["data" => ScannerSettingsResource::collection(ScannerSettings::all())]);
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
            "title" => "required|string"
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => "Το όνομα του αρχείου δεν πρέπει να είναι κενό!"], 422);
        }

        $filename = Greeklish::remove_accent($request->title);
        $filename = strtolower($filename);

        $name_exists = ScannerSettings::where('filename', $filename)->first();

        if ($name_exists) {
            return response()->json(["message" => "Το συγκεκριμένο όνομα αρχείου υπάρχει ήδη στο σύστημα"], 422);
        }

        ScannerSettings::create(['filename' => $filename, 'title' => $request->title]);
        return response()->json(["message" => "Η νέα ονομασία αρχείου με όνομα επιλογής" . $request->title . " και όνομα αρχείου " . $filename . " καταχωρήθηκε επιτυχώς"], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $scanner_setting = ScannerSettings::where('id', $id)->first();
        if (!$scanner_setting) {
            return response()->json(["message" => "Δεν βρέθηκε η ονομασία που αναζητείτε"], 404);
        }

        return response()->json(["data" => ScannerSettingsResource::make($scanner_setting)], 200);
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
        $validator = Validator::make($request->all(), [
            "title" => "required|string"
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => "Το όνομα του αρχείου δεν πρέπει να είναι κενό!"], 422);
        }

        $scanner_settings = ScannerSettings::find($id);
        if (!$scanner_settings) {
            return response()->json(["message" => "Δεν βρέθηκε η εγγραφή προς ενημέρωση"], 404);
        }

        $filename = Greeklish::remove_accent($request->title);
        $filename = strtolower($filename);

        $name_exists = ScannerSettings::where('filename', $filename)->where('id', '!=', $id)->first();

        if ($name_exists) {
            return response()->json(["message" => "Το συγκεκριμένο όνομα αρχείου υπάρχει ήδη στο σύστημα"], 422);
        }

        $scanner_settings->update(['filename' => $filename, 'title' => $request->title]);
        return response()->json(["message" => "Η νέα ονομασία αρχείου με όνομα επιλογής" . $request->title . " και όνομα αρχείου " . $filename . " καταχωρήθηκε επιτυχώς"], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $scanner_settings = ScannerSettings::find($id);
        if (!$scanner_settings) {
            return response()->json(["message" => "Δεν βρέθηκε η εγγραφή προς ενημέρωση"], 404);
        }

        $scanner_settings->delete();
        return response()->json(["message" => "Η ονομασία διαγράφηκε επιτυχώς!"], 200);
    }
}
