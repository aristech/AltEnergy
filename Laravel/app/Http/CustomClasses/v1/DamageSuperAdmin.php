<?php

namespace App\Http\CustomClasses\v1;
use App\Damage;
use Validator;
use App\Device;
use App\Http\Resources\DamageResource;
use Illuminate\Http\Request;
use App\Client;
use App\DamageType;
use App\Eventt;
use App\UsersRoles;
use App\Calendar;

class DamageSuperAdmin
{
    protected $request;
    protected $hasError = false;
    protected $error;
    protected $message;
    protected $damage;
    protected $damageInput;
    protected $techs;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public static function getDamages()
    {
        $damages = DamageResource::collection(Damage::where('status','Μη Ολοκληρωμένη')->get());
        return $damages;
    }

    public function insertTechs()
    {
        if(count($this->request->techs) != 0)
        {
            $tech_array = array();
            foreach($this->request->techs as $technician)
            {
                array_push($tech_array,$technician);//if all goes south $technician['tech_id]
            }
            $techs = implode(',',$tech_array);

            return $techs;
        }
        else
        {
            return null;
        }

    }

    public static function getDamagesHistory()
    {
        $damages = DamageResource::collection(Damage::where('status','Ολοκληρωμένη')->orWhere('status','Ακυρώθηκε')->orderBy('created_at','DESC')->get());
        return $damages;
    }

    private function checkDamageType()
    {
        $damageType = DamageType::where('id',$this->request->damage_type_id)->first();
        if(!$damageType)
        {
            $this->hasError = true;
            $this->error = response()->json(["message" => "Δεν βρέθηκε ο συγκεκριμένος τύπος βλάβης!"],404);
        }
    }

    protected function validatorCreate()
    {
        $validator = Validator::make($this->request->all(),
        [
            'damage_type_id' => 'required|integer',
            'damage_comments' => 'nullable|min:4|max:10000',
            'cost' => 'nullable|numeric|between:0.00,999999.99',
            'guarantee' => 'required|boolean',
            'status' => 'required|string',
            'client_id' => 'required|integer',
            'device_id' => 'required|integer',
            'comments' => 'nullable|min:4|max:100000',
            'manufacturer_id' => 'required|integer',
            'mark_id' => 'required|integer',
            'appointment_start' => 'nullable|string',
            'appointment_end' => 'nullable|string'
            // 'user_id' => 'nullable|integer'

        ]);

        if($validator->fails())
        {
            $this->hasError = true;
            $this->error = response()->json(["message" => $validator->errors()->first()],422);
            return response()->json(["message" => $validator->errors()->first()],422);
        }

    }

    protected function validatorUpdate()
    {
        $validator = Validator::make($this->request->all(),
        [
            'id' => 'required|integer',
            'damage_type_id' => 'required|integer',
            'damage_comments' => 'nullable|min:4|max:10000',
            'cost' => 'nullable|numeric|between:0.00,999999.99',
            'guarantee' => 'required|boolean',
            'status' => 'required|string',
            'appointment_pending' => 'required|boolean',
            'technician_left' => 'required|boolean',
            'technician_arrived' => 'required|boolean',
            'appointment_completed' => 'required|boolean',
            'appointment_needed' => 'required|boolean',
            'supplement_pending' => 'required|boolean',
            'damage_fixed' => 'required|boolean',
            'completed_no_transaction' => 'required|boolean',
            'client_id' => 'required|integer',
            'device_id' => 'required|integer',
            'comments' => 'nullable|min:4|max:100000',
            'manufacturer_id' => 'required|integer',
            'mark_id' => 'required|integer',
            'supplement' => 'nullable|string',
            'appointment_start' => 'nullable|string',
            'appointment_end' => 'nullable|string',
            // 'user_id' => 'nullable|integer'
        ]);

        if($validator->fails())
        {
            $this->hasError = true;
            $this->error = response()->json(["message" => $validator->errors()->first()],422);
        }
    }

    public function checkDevice()
    {
        $manufacturer_id = $this->request->manufacturer_id;
        $mark_id = $this->request->mark_id;
        $device_id = $this->request->device_id;

        $device = Device::whereHas('mark', function($query) use($mark_id)
        {
            $query->where('id',$mark_id);

        })
        ->whereHas('mark.manufacturer', function($query) use ($manufacturer_id)
        {
            $query->where('id',$manufacturer_id);
        })
        ->where('id',$device_id)->first();

        if(!$device)
        {
            $this->hasErrors = true;
            $this->error = response()->json(["message"=>"Η συσκευή που εισάγατε δεν υπάρχει στο σύστημα. Βεβαιωθείτε ότι τα στοιχεία της συσκευης είναι σωστά!"],404);
        }

    }

