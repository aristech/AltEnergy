<?php

namespace App\Http\CustomClasses\v1;
use Validator;
use Illuminate\Http\Request;
use App\Calendar;
use App\Note;
use App\User;

class NotesManagement
{
    protected $request;
    protected $hasError = false;
    protected $error;
    protected $message;
    public $id;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    //used for update and create notes
    protected function validateNote()
    {
        $validator = Validator::make($this->request->all(),
        [
            'title' => 'required|string',
            'description' => 'required',
            'importance' => 'required|integer',
            'all_day' => 'required|boolean',
            'dateTime_start' => 'required|string',
            'dateTime_end'  => 'required|string'
        ]);

        if($validator->fails())
        {
            $this->hasError = true;
            $this->error = response()->json(["message" => $validator->errors()->first()],422);
        }
    }

    public function checkImportance()
    {
        if($this->request->status < 0 && $this->request->status > 3 )
        {
            $this->hasError = 'true';
            $this->error = response()->json(["message" => "Ο βαθμός σημαντικότητας δεν είναι έγκυρος"],422);
        }
    }

    public function checkUser()
    {
        $user = User::find($this->request->user()->id);
        if(!$user)
        {
            $this->hasError = true;
            $this->error = response()->json(["message" => "Αυτός ο χρήστης δεν είναι καταχωρημένος στο σύστημα"],422);
        }
    }
    //end methods used for both creation and update

    //method for validating update only
    public function checkNote()
    {
        $note = Note::find($this->id);
        if(!$note)
        {
            $this->hasError = true;
            $this->error = response()->json(["message" => "Δεν υπάρχει η σημείωση ωστέ να γίνει ενημέρωση"],404);
        }
    }
    //end update only method

    public function storeNote()
    {
        $this->validateNote();
        if($this->hasError == true)
        {
            return $this->error;
        }
        $this->checkUser();
        if($this->hasError == true)
        {
            return $this->error;
        }
        $this->checkImportance();
        if($this->hasError == true)
        {
            return $this->error;
        }

        $this->request->request->add(['user_id' => $this->request->user()->id]);

        $note = Note::create($this->request->all());
        Calendar::create(["name"=>"Σημείωση","type"=>"notes" ,"note_id" => $note->id]);

        return response()->json(["message" => "Η σημείωση καταχωρήθηκε επιτυχώς!"],200);
    }



    public function updateNote()
    {
        $this->validateNote();
        if($this->hasError == true)
        {
            return $this->error;
        }
        $this->checkImportance();
        if($this->hasError == true)
        {
            return $this->error;
        }
        $this->checkNote();
        if($this->hasError == true)
        {
            return $this->error;
        }

        $this->request->request->add(['updated_by' => $this->request->user()->id]);
        $note = Note::find($this->id);
        $note->update($this->request->all());

        //End Calendar Events
        return response()->json(["message" => "Η σημείωση ενημερώθηκε επιτυχώς"],200);

    }
}











