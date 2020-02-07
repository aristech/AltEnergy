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
use App\Http\CustomClasses\v1\TechMail;

use App\Mark;

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
        $damages = DamageResource::collection(Damage::orderBy('appointment_start', 'asc')->get()); //Damage::where('status','Μη Ολοκληρωμένη')->get()
        return $damages;
    }

    public function insertTechs()
    {
        if (count($this->request->techs) != 0) {
            $tech_array = array();
            foreach ($this->request->techs as $technician) {
                if (is_int($technician)) {
                    array_push($tech_array, $technician);
                } else {
                    array_push($tech_array, $technician['id']); //if all goes south $technician['tech_id]
                }
            }
            $techs = implode(',', $tech_array);

            return $techs;
        } else {
            return null;
        }
    }

    public function insertMarks()
    {
        if (count($this->request->marks) != 0) {
            $mark_array = array();
            foreach ($this->request->marks as $mark) {
                array_push($mark_array, $mark); //if all goes south $technician['tech_id]
            }
            $marks = implode(',', $mark_array);

            return $marks;
        } else {
            return null;
        }

        $marks = implode(',', $this->request->marks);
        $this->request->merge(['marks' => $marks]);
    }

    public static function getDamagesHistory()
    {
        $damages = DamageResource::collection(Damage::where('status', 'Ολοκληρώθηκε')->orWhere('status', 'Ακυρώθηκε')->orderBy('created_at', 'DESC')->get());
        return $damages;
    }

    private function checkDamageType()
    {
        $damageType = DamageType::where('id', $this->request->damage_type_id)->first();
        if (!$damageType) {
            $this->hasError = true;
            $this->error = response()->json(["message" => "Δεν βρέθηκε ο συγκεκριμένος τύπος βλάβης!"], 404);
        }
    }

    protected function validatorCreate()
    {
        $validator = Validator::make(
            $this->request->all(),
            [
                'damage_type_id' => 'required|integer',
                'damage_comments' => 'nullable',
                'cost' => 'nullable|numeric|between:0.00,999999.99',
                'guarantee' => 'required|boolean',
                //'status' => 'required|string',
                // 'client_id' => 'required|integer',
                // 'device_id' => 'required|integer',
                'comments' => 'nullable',
                // 'manufacturer_id' => 'required|integer',
                //'mark_id' => 'required|integer',
                'appointment_start' => 'nullable|string',
                'appointment_end' => 'nullable|string',
                'manager_payment' => 'nullable|numeric|between:0.00,999999.99',
                // 'user_id' => 'nullable|integer'

            ]
        );

        if ($validator->fails()) {
            $this->hasError = true;
            $this->error = response()->json(["message" => $validator->errors()->first()], 422);
            return response()->json(["message" => $validator->errors()->first()], 422);
        }
    }

    protected function validatorUpdate()
    {
        $validator = Validator::make(
            $this->request->all(),
            [
                'id' => 'required|integer',
                'damage_type_id' => 'required|integer',
                'damage_comments' => 'nullable',
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
                //'device_id' => 'required|integer',
                'comments' => 'nullable',
                //'manufacturer_id' => 'required|integer',
                //'mark_id' => 'required|integer',
                'supplement' => 'nullable|string',
                'appointment_start' => 'nullable|string',
                'appointment_end' => 'nullable|string',
                'manager_payment' => 'nullable|numeric|between:0.00,999999.99',
            ]
        );

        if ($validator->fails()) {
            $this->hasError = true;
            $this->error = response()->json(["message" => $validator->errors()->first()], 422);
        }
    }

    public function checkDevice()
    {
        // $manufacturer_id = $this->request->manufacturer_id;
        // $mark_id = $this->request->mark_id;
        // $device_id = $this->request->device_id;

        // $device = Device::whereHas('mark', function ($query) use ($mark_id) {
        //     $query->where('id', $mark_id);
        // })
        //     ->whereHas('mark.manufacturer', function ($query) use ($manufacturer_id) {
        //         $query->where('id', $manufacturer_id);
        //     })
        //     ->where('id', $device_id)->first();

        // if (!$device) {
        //     $this->hasErrors = true;
        //     $this->error = response()->json(["message" => "Η συσκευή που εισάγατε δεν υπάρχει στο σύστημα. Βεβαιωθείτε ότι τα στοιχεία της συσκευης είναι σωστά!"], 404);
        // }

        $marks = $this->request->marks;
        foreach ($marks as $mark) {
            $check_mark = Mark::where('id', $mark)->first();
            if (!$check_mark) {
                $this->hasError = true;
                $this->error = response()->json(["message" => "Μια ή περισσότερες επισκευές δεν υπάρχουν στο σύστημα"], 404);
            }
        }
    }

    public function checkClient()
    {
        $client = Client::where('id', $this->request->client_id)->first();
        if (!$client) {
            $this->hasErrors = true;
            $this->error = response()->json(["message" => "Ο πελάτης αυτός δεν υπάρχει στο σύστημα!"], 404);
            //return response()->json(["message" => "Ο πελάτης αυτός δεν υπάρχει στο σύστημα!"], 404);
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
        if (count($this->request->techs) != 0) {
            foreach ($this->request->techs as $techn) {
                if (is_int($techn)) {
                    $tech = UsersRoles::where('user_id', $techn)->where('role_id', '3')->first();
                } else {
                    $tech = UsersRoles::where('user_id', $techn['id'])->where('role_id', '3')->first();
                }
                if (!$tech) {
                    $this->hasError = true;
                    //$this->error = response()->json(["message" => "Παρακαλώ ελεγξτε πάλι τους τεχνικούς"], 405);
                    $this->error = $this->request->techs;
                    break;
                }
            }
        }
    }

    public function storeDamage()
    {
        //return $this->insertTechs();
        $this->validatorCreate();
        if ($this->hasError == true) {
            return $this->error;
        }
        $this->checkDamageType();
        if ($this->hasError == true) {
            return $this->error;
        }
        $this->checkDevice();
        if ($this->hasError == true) {
            return $this->error;
        }
        $this->checkClient();

        if ($this->hasError == true) {
            return $this->error;
        }
        // $this->checkDate();

        // if($this->hasError == true)
        // {
        //     return $this->error;
        // }

        $this->checkTechnician();
        if ($this->hasError == true) {
            return $this->error;
        }

        if ($this->request->cost == null) {

            $this->request->merge(['cost' => 0.00]);
        }

        $techs = $this->insertTechs();
        $this->request->merge(['techs' => $techs]);
        $this->request->request->add(['status' => 'Μη Ολοκληρωμένη']);

        $marks = implode(',', $this->request->marks);
        $this->request->merge(['marks' => $marks]);


        //if not appointment then set pending date
        if ($this->request->appointment_start == null) {
            $this->request->request->add(['appointment_pending' => true]);
            $this->request->appointment_start = null;
        }

        $damage = Damage::create($this->request->all());
        //Calendar Management
        //if ($damage->status == "Μη Ολοκληρωμένη")
        Calendar::create(['name' => 'βλάβη', 'type' => 'damages', 'damage_id' => $damage->id]); //inserted change if entry is not null
        //End Calendar management

        // if($this->request->appointment_start != null)
        // {
        //     // $client = Client::where('id',$this->request->client_id)->first();
        //     // Eventt::create(["event_type" => "damage", "event_id" => $damage->id]);
        // }
        $dmg = Damage::find($damage->id);
        TechMail::sendToTechs($dmg, "βλάβη", "new");

        return response()->json(["message" => "Η βλάβη του πελάτη καταχωρήθηκε επιτυχως!"], 200);
    }

    public function checkDamage()
    {
        $damage = Damage::where('id', $this->request->id)->first();
        if (!$damage) {
            $this->hasError = true;
            $this->error = response()->json(["message" => "Η βλάβη αυτή δεν είναι περασμένη στο σύστημα!"], 404);
        } else {
            $this->damage = $damage;
        }
    }

    public function updateDamage()
    {
        $this->validatorUpdate();
        if ($this->hasError == true) {
            return $this->error;
        }
        $this->checkDamageType();
        if ($this->hasError == true) {
            return $this->error;
        }
        $this->checkDevice();
        if ($this->hasError == true) {
            return $this->error;
        }
        $this->checkClient();
        if ($this->hasError == true) {
            return $this->error;
        }
        $this->checkDamage();

        if ($this->hasError == true) {
            return $this->error;
        }
        $this->checkTechnician();
        if ($this->hasError == true) {
            return $this->error;
        }
        $this->createUpdateInput();
        if ($this->hasError == true) {
            return $this->error;
        }

        if ($this->input['cost'] == null) {
            $this->input['cost'] = 0.00;
        }

        if ($this->input['manager_payment'] == null) {
            $this->input['manager_payment'] = 0.00;
        }

        if ($this->input['appointment_start'] == null && $this->input['status'] == "Μη Ολοκληρωμένη") {
            $this->input['appointment_pending'] = true;
            $this->input['appointment_end'] = null;
        } else {
            $this->input['appointment_pending'] = false;
        }

        //# Να δω αν σε περιπτωση π δεν εχει ξετικαρει το αναμονη ραντεβου θα ειναι ημ/νθια κενη ή οχι

        $this->damage->update($this->input);
        //Calendar for update
        $calendar = Calendar::where('damage_id', $this->damage->id)->first();

        // if ($this->damage->status != "Μη Ολοκληρωμένη" && $calendar) {
        //     $calendar->delete();
        // }
        if ($this->damage->status == "Μη Ολοκληρωμένη" && !$calendar) {
            Calendar::create(['type' => 'damages', 'name' => 'βλάβη', 'damage_id' => $this->damage->id]);
        }

        TechMail::sendToTechs($this->damage, "βλάβη", "update");
        //End Calendar update process
        return response()->json(["message" => "Τα στοίχεια της βλάβης με κωδικό " . $this->request->id . " ενημερώθηκαν επιτυχώς!"], 200);
    }

    public function createUpdateInput()
    {
        if ($this->request->completed_no_transaction == true && $this->request->damage_fixed == true) {
            $this->hasError = true;
            $this->error = request()->json(["message" => "Η συναλλαγή δεν μπορεί να έιναι ακυρωμένη και επιδιορθωμένη!"], 200);
        } elseif ($this->request->damage_fixed == true) // in case of problems insert  || $this->request->status == "Ολοκληρωμένη" in if statement
        {
            $this->input = array();
            $this->input =
                [
                    "damage_type" => $this->request->damage_type,
                    "damage_comments" => $this->request->damage_comments,
                    "cost" => $this->request->cost,
                    "guarantee" => $this->request->guarantee,
                    "status" => "Ολοκληρώθηκε",
                    "appointment_pending" => false,
                    "technician_left" => true,
                    "technician_arrived" => true,
                    "appointment_completed" => true,
                    "appointment_needed" => false,
                    "supplement_pending" => false,
                    "completed_no_transaction" => false,
                    "damage_fixed" => true,
                    "client_id" => $this->request->client_id,
                    //"device_id" => $this->request->device_id,
                    "comments" => $this->request->comments,
                    //"manufacturer_id" => $this->request->manufacturer_id,
                    //"mark_id" => $this->request->mark_id,
                    "supplement" => $this->request->supplement,
                    "appointment_start" => $this->request->appointment_start,
                    "appointment_end" => $this->request->appointment_end,
                    //"user_id" => $this->request->user_id,
                    "techs" => $this->insertTechs(),
                    "marks" => $this->insertMarks(),
                    "manager_payment" => $this->request->manager_payment
                ];
        } elseif ($this->request->completed_no_transaction == true) // || $this->request->status == "Ακυρώθηκε" insert that in if statement if problems occur
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
                    //"device_id" => $this->request->device_id,
                    "comments" => $this->request->comments,
                    //"manufacturer_id" => $this->request->manufacturer_id,
                    //"mark_id" => $this->request->mark_id,
                    "supplement" => $this->request->supplement,
                    "appointment_start" => $this->request->appointment_start,
                    "appointment_end" => $this->request->appointment_end,
                    //"user_id" => $this->request->user_id,
                    "techs" => $this->insertTechs(),
                    "marks" => $this->insertMarks(),
                    "manager_payment" => $this->request->manager_payment
                ];
        } else {
            $this->input = array();
            $this->input =
                [
                    "damage_type" => $this->request->damage_type,
                    "damage_comments" => $this->request->damage_comments,
                    "cost" => $this->request->cost,
                    "guarantee" => (int) $this->request->guarantee,
                    "status" => $this->request->status,
                    "appointment_pending" => (int) $this->request->appointment_pending,
                    "technician_left" => (int) $this->request->technician_left,
                    "technician_arrived" => (int) $this->request->technician_arrived,
                    "appointment_completed" => (int) $this->request->appointment_completed,
                    "appointment_needed" => (int) $this->request->appointment_needed,
                    "supplement_pending" => (int) $this->request->supplement_pending,
                    "completed_no_transaction" => (int) $this->request->completed_no_transaction,
                    "damage_fixed" => false,
                    "client_id" => $this->request->client_id,
                    //"device_id" => $this->request->device_id,
                    "comments" => $this->request->comments,
                    //"manufacturer_id" => $this->request->manufacturer_id,
                    //"mark_id" => $this->request->mark_id,
                    "supplement" => $this->request->supplement,
                    "appointment_start" => $this->request->appointment_start,
                    "appointment_end" => $this->request->appointment_end,
                    //"user_id" => $this->request->user_id,
                    "techs" => $this->insertTechs(),
                    "marks" => $this->insertMarks(),
                    "manager_payment" => $this->request->manager_payment
                ];
        }
    }
}
