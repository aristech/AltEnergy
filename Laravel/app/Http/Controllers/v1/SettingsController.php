<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ScannerSettings;
use App\Bullet;
use App\Http\Resources\ScannerSettingsResource;
use App\Http\Resources\BulletResource;
use App\OfferText;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $settings = array();

        $settings_type = new \stdClass();
        $settings_type->name = "Scanner";
        $settings_type->url = "scanner";
        $settings_type->data = ScannerSettingsResource::collection(ScannerSettings::all());

        array_push($settings, $settings_type);

        $settings_type = new \stdClass();
        $settings_type->name = "Χρώματα";
        $settings_type->url = "colors";
        $settings_type->data = array();

        array_push($settings, $settings_type);

        $settings_type = new \stdClass();
        $settings_type->name = "Τμήματα Προσφορών";
        $settings_type->url = "bullets";
        $settings_type->data = BulletResource::collection(Bullet::all());

        array_push($settings, $settings_type);
        //offer text entity
        $settings_type = new \stdClass();
        $settings_type->name = "Κείμενα Προσφορών";
        $settings_type->url = "offer_texts";
        $settings_type->data = OfferText::all();

        array_push($settings, $settings_type);
        //offer text entity end
        return response()->json(["data" => $settings], 200);
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
        //
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
    public function destroy($id)
    {
        //
    }
}
