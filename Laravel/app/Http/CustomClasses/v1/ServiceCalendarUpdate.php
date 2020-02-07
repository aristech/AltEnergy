<?php

namespace App\Http\CustomClasses\v1;

use App\Service;
use Validator;
use App\Device;
use App\Http\Resources\ServiceResource;
use Illuminate\Http\Request;
use App\Client;
use App\DamageType;
use App\UsersRoles;
use App\Calendar;
use App\Mark;
use App\Http\CustomClasses\v1\TechMail;

class ServiceCalendarUpdate
{
    protected $request;
    protected $hasError = false;
    protected $error;
    protected $message;
    protected $service;
    protected $serviceInput;
    protected $service_id;

    public function __construct(Request $request, $service_id)
    {
        $this->request = $request;
        $this->service_id = $service_id;
    }

    public static function getServices()
    {
        $services = ServiceResource::collection(Service::orderBy('appointment_start', 'asc')->get());
        return $services;
    }

    public static function getServicesHistory()
    {
        $services = ServiceResource::collection(Service::where('status', '!=', 'Μη Ολοκληρωμένο')->orderBy('created_at', 'DESC')->get());
        return $services;
    }

    public function insertTechs()
    {
        if (count($this->request->techs) != 0) {
            $tech_array = array();
            foreach ($this->request->techs as $technician) {
                array_push($tech_array, $technician['tech_id']);
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
                array_push($mark_array, $mark['id']); //if all goes south $technician['tech_id]
            }
            $marks = implode(',', $mark_array);

            return $marks;
        } else {
            return null;
        }

        $marks = implode(',', $this->request->marks);
        $this->request->merge(['marks' => $marks]);
    }

    private function checkServiceType()
    {
        $serviceType = DamageType::where('id', $this->request->service_type_id2)->first();
        if (!$serviceType) {
            $this->hasError = true;
            $this->error = response()->json(["message" => "Δεν βρέθηκε ο συγκεκριμένος τύπος service!"], 404);
        }
    }

    protected function validatorCreate()
    {
        $validator = Validator::make(
            $this->request->all(),
            [
                'service_type_id2' => 'required|integer',
                'service_comments' => 'nullable|min:4|max:10000',
                'cost' => 'nullable|numeric|between:0.00,999999.99',
                'guarantee' => 'required|boolean',
                'status' => 'required|string',
                'client_id' => 'required|integer',
                //'device_id' => 'required|integer',
                'comments' => 'nullable|min:4|max:100000',
                'manufacturer_id' => 'required|integer',
                //'mark_id' => 'required|integer',
                'appointment_start' => 'nullable|string',
                'appointment_end' => 'nullable|string',
                'user_id' => 'nullable|integer',
                'repeatable' => 'required|boolean',
                'frequency' => 'nullable|string',
                'manager_payment' => 'nullable|numeric|between:0.00,999999.99'
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
                'service_type_id2' => 'required|integer',
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
                'service_completed' => 'required|boolean',
                'completed_no_transaction' => 'required|boolean',
                'client_id' => 'required|integer',
                //'device_id' => 'required|integer',
                'comments' => 'nullable|min:4|max:100000',
                //'manufacturer_id' => 'required|integer',
                //'mark_id' => 'required|integer',
                'supplements' => 'nullable|string',
                'appointment_start' => 'nullable|string',
                'appointment_end' => 'nullable|string',
                'user_id' => 'nullable|integer',
                'repeatable' => 'required|boolean',
                'frequency' => 'nullable|string',
                'manager_payment' => 'nullable|numeric|between:0.00,999999.99'
            ]
        );

        if ($validator->fails()) {
            $this->hasError = true;
            $this->error = response()->json(["message" => $validator->errors()->first()], 422);
        }
    }

    public function checkDevice()
    {
        $marks = $this->request->marks;
        foreach ($marks as $mark) {
            $check_mark = Mark::where('id', $mark)->first();
            if (!$check_mark) {
                $this->hasError = true;
                $this->error = response()->json(["message" => "Μια ή περισσότερες συσκευές δεν υπάρχουν στο σύστημα"], 404);
            }
        }
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
    }

    public function checkClient()
    {
        $client = Client::where('id', $this->request->client_id)->first();
        if (!$client) {
            $this->hasErrors = true;
            $this->error = response()->json(["message" => "Ο πελάτης αυτός δεν υπάρχει στο σύστημα!"], 404);
            return response()->json(["message" => "Ο πελάτης αυτός δεν υπάρχει στο σύστημα!"], 404);
        }
    }

    public function checkFrequency()
    {
        if (($this->request->repeatable == false && $this->request->frequency != null) || ($this->request->repeatable == true && $this->request->frequency == null)) {
            $this->hasError;
            $this->error = response()->json(["message" => "Πρέπει να οριστεί και επαναλαμβανόμενο πεδίο και συχνότητα!"], 422);
        }
    }