    public function checkClient()
    {
        $client = Client::where('id',$this->request->client_id)->first();
        if(!$client)
        {
            $this->hasErrors = true;
            $this->error = response()->json(["message"=>"Ο πελάτης αυτός δεν υπάρχει στο σύστημα!"],404);
            return response()->json(["message"=>"Ο πελάτης αυτός δεν υπάρχει στο σύστημα!"],404);
        }
    }

    // public function checkDate()
    // {
    //     if($this->request->appointment_start != null)
    //     {
    //         $string = strtotime($this->request->appointment_start);
    //         $date = date('d/M/Y h:i:s', $string);

    //         return $date;

    //         $string = $this->request->appointment_start;
    //         if (\DateTime::createFromFormat('Y-m-d H:i:s', $string) == false)
    //         {
    //             $this->hasError = true;
    //             $this->error = response()->json(["message" => "Η ημερομηνία έναρξης δεν είναι έγκυρη!"],422);
    //         }
    //     }

    //     if($this->request->appointment_end != null)
    //     {
    //         $string2 = $this->request->appointment_end;
    //         if (\DateTime::createFromFormat('Y-m-d H:i:s', $string2) == false)
    //         {
    //             $this->hasError = 'true';
    //             $this->error = response()->json(["message" => "Η ημερομηνία λήξης δεν είναι έγκυρη!"],422);
    //         }
    //     }

    //     if($this->request->appointment_start != null && $this->request->appointment_end != null)
    //     {
    //         if($string2 < $string)
    //         {
    //             $this->hasError = 'true';
    //             $this->error = response()->json(["message" => "Η ημερομηνία λήξης πρέπει να είναι μεγαλύτερη της ημερομηνίας έναρξης!"],422);
    //         }
    //     }
    // }

    public function checkTechnician()
    {
        if(count($this->request->techs) != 0)
        {
            foreach($this->request->techs as $tech)
            {
                $tech = UsersRoles::where('user_id',$tech)->where('role_id','3')->first();
                if(!$tech)
                {
                    $this->hasError = true;
                    $this->error = response()->json(["message" => "Το πρόσωπο με κωδικό ".$tech." δεν είναι τεχνικός!"],405);
                    break;
                }
            }
        }
    }

    public function storeDamage()
    {
        //return $this->insertTechs();
        $this->validatorCreate();
        if($this->hasError == true)
        {
            return $this->error;
        }
        $this->checkDamageType();
        if($this->hasError == true)
        {
            return $this->error;
        }
        $this->checkDevice();
        if($this->hasError == true)
        {
            return $this->error;
        }
        $this->checkClient();

        if($this->hasError == true)
        {
            return $this->error;
        }
        // $this->checkDate();

        // if($this->hasError == true)
        // {
        //     return $this->error;
        // }

        $this->checkTechnician();
        if($this->hasError == true)
        {
            return $this->error;
        }

        if($this->request->cost == null)
        {

            $this->request->merge(['cost' => 0.00]);
        }

        $techs = $this->insertTechs();
        $this->request->merge(['techs'=>$techs]);

        $damage = Damage::create($this->request->all());
        //Calendar Management
        if($damage->status == "Μη Ολοκληρωμένη")Calendar::create(['name'=>'βλάβη','type'=>'damages','damage_id'=>$damage->id]);
        //End Calendar management

        // if($this->request->appointment_start != null)
        // {
        //     // $client = Client::where('id',$this->request->client_id)->first();
        //     // Eventt::create(["event_type" => "damage", "event_id" => $damage->id]);
        // }


        return response()->json(["message" => "Η βλάβη του πελάτη καταχωρήθηκε επιτυχως!"],200);
    }

    public function checkDamage()
    {
        $damage = Damage::where('id',$this->request->id)->first();
        if(!$damage)
        {
            $this->hasError = true;
            $this->error = response()->json(["message" => "Η βλάβη αυτή δεν είναι περασμένη στο σύστημα!"],404);
        }
        else
        {
            $this->damage = $damage;
        }
    }

    public function updateDamage()
    {
        $this->validatorUpdate();
        if($this->hasError == true)
        {
            return $this->error;
        }
        $this->checkDamageType();
        if($this->hasError == true)
        {
            return $this->error;
        }
        $this->checkDevice();
        if($this->hasError == true)
        {
            return $this->error;
        }
        $this->checkClient();
        if($this->hasError == true)
        {
            return $this->error;
        }
        $this->checkDamage();

        if($this->hasError == true)
        {
            return $this->error;
        }
        $this->checkTechnician();
        if($this->hasError == true)
        {
            return $this->error;
        }
        $this->createUpdateInput();
        if($this->hasError == true)
        {
            return $this->error;
        }

        if($this->input['cost'] == null)
        {
            $this->input['cost'] = 0.00;
        }
        $this->damage->update($this->input);
        //Calendar for update
        $calendar = Calendar::where('damage_id',$this->damage->id)->first();

        if($this->damage->status != "Μη Ολοκληρωμένη")Calendar::where('damage_id',$this->damage->id)->first()->delete();
        if($this->damage->status == "Μη Ολοκληρωμένη" && !$calendar)Calendar::create(['type'=>'damages','name'=>'βλάβη','damage_id'=>$this->damage->id]);
        //End Calendar update process
        return response()->json(["message" => "Τα στοίχεια της βλάβης με κωδικό ".$this->request->id." ενημερώθηκαν επιτυχώς!"],200);
    }

