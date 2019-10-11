<?php

namespace App\Http\CustomClasses\v1;
use App\Service;
use Validator;
use App\Device;
use App\Http\Resources\ServiceResource;
use Illuminate\Http\Request;
use App\Client;
use App\ServiceType;
use App\Eventt;
use App\UsersRoles;
use App\Calendar;

class ServiceManagement
{
    protected $request;
    protected $hasError = false;
    protected $error;
    protected $message;
    protected $service;
    protected $serviceInput;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public static function getServices()
    {
        $services = ServiceResource::collection(Service::where('status','Μη Ολοκληρωμένο')->get());
        return $services;
    }

    public static function getServicesHistory()
    {
        $services = ServiceResource::collection(Service::where('status','!=','Μη Ολοκληρωμένο')->orderBy('created_at','DESC')->get());
        return $services;
    }

    private function checkServiceType()
    {
        $serviceType = ServiceType::where('id',$this->request->service_type_id)->first();
        if(!$serviceType)
        {
            $this->hasError = true;
            $this->error = response()->json(["message" => "Δεν βρέθηκε ο συγκεκριμένος τύπος service!"],404);
        }
    }

    public function checkFrequency()
    {
        if($this->request->repeatable == false && $this->request->frequency == null)
        {
            $this->hasError = true;
            $this->error = response()->json(["message" => "Η συχνότητα δεν πρέπει να είναι κενή"],422);
        }
    }