    public function checkTechnician()
    {
        if (count($this->request->techs) != 0) {
            foreach ($this->request->techs as $techn) {
                $tech = UsersRoles::where('user_id', $techn['tech_id'])->where('role_id', '3')->first();
                if (!$tech) {
                    $this->hasError = true;
                    $this->error = response()->json(["message" => "Το πρόσωπο με κωδικό " . $techn . " δεν είναι τεχνικός!"], 405);
                    break;
                }
            }
        }
    }

    public function storeService()
    {
        $this->validatorCreate();
        if ($this->hasError == true) {
            return $this->error;
        }
        $this->checkStatus();
        if ($this->hasError == true) {
            return $this->error;
        }
        $this->checkServiceType();
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
        $this->checkTechnician();
        if ($this->hasError == true) {
            return $this->error;
        }
        $this->checkFrequency();
        if ($this->hasError == true) {
            return $this->error;
        }
        if ($this->request->cost == null) {

            $this->request->merge(['cost' => 0.00]);
        }

        $techs = $this->insertTechs();
        $this->request->merge(['techs' => $techs]);

        $marks = implode(",", $this->request->marks);
        $this->request->merge(['marks' => $marks]);

        if ($this->request->appointment_start == null) {
            $this->request->request->add(['appointment_pending', true]);
        }

        if ($this->request->repeatable == true && $this->request->appointment_start != null) {
            $newDateTime = strtotime($this->request->frequency, strtotime($this->request->appointment_start));
            $newDate = date('c', $newDateTime);
            $newDateArray = explode('+', $newDate);
            $newDate = $newDateArray[0] . ".000Z";
            $this->request->merge(['appointment_start' => $newDate]);
            $this->request->merge(['status' => 'Μη Ολοκληρωμένο']);
        }

        $service = Service::create($this->request->all());

        if ($service->appointment_start != null && $service->status == "Μη Ολοκληρωμένο") Calendar::create(["name" => "service", "type" => "services", "service_id" => $service->id]);

        TechMail::sendToTechs($service, "σέρβις", "new");
        return response()->json(["message" => "Το service καταχωρήθηκε επιτυχως!"], 200);
    }

    public function checkService()
    {
        $service = Service::where('id', $this->service_id)->first();
        if (!$service) {
            $this->hasError = true;
            $this->error = response()->json(["message" => "To service αυτο δεν είναι περασμένη στο σύστημα!"], 404);
        } else {
            $this->service = $service;
        }
    }

    public function checkStatus()
    {
        if ($this->request->status != "Ολοκληρώθηκε" && $this->request->status != "Μη Ολοκληρωμένο" && $this->request->status != "Ακυρώθηκε") {
            $this->hasError = true;
            $this->error = response()->json(["message" => "Η κατάσταση του service δεν επιτρέπεται!"], 422);
        }
    }

    public function updateService()
    {
        $this->validatorUpdate();
        if ($this->hasError == true) {
            return $this->error;
        }
        $this->checkStatus();
        if ($this->hasError == true) {
            return $this->error;
        }
        $this->checkServiceType();
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
        $this->checkService();

        if ($this->hasError == true) {
            return $this->error;
        }
        $this->checkTechnician();
        if ($this->hasError == true) {
            return $this->error;
        }
        $this->checkFrequency();
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

        if ($this->input['appointment_start'] == null) {
            $this->input['appointment_pending'] = true;
        }

        if ($this->input['appointment_start'] != null) {
            $this->input['appointment_pending'] = false;
        }



        $this->service->update($this->input);


        //Calendar Events
        $calendar = Calendar::where('service_id', $this->service->id)->first();

        //if ($this->service->status != "Μη Ολοκληρωμένο" && $calendar && $this->service->repeatable == false) $calendar->delete(); -> uncomment if all goes wrong
        //if($this->service->status != "Ολοκληρωμένο" && $this->repeatable->status == false && $calendar)$calendar->delete();
        if ($this->service->status == "Μη Ολοκληρωμένο" && !$calendar && $this->repeatable == true) Calendar::create(['name' => 'service', 'type' => 'services', 'service_id' => $this->service->id]);
        //TechMail::sendToTechs($service, "σέρβις", "update");

        $service = Service::find($this->service['id']);
        TechMail::sendToTechs($service, "σέρβις", "update");

        return response()->json(["message" => "Τα στοίχεια του service με κωδικό " . $this->request->id . " ενημερώθηκαν επιτυχώς!"], 200);
    }

