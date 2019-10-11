<?php

namespace App\Http\CustomClasses\v1;
use App\Damage;
use Validator;
use App\Device;
use App\Http\Resources\DamageResource;
use Illuminate\Http\Request;
use App\Eventt;
use App\Calendar;

class EventMod
{
    protected $request;
    protected $hasError = false;
    protected $error;
    protected $message;
    protected $damage;
    protected $damageInput;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function validatorCreate()
    {
        $validator = Validator::make($this->request->all(),
        [
            'title' => 'required|string',
            'status' => 'required|string',
            'description' => 'nullable|min:4|max:10000',
            'comments' => 'nullable|min:4|max:10000'
        ]);

        if($validator->fails())
        {
            $this->hasError = true;
            $this->error = response()->json(["message" => $validator->errors()->first()],422);
        }
    }

    protected function validatorUpdate()
    {
        $validator = Validator::make($this->request->all(),
        [
            'id' => 'required|integer',
            'title' => 'required|string',
            'status' => 'required|string',
            'description' => 'nullable|min:4|max:10000',
            'comments' => 'nullable|min:4|max:10000'
        ]);

        if($validator->fails())
        {
            $this->hasError = true;
            $this->error = response()->json(["message" => $validator->errors()->first()],422);
        }
    }

    public function checkEvent()
    {
        $event = Eventt::find($this->request->id);
        if(!$event)
        {
            $this->hasError = true;
            $this->error = response()->json(["message" => "Δεν υπάρχει το καταχωρημένο το event που αναζητείτε"],404);
        }
    }

    public function checkDate()
    {
        if($this->request->event_start != null)
        {
            $string = $this->request->event_start;
            if (\DateTime::createFromFormat('Y-m-d H:i:s', $string) == false)
            {
                $this->hasError = true;
                $this->error = response()->json(["message" => "Η ημερομηνία έναρξης δεν είναι έγκυρη!"],422);
            }
        }

        if($this->request->event_end != null)
        {
            $string2 = $this->request->event_end;
            if (\DateTime::createFromFormat('Y-m-d H:i:s', $string2) == false)
            {
                $this->hasError = 'true';
                $this->error = response()->json(["message" => "Η ημερομηνία λήξης δεν είναι έγκυρη!"],422);
            }
        }

        if($this->request->event_start != null && $this->request->event_end != null)
        {
            if($string2 < $string)
            {
                $this->hasError = 'true';
                $this->error = response()->json(["message" => "Η ημερομηνία λήξης πρέπει να είναι μεγαλύτερη της ημερομηνίας έναρξης!"],422);
            }
        }
    }

    public function checkStatus()
    {
        if($this->request->status != "Ολοκληρωμένο" && $this->request->status != "Μη Ολοκληρωμένο" && $this->request->status != "Ακυρώθηκε")
        {
            $this->hasError = 'true';
            $this->error = response()->json(["message" => "Η κατάσταση δεν είναι έγκυρη!"],422);
        }
    }

    public function storeEvent()
    {
        $this->validatorCreate();
        if($this->hasError == true)
        {
            return $this->error;
        }
        $this->checkDate();
        if($this->hasError == true)
        {
            return $this->error;
        }
        $this->checkStatus();
        if($this->hasError == true)
        {
            return $this->error;
        }
        $event = Eventt::create($this->request->all());
        Calendar::create(["type"=>"event" ,"event_id" => $event->id]);

        return response()->json(["message" => "Το event καταχωρήθηκε επιτυχώς!"],200);
    }

    public function updateEvent()
    {
        $this->validatorUpdate();
        if($this->hasError == true)
        {
            return $this->error;
        }
        $this->checkDate();
        if($this->hasError == true)
        {
            return $this->error;
        }
        $this->checkEvent();
        if($this->hasError == true)
        {
            return $this->error;
        }
        $this->checkStatus();
        if($this->hasError == true)
        {
            return $this->error;
        }

        $event = Eventt::find($this->request->id);
        $event->update($this->request->except(["id"]));
        //Calendar Events
        $calendar = Calendar::where('event_id',$event->id)->first();

        if($event->status != "Μη Ολοκληρωμένο" && $calendar)$calendar->delete();
        if($event->status == "Μη Ολοκληρωμένο" && !$calendar)Calendar::create(['type'=>'event','event_id' => $event->id]);
        //End Calendar Events
        return response()->json(["message" => "Το event ενημερώθηκε επιτυχώς"],200);

    }
}