    protected function validatorCreate()
    {
        $validator = Validator::make($this->request->all(),
        [
            'service_type_id' => 'required|integer',
            'service_comments' => 'nullable|min:4|max:10000',
            'cost' => 'nullable|numeric|between:0.00,999999.99',
            'guarantee' => 'required|boolean',
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
            'frequency' => 'nullable|string'
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
            'service_type_id' => 'required|integer',
            'service_comments' => 'nullable|min:4|max:10000',
            'cost' => 'nullable|numeric|between:0.00,999999.99',
            'guarantee' => 'required|boolean',
            'status' => 'required|string',
            'appointment_pending' => 'required|boolean',
            'technician_left' => 'required|boolean',
            'technician_arrived' => 'required|boolean',
            'appointment_completed' => 'required|boolean',
            'appointment_needed' => 'required|boolean',
            'supplement_pending' => 'required|boolean',
            'service_done' => 'required|boolean',
            'completed_no_transaction' => 'required|boolean',
            'client_id' => 'required|integer',
            'device_id' => 'required|integer',
            'comments' => 'nullable|min:4|max:100000',
            'manufacturer_id' => 'required|integer',
            'mark_id' => 'required|integer',
            'supplement' => 'nullable|string',
            'appointment_start' => 'nullable|string',
            'appointment_end' => 'nullable|string',
            'user_id' => 'nullable|integer',
            'repeatable' => 'required|boolean',
            'frequency' => 'nullable|string'
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

    public function storeService()
    {
        $this->validatorCreate();
        if($this->hasError == true)
        {
            return $this->error;
        }
        $this->checkStatus();
        if($this->hasError == true)
        {
            return $this->error;
        }
        $this->checkServiceType();
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
        $this->checkFrequency();
        if($this->hasError == true)
        {
            return $this->error;
        }
        if($this->request->cost == null)
        {

            $this->request->merge(['cost' => 0.00]);
        }
        $service = Service::create($this->request->all());

        $calendar = Calendar::create(["type"=>"service" ,"service_id" => $service->id]);

        return response()->json(["message" => "Το service καταχωρήθηκε επιτυχως!"],200);
    }

    public function checkService()
    {
        $service = Service::where('id',$this->request->id)->first();
        if(!$service)
        {
            $this->hasError = true;
            $this->error = response()->json(["message" => "To service αυτο δεν είναι περασμένη στο σύστημα!"],404);
        }
        else
        {
            $this->service = $service;
        }
    }

    public function checkStatus()
    {
        if($this->request->status != "Ολοκληρωμένο" && $this->request->status != "Μη Ολοκληρωμένο" && $this->request->status != "Ακυρώθηκε")
        {
            $this->hasError = true;
            $this->error = response()->json(["message" => "Η κατάσταση του service δεν επιτρέπεται!"],422);
        }
    }

    public function updateService()
    {
        $this->validatorUpdate();
        if($this->hasError == true)
        {
            return $this->error;
        }
        $this->checkStatus();
        if($this->hasError == true)
        {
            return $this->error;
        }
        $this->checkServiceType();
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
        $this->checkService();

        if($this->hasError == true)
        {
            return $this->error;
        }
        $this->checkTechnician();
        if($this->hasError == true)
        {
            return $this->error;
        }
        $this->checkFrequency();
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
        $this->service->update($this->input);


        //Calendar Events
        $calendar = Calendar::where('service_id',$this->service->id)->first();

        if($this->service->status != "Μη Ολοκληρωμένο" && $calendar)$calendar->delete();
        //if($this->service->status != "Ολοκληρωμένο" && $this->repeatable->status == false && $calendar)$calendar->delete();
        if($this->service->status == "Μη Ολοκληρωμένο" && !$calendar)Calendar::create(['type'=>'service','service_id' => $this->service->id]);

        return response()->json(["message" => "Τα στοίχεια του service με κωδικό ".$this->request->id." ενημερώθηκαν επιτυχώς!"],200);
    }

    public function createUpdateInput()
    {
        if(($this->request->appointment_pending == 0 && $this->request->technician_left == 1 && $this->request->technician_arrived == 1 && $this->request->appointment_completed == 1 && $this->request->appointment_needed == 0 && $this->request->service_done == 1 && $this->request->supplement_pending == 0 && $this->request->completed_no_transaction == 0) || $this->request->status == "Ολοκληρωμένo")
        {
            $this->input = array();
            $this->input =
            [
                "service_type" => $this->request->service_type,
                "service_comments" => $this->request->service_comments,
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
                "service_done" => true,
                "client_id" => $this->request->client_id,
                "device_id" => $this->request->device_id,
                "comments" => $this->request->comments,
                "manufacturer_id" => $this->request->manufacturer_id,
                "mark_id" => $this->request->mark_id,
                "supplement" => $this->request->supplement,
                "appointment_start" => $this->request->appointment_start,
                "appointment_end" => $this->request->appointment_end,
                "user_id" => $this->request->user_id,
                "repeatable" => $this->request->repeatable,
                "frequency" => $this->request->frequency
            ];
        }
        elseif($this->request->completed_no_transaction == 0 || $this->request->status == "Ακυρώθηκε")
        {
            $this->input = array();
            $this->input =
            [
                "service_type" => $this->request->service_type,
                "service_comments" => $this->request->service_comments,
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
                "service_done" => false,
                "client_id" => $this->request->client_id,
                "device_id" => $this->request->device_id,
                "comments" => $this->request->comments,
                "manufacturer_id" => $this->request->manufacturer_id,
                "mark_id" => $this->request->mark_id,
                "supplement" => $this->request->supplement,
                "appointment_start" => $this->request->appointment_start,
                "appointment_end" => $this->request->appointment_end,
                "user_id" => $this->request->user_id,
                "repeatable" => $this->request->repeatable,
                "frequency" => $this->request->frequency
            ];

        }
        elseif($this->request->completed_no_transaction == true && $this->request->service_done == true)
        {
            $this->hasError = true;
            $this->error = request()->json(["message" => "Η συναλλαγή δεν μπορεί να έιναι ακυρωμένη και επιδιορθωμένη!"],200);
        }
        else
        {
            $this->input = array();
            $this->input =
            [
                "service_type" => $this->request->service_type,
                "service_comments" => $this->request->service_comments,
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
                "service_done" => false,
                "client_id" => $this->request->client_id,
                "device_id" => $this->request->device_id,
                "comments" => $this->request->comments,
                "manufacturer_id" => $this->request->manufacturer_id,
                "mark_id" => $this->request->mark_id,
                "supplement" => $this->request->supplement,
                "appointment_start" => $this->request->appointment_start,
                "appointment_end" => $this->request->appointment_end,
                "user_id" => $this->request->user_id,
                "repeatable" => $this->repeatable,
                "frequency" => $this->frequency
            ];
        }
    }


}