    public function createUpdateInput()
    {
        if ($this->request->completed_no_transaction == true && $this->request->service_completed == true) {
            $this->hasError = true;
            $this->error = request()->json(["message" => "Η συναλλαγή δεν μπορεί να έιναι ακυρωμένη και επιδιορθωμένη!"], 200);
        } elseif ($this->request->service_completed == true || $this->request->status == "Ολοκληρώθηκε") {
            $this->input = array();
            if ($this->request->repeatable == true && $this->request->appointment_start != null) {
                $newDateTime = strtotime($this->request->frequency, strtotime($this->request->appointment_start));
                $newDate = date('c', $newDateTime);
                $newDateArray = explode('+', $newDate);
                $newDate = $newDateArray[0] . ".000Z";
                $status = 1;
            } else {
                $newDate = $this->request->appointment_start;
                $status = 0;
            }

            $this->input =
                [
                    "service_type_id2" => $this->request->service_type_id2,
                    "service_comments" => $this->request->service_comments,
                    "cost" => $this->request->cost,
                    "guarantee" => $this->request->guarantee,
                    "status" => $status == 1 ? "Μη Ολοκληρωμένο" : "Ολοκληρώθηκε",
                    "appointment_pending" =>  $status == 1 ? true : false,
                    "technician_left" => $status == 1 ? false : $this->request->technician_left,
                    "technician_arrived" => $status == 1 ? false : $this->request->technician_arrived,
                    "appointment_completed" => $status == 1 ? false : $this->request->appointment_completed,
                    "appointment_needed" => $status == 1 ? false : $this->request->appointment_needed,
                    "supplement_pending" => $status == 1 ? false : $this->request->supplement_pending,
                    "completed_no_transaction" => $status == 1 ? false : $this->request->completed_no_transaction,
                    "service_completed" => $status == 1 ? false : true,
                    "client_id" => $this->request->client_id,
                    "marks" => $this->insertMarks(),
                    //"device_id" => $this->request->device_id,
                    "comments" => $this->request->comments,
                    //"manufacturer_id" => $this->request->manufacturer_id,
                    //"mark_id" => $this->request->mark_id,
                    "supplements" => $this->request->supplements,
                    "appointment_start" => $newDate,
                    "appointment_end" => $this->request->appointment_end,
                    // "user_id" => $this->request->user_id,
                    "techs" => $this->insertTechs(),
                    "repeatable" => $this->request->repeatable,
                    "frequency" => $this->request->frequency,
                    "manager_payment" => $this->request->manager_payment
                ];
        } elseif ($this->request->status == "Ακυρώθηκε") {
            $this->input = array();
            $this->input =
                [
                    "service_type_id2" => $this->request->service_type_id2,
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
                    "service_completed" => false,
                    "client_id" => $this->request->client_id,
                    "device_id" => $this->request->device_id,
                    "comments" => $this->request->comments,
                    "manufacturer_id" => $this->request->manufacturer_id,
                    "mark_id" => $this->request->mark_id,
                    "supplements" => $this->request->supplements,
                    "appointment_start" => $this->request->appointment_start,
                    "appointment_end" => $this->request->appointment_end,
                    // "user_id" => $this->request->user_id,
                    "techs" => $this->insertTechs(),
                    "marks" => $this->insertMarks(),
                    "repeatable" => $this->request->repeatable,
                    "frequency" => $this->request->frequency,
                    "manager_payment" => $this->request->manager_payment
                ];
        } else {
            $this->input = array();
            $this->input =
                [
                    "service_type_id2" => $this->request->service_type_id2,
                    "service_comments" => $this->request->service_comments,
                    "cost" => $this->request->cost,
                    "guarantee" => $this->request->guarantee,
                    "status" => $this->request->status,
                    "appointment_pending" => $this->request->appointment_pending,
                    "technician_left" => $this->request->technician_left,
                    "technician_arrived" => $this->request->technician_arrived,
                    "appointment_completed" => $this->request->appointment_completed,
                    "appointment_needed" => $this->request->appointment_needed,
                    "supplement_pending" => $this->request->supplement_pending,
                    "completed_no_transaction" => $this->request->completed_no_transaction,
                    "service_completed" => false,
                    "client_id" => $this->request->client_id,
                    //"device_id" => $this->request->device_id,
                    "comments" => $this->request->comments,
                    //"manufacturer_id" => $this->request->manufacturer_id,
                    //"mark_id" => $this->request->mark_id,
                    "marks" => $this->insertMarks(),
                    "techs" => $this->insertTechs(),
                    "supplements" => $this->request->supplements,
                    "appointment_start" => $this->request->appointment_start,
                    "appointment_end" => $this->request->appointment_end,
                    //"user_id" => $this->request->user_id,
                    "repeatable" => $this->request->repeatable,
                    "frequency" => $this->request->frequency,
                    "manager_payment" => $this->request->manager_payment
                ];
        }
    }
}