    public function createUpdateInput()
    {
        if(($this->request->appointment_pending == 0 && $this->request->technician_left == 1 && $this->request->technician_arrived == 1 && $this->request->appointment_completed == 1 && $this->request->appointment_needed == 0 && $this->request->damage_fixed == 1 && $this->request->supplement_pending == 0 && $this->request->completed_no_transaction == 0 && $this->request->damage_fixed == 1) || $this->request->status == "Ολοκληρωμένη")
        {


            $this->input = array();
            $this->input =
            [
                "damage_type" => $this->request->damage_type,
                "damage_comments" => $this->request->damage_comments,
                "cost" => $this->request->cost,
                "guarantee" => $this->request->guarantee,
                "status" => "Ολοκληρωμένη",
                "appointment_pending" => false,
                "technician_left" => true,
                "technician_arrived" => true,
                "appointment_completed" => true,
                "appointment_needed" => false,
                "supplement_pending" => false,
                "completed_no_transaction" => false,
                "damage_fixed" => true,
                "client_id" => $this->request->client_id,
                "device_id" => $this->request->device_id,
                "comments" => $this->request->comments,
                "manufacturer_id" => $this->request->manufacturer_id,
                "mark_id" => $this->request->mark_id,
                "supplement" => $this->request->supplement,
                "appointment_start" => $this->request->appointment_start,
                "appointment_end" => $this->request->appointment_end,
                //"user_id" => $this->request->user_id,
                "techs" => $this->insertTechs()
            ];
        }
        elseif($this->request->completed_no_transaction == 0 || $this->request->status == "Ακυρώθηκε")
        {
            $this->input = array();
            $this->input =
            [
                "damage_type" => $this->request->damage_type,
                "damage_comments" => $this->request->damage_comments,
                "cost" => $this->request->cost,
                "guarantee" => $this->request->guarantee,
                "status" => "Ακυρώθηκε",
                "appointment_pending" => $this->request->appointment_pending,
                "technician_left" => $this->request->technician_left,
                "technician_arrived" => $this->request->technician_arrived,
                "appointment_completed" => $this->request->appointment_completed,
                "appointment_needed" => $this->request->appointment_needed,
                "supplement_pending" => $this->request->supplement_pending,
                "completed_no_transaction" => true,
                "damage_fixed" => false,
                "client_id" => $this->request->client_id,
                "device_id" => $this->request->device_id,
                "comments" => $this->request->comments,
                "manufacturer_id" => $this->request->manufacturer_id,
                "mark_id" => $this->request->mark_id,
                "supplement" => $this->request->supplement,
                "appointment_start" => $this->request->appointment_start,
                "appointment_end" => $this->request->appointment_end,
                //"user_id" => $this->request->user_id
                "techs" => $this->insertTechs()
            ];

        }
        elseif($this->request->completed_no_transaction == true && $this->request->damage_fixed == true)
        {
            $this->hasError = true;
            $this->error = request()->json(["message" => "Η συναλλαγή δεν μπορεί να έιναι ακυρωμένη και επιδιορθωμένη!"],200);
        }
        else
        {
            $this->input = array();
            $this->input =
            [
                "damage_type" => $this->request->damage_type,
                "damage_comments" => $this->request->damage_comments,
                "cost" => $this->request->cost,
                "guarantee" => $this->request->guarantee,
                "status" => $this->status,
                "appointment_pending" => $this->request->appointment_pending,
                "technician_left" => $this->request->technician_left,
                "technician_arrived" => $this->request->technician_arrived,
                "appointment_completed" => $this->appointment_completed,
                "appointment_needed" => $this->appointment_needed,
                "supplement_pending" => $this->supplement_pending,
                "completed_no_transaction" => $this->completed_no_transca,
                "damage_fixed" => false,
                "client_id" => $this->request->client_id,
                "device_id" => $this->request->device_id,
                "comments" => $this->request->comments,
                "manufacturer_id" => $this->request->manufacturer_id,
                "mark_id" => $this->request->mark_id,
                "supplement" => $this->request->supplement,
                "appointment_start" => $this->request->appointment_start,
                "appointment_end" => $this->request->appointment_end,
                "user_id" => $this->request->user_id
            ];
        }
    }


}











