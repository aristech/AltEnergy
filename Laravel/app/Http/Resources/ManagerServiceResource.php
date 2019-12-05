<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\User;

class ManagerServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return
            [
                "resource" => "services",
                "id" => $this->id,
                "service_type" => $this->type['name'],
                "service_comments" => $this->damage_comments,
                "cost" => $this->cost,
                "guarantee" => $this->guarantee,
                "status" => $this->status,
                "appointment_pending" => $this->appointment_pending,
                "technician_left" => $this->technician_left,
                "technician_arrived" => $this->technician_arrived,
                "appointment_completed" => $this->appointment_completed,
                "appointment_needed" => $this->appointment_needed,
                "supplement_pending" => $this->supplement_pending,
                "service_completed" => $this->service_completed,
                "completed_no_transaction" => $this->completed_no_transaction,
                "client_id" => $this->client_id,
                "client_lastname" => $this->client['lastname'],
                "client_firstname" => $this->client['firstname'],
                "client_address" => $this->client['address'],
                "client_phone" =>  $this->when(true, function () {
                    if ($this->client['telephone'] != null) return $this->client['telephone'];
                    if ($this->client['telephone2'] != null) return $this->client['telephone2'];
                    if ($this->client['mobile'] != null) return $this->client['mobile'];
                }),
                "manufacturer_id" => $this->manufacturer_id,
                "manufacturer" => $this->device['mark']['manufacturer']['name'],
                "mark_id" => $this->mark_id,
                "mark" => $this->device['mark']['name'],
                "device_id" => $this->device_id,
                "device" => $this->device['name'],
                "supplements" => $this->supplements,
                "comments" => $this->comments,
                "appointment_start" => $this->appointment_start,
                "appointment_end" => $this->appointment_end,
                "user_id" => $this->user_id,
                'manager_payment' => $this->manager_payment,
                "techs" => $this->when(true, function () {
                    $technicians = array();
                    if ($this->techs == null) {
                        return $technicians;
                    }
                    $techs = explode(',', $this->techs);
                    foreach ($techs as $tech) {
                        $technician = new \stdClass();
                        $techn = User::where('id', $tech)->where('active', true)->first();
                        if ($techn) {
                            $technician->tech_id = $tech;
                            $technician->tech_fullname = $techn['lastname'] . " " . $techn['firstname'];
                            array_push($technicians, $technician);
                        }
                    }
                    return $technicians;
                }),
                "repeatable" => $this->repeatable,
                "frequency" => $this->frequency,
                "editable" => array([
                    "resource" => "services",
                    "id" => $this->id,
                    "appointment_start" => ["field" => "appointment_start", "title" => "Έναρξη Ραντεβού", "type" => "datetime", "value" => $this->appointment_start, "required" => false],
                    "appointment_end" => ["field" => "appointment_end", "title" => "Λήξη Ραντεβού", "type" => "datetime", "value" => $this->appointment_end, "required" => false],
                    // "info" => [
                    //     "client_lastname" => $this->client['lastname'],
                    //     "client_firstname" => $this->client['firstname'],
                    //     "client_address" => $this->client['address'] . "," . $this->client['location'] . "," . $this->client['zipcode'],
                    //     "client_phone" =>  $this->when(true, function () {
                    //         if ($this->client['telephone'] != null) return $this->client['telephone'];
                    //         if ($this->client['telephone2'] != null) return $this->client['telephone2'];
                    //         if ($this->client['mobile'] != null) return $this->client['mobile'];
                    //     })
                    // ],
                    // "service" => ["field" => "service_type_id2", "value" => $this->service_type_id2, "type" => "search", "title" => "Τύπος Σέρβις", "page" => "damagetypes", "holder" => $this->type['name'], "required" => false],
                    // "client" => ["field" => "client_id", "value" => $this->client_id, "type" => "search", "title" => "Πελάτης", "page" => "clients", "holder" => $this->client['firstname'] . " " . $this->client['lastname'] . " | " . $this->client['address'], "required" => true],
                    // "techs" => $this->when(true, function () {
                    //     $technicians = array();
                    //     $technician_ids = array();
                    //     if ($this->techs != null) {
                    //         $techs = explode(',', $this->techs);
                    //         foreach ($techs as $tech) {
                    //             $techn = User::where('id', $tech)->where('active', true)->first();
                    //             if ($techn) {
                    //                 array_push($technicians, $techn['lastname'] . " " . $techn['firstname']);
                    //                 $techno = new \stdClass();
                    //                 $techno->id = $techn['id'];
                    //                 $techno->fullname = $techn['firstname'] . " " . $techn['lastname'];
                    //                 $techno->email = $techn['email'];
                    //                 $techno->telephone = $techn['telephone'];
                    //                 $techno->telephone2 = $techn['telephone2'];
                    //                 $techno->mobile = $techn['mobile'];
                    //                 array_push($technician_ids, $techno);
                    //             }
                    //         }

                    //         $technicians = ["title" => "Τεχνικοί", "field" => "techs", "type" => "searchtechs", "page" => "tech", "value" => $technician_ids, "holder" => $technicians, "required" => false];
                    //     } else {
                    //         $technicians = ["title" => "Τεχνικοί", "field" => "techs", "type" => "searchtechs", "page" => "tech", "value" => array(), "holder" => array(), "required" => false];
                    //     }
                    //     return $technicians;
                    // }),
                    "manager_payment" => ["field" => "manager_payment", "value" => $this->manager_payment, "type" => "float", "title" => "Πληρωμή Διαχειριστή", "required" => false]
                ])

            ];
    }
}
