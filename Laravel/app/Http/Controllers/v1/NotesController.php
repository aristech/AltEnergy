<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\CustomClasses\v1\NotesManagement;
use Illuminate\Http\Request;
use App\Http\Resources\NoteResource;
use App\Note;
use App\Calendar;

class NotesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        // if($request->user()->role()->first()->id < 3)
        // {
        //     return response()->json(["message" => "Ο χρήστης αυτός δεν έχει πρόσβαση!"],401);
        // }

        $note = new NotesManagement($request);
        return $note->storeNote();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $note)
    {
        // if($request->user()->role()->first()->id < 3)
        // {
        //     return response()->json(["message" => "Ο χρήστης αυτός δεν μπορεί να έχει πρόσβαση!"],401);
        // }

        $note = Note::find($note);
        if (!$note) {
            return response()->json(["message" => "Δεν βρέθηκε η σημείωση"], 404);
        }

        return NoteResource::make($note);
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
    public function update(Request $request, $note)
    {
        // if($request->user()->role()->first()->id < 3)
        // {
        //     return response()->json(["message" => "Ο χρήστης αυτός δεν έχει πρόσβαση!"],401);
        // }

        $noteObj = new NotesManagement($request);
        $noteObj->id = $note;
        return $noteObj->updateNote();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $note)
    {
        // if($request->user()->role()->first()->id < 3)
        // {
        //     return response()->json(["message" => "Ο χρήστης αυτός δεν μπορεί να έχει πρόσβαση!"],401);
        // }

        $noteMod = Note::find($note);
        if (!$noteMod) {
            return response()->json(["message" => "Δεν βρέθηκε η σημείωση"], 404);
        }

        //$note->delete();
        $calendar = Calendar::where("type", "notes")->where('note_id', $note)->first();
        if ($calendar && $noteMod) {
            $calendar->delete();
            $noteMod->delete();

            return response()->json(["message" => "Η σημείωση διαγράφηκε επιτυχώς"], 200);
        }

        return response()->json(["message" => "Παρακαλώ βεβαιωθείτε οτι υπάρχει η σημείωση που επιθυμείτε να διαγράψετε"], 404);
    }
}
