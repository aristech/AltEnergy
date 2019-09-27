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

class DamageSuperAdmin
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

    public static function getDamages()
    {
        $damages = DamageResource::collection(Damage::all());
        return $damages;
    }

    protected function validatorCreate()
    {
        $validator = Validator::make($this->request->all(),
        [
            'damage_type_id' => 'required|integer',
            'damage_comments' => 'nullable|min:4|max:10000',
            'cost' => 'nullable|numeric|between:0.00,999999.99',
            'guarantee' => 'nullable|boolean',
            'status' => 'required|string',
            'client_id' => 'required|integer',
            'device_id' => 'required|integer',
            'comments' => 'nullable|min:4|max:100000',
            'manufacturer_id' => 'required|integer',
            'mark_id' => 'required|integer',
            'appointment_start' => 'nullable|string',
            'appointment_end' => 'nullable|string',
            'user_id' => 'nullable|integer',
            'repeatable' => 'required|boolean',
            'repeat_frequency' => 'nullable|integer',
            'repeat_type' => 'string|nullable'

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
            'estimation_appointment' => 'required|boolean',
            'cost_information' => 'required|boolean',
            'supplement_available' => 'required|boolean',
            'fixing_appointment' => 'required|boolean',
            'damage_fixed' => 'required|boolean',
            'damage_paid' => 'required|boolean',
            'client_id' => 'required|integer',
            'device_id' => 'required|integer',
            'comments' => 'nullable|min:4|max:100000',
            'manufacturer_id' => 'required|integer',
            'mark_id' => 'required|integer',
            'supplement' => 'nullable|string',
            'repeatable' => 'required|boolean',
            'repeat_frequency' => 'nullable|integer',
            'repeat_type' => 'string|nullable'

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
            $this->error = response()->json(["message"=>"Η συσκευή που εισάγατε δεν υπάρχει στο σύστημα. Βεβαιωθείτε ότι τα στοιχεία της συσκευης είναι σωστά!"],422);
            return response()->json(["message"=>"Η συσκευή που εισάγατε δεν υπάρχει στο σύστημα. Βεβαιωθείτε ότι τα στοιχεία της συσκευης είναι σωστά!"],422);
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

    public function checkDate()
    {
        if($this->request->appointment_start != null)
        {
            $string = $this->request->appointment_start;
            if (\DateTime::createFromFormat('Y-m-d H:i:s', $string) == false)
            {
                $this->hasError = true;
                $this->error = response()->json(["message" => "Η ημερομηνία έναρξης δεν είναι έγκυρη!"],422);
            }
        }

        if($this->request->appointment_end != null)
        {
            $string = $this->request->appointment_end;
            if (\DateTime::createFromFormat('Y-m-d H:i:s', $string) == false)
            {
                $this->hasError = 'true';
                $this->error = response()->json(["message" => "Η ημερομηνία λήξης δεν είναι έγκυρη!"],422);
            }
        }
    }

    public function checkTechnician()
    {
        if($this->request->user_id != null)
        {
            $tech_id = $this->request->user_id;
            $tech = UsersRoles::where('user_id',$tech_id)->where('role_id','3')->first();
            if(!$tech)
            {
                $this->hasError = true;
                $this->error = response()->json(["message" => "Το συγκεκριμένο πρόσωπο δεν είναι τεχνικός!"],405);
            }
        }
    }

    public function storeDamage()
    {
        $this->validatorCreate();

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
        $this->checkDate();

        if($this->hasError == true)
        {
            return $this->error;
        }

        $this->checkTechnician();
        if($this->hasError == true)
        {
            return $this->error;
        }

        $damage = Damage::create($this->request->all());

        if($this->request->appointment_start != null)
        {
            $client = Client::where('id',$this->request->client_id)->first();
            Eventt::create(["event_type" => "damage", "event_id" => $damage->id]);
        }


        return response()->json(["message" => "Η ζημιά του πελάτη καταχωρήθηκε επιτυχως!"],200);
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

        if($this->hasError == false)
        {
            $this->createUpdateInput();
            $this->damage->update($this->input);
            return response()->json(["message" => "Τα στοίχεια της βλάβης με κωδικό ".$this->request->id." ενημερώθηκαν επιτυχώς!"],200);
        }
        else
        {
            return $this->error;
        }
    }

    public function createUpdateInput()
    {
        if(($this->request->estimation_appointment == 1 && $this->request->cost_information == 1 && $this->request->supplement_available == 1 && $this->request->fixing_appointment == 1 && $this->request->damage_fixed == 1 && $this->request->damage_paid == 1) || $this->request->status == "Ολοκληρωμένη")
        {
            $this->input = array();
            $this->input =
            [
                "damage_type" => $this->request->damage_type,
                "damage_comments" => $this->request->damage_comments,
                "cost" => $this->request->cost,
                "guarantee" => $this->request->guarantee,
                "status" => "Ολοκληρωμένη",
                "estimation_appointment" => true,
                "cost_information" => true,
                "supplement_available" => true,
                "fixing_appointment" => true,
                "damage_fixed" => true,
                "damage_paid" => true,
                "client_id" => $this->request->client_id,
                "device_id" => $this->request->device_id,
                "comments" => $this->request->comments,
                "manufacturer_id" => $this->request->manufacturer_id,
                "mark_id" => $this->request->mark_id,
                "supplement" => $this->request->supplement,
                "appointment_start" => $this->request->appointment_start,
                "appointment_end" => $this->request->appointment_end,
                "repeatable" => $this->request->repeatable,
                "repeat_frequency" => $this->request->repeat_frequency,
                "repeat_type" => $this->request->repeat_type
            ];
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
                "status" => $this->request->status,
                "estimation_appointment" => $this->request->estimation_appointment,
                "cost_information" => $this->request->cost_information,
                "supplement_available" => $this->request->supplement_available,
                "fixing_appointment" => $this->request->fixing_appointment,
                "damage_fixed" => $this->request->damage_fixed,
                "damage_paid" => $this->request->damage_paid,
                "client_id" => $this->request->client_id,
                "device_id" => $this->request->device_id,
                "comments" => $this->request->comments,
                "manufacturer_id" => $this->request->manufacturer_id,
                "mark_id" => $this->request->mark_id,
                "supplement" => $this->request->supplement,
                "repeatable" => $this->request->repeatable,
                "repeat_frequency" => $this->request->repeat_frequency,
                "repeat_type" => $this->request->repeat_type
            ];

        }
    }


}











