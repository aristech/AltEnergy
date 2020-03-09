<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Bullet;
use App\Http\Resources\BulletResource;

class BulletController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return BulletResource::collection(Bullet::all());
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
        if ($request->description === null && $request->description === "") {
            return response()->json(["message" => "Η περιγραφή δεν πρέπει να είναι κενη"], 422);
        }

        if ($request->price != null && !is_numeric($request->price)) {
            return response()->json(["message" => "Η τιμή πρέπει να είναι αριθμός"], 422);
        }


        Bullet::create(["description" => $request->description, "price" => round($request->price, 2)]);

        return response()->json(["message" => "Η εγγραφή για προσφορές καταχωρηθηκε επιτυχώς"], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $bult)
    {
        $bullet = Bullet::where('id', $bult)->first();
        if (!$bullet) {
            return response()->json(["message" => "Δεν υπάρχει η εγγραφή στο σύστημα"], 404);
        }
        return BulletResource::make($bullet);
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
    public function update(Request $request, $bullet)
    {
        $selected = Bullet::where('id', $bullet)->first();
        if (!$selected) {
            return response()->json(["message" => "Δεν βρέθηκε η εγγραφή στο σύστημα"], 422);
        }

        if ($request->price != null && !is_numeric($request->price)) {
            return response()->json(["message" => "Η τιμή πρέπει να είναι αριθμός"], 422);
        }

        $selected->update(["description" => $request->description, "price" => round($request->price, 2)]);

        return response()->json(["message" => "Η εγγραφή ενημερώθηκε επιτυχώς"], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $bullet)
    {
        $selected = Bullet::where('id', $bullet)->first();
        if (!$selected) {
            return response()->json(["message" => "Δεν βρέθηκε η εγγραφή στο σύστημα"], 422);
        }

        $selected->delete();

        return response()->json(["message" => "H εγγραφή διαγράφτηκε επιτυχώς"], 200);
    }
}
