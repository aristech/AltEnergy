<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\User;
use App\Mark;

class ServiceResource extends JsonResource
{
    public function fixedAddress()
    {
        $add = '';
        if ($this->client['address']) {
            $add .= $this->client['address'];
        }

        if ($this->client['location'] && $this->client['address']) {
            $add .= ', ' . $this->client['location'];
        } elseif ($this->client['location'] && !$this->client['address']) {
            $add .=  $this->client['location'];
        }

        if ($this->client['zipcode'] && ($this->client['address'] || $this->client['location'])) {
            $add .= ', ' . $this->client['zipcode'];
        } elseif ($this->client['zipcode'] && (!$this->client['address'] && !$this->client['location'])) {
            $add = $this->client['zipcode'];
        }

        return $add;
    }
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
                'case_type' => 'ΣΕΡΒΙΣ',
                'case_name' => $this->type['name'],
                "resource" => "services",
                "id" => $this->id,
                "service_type" => $this->type['name'],
                "service_comments" => $this->damage_comments,
                "cost" => $this->cost,
                "manager_payment" => $this->manager_payment,
                "total_cost" => $this->cost + $this->manager_payment,
                "guarantee" => $this->guarantee,
                "status" => $this->status,
                "appointment_pending" => $this->appointment_pending,
                "appointment_pending_text" => $this->when(true, function () {
                    if ($this->appointment_pending == 1) {
                        return "ναι";
                    } else {
                        return "οχι";
                    }
                }),
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
                "client_address" => $this->fixedAddress(),
                "client_phone" =>  $this->when(true, function () {
                    if ($this->client['telephone'] != null) return $this->client['telephone'];
                    if ($this->client['telephone2'] != null) return $this->client['telephone2'];
                    if ($this->client['mobile'] != null) return $this->client['mobile'];
                }),
                // "manufacturer_id" => $this->manufacturer_id,
                // "manufacturer" => $this->device['mark']['manufacturer']['name'],
                // "mark_id" => $this->mark_id,
                // "mark" => $this->device['mark']['name'],
                // "device_id" => $this->device_id,
                // "device" => $this->device['name'],
                "devices" => $this->when(true, function () {
                    if ($this->marks === null) {
                        return "";
                    }
                    $detailed_device_array = array();
                    $devices_array = explode(',', $this->marks);
                    foreach ($devices_array as $d) {
                        $mark = Mark::where('id', $d)->first();
                        if ($mark) {
                            $device = $mark['manufacturer']['name'] . "/" . $mark['name'];
                            array_push($detailed_device_array, $device);
                        }
                    }

                    return implode(" , ", $detailed_device_array);
                }),
                "supplements" => $this->supplements,
                "comments" => $this->comments,
                "appointment_start" => $this->appointment_start,
                "appointment_end" => $this->appointment_end,
                "user_id" => $this->user_id,
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
                "marks" => $this->when(true, function () {
                    $markes = array();
                    if ($this->marks == null) {
                        return $markes;
                    }
                    $marks = explode(',', $this->marks);
                    foreach ($marks as $mark) {
                        $marka = new \stdClass();
                        $markk = Mark::where('id', $mark)->first();
                        if ($markk) {
                            $marka->id = $mark;
                            $marka->fullname = $markk['manufacturer']['name'] . ", " . $markk['name'];
                            array_push($markes, $marka);
                        }
                    }
                    return $markes;
                }),
                "repeatable" => $this->repeatable,
                "frequency" => $this->frequency,
                "editable" => array([
                    "resource" => "services",
                    "id" => $this->id,
                    "info" => [
                        "client_lastname" => $this->client['lastname'],
                        "client_firstname" => $this->client['firstname'],
                        "client_address" => $this->client['address'] . "," . $this->client['location'] . "," . $this->client['zipcode'],
                        "client_phone" =>  $this->when(true, function () {
                            if ($this->client['telephone'] != null) return $this->client['telephone'];
                            if ($this->client['telephone2'] != null) return $this->client['telephone2'];
                            if ($this->client['mobile'] != null) return $this->client['mobile'];
                        })
                    ],
                    "service" => ["roles" => array(5, 4, 3), "field" => "service_type_id2", "value" => $this->service_type_id2, "type" => "search", "title" => "ΤΥΠΟΣ ΣΕΡΒΙΣ", "page" => "damagetypes", "holder" => $this->type['name'], "required" => false],
                    "client" => ["roles" => array(5, 4, 3), "field" => "client_id", "value" => $this->client_id, "type" => "search", "title" => "ΠΕΛΑΤΗΣ", "page" => "clients", "holder" => $this->client['firstname'] . " " . $this->client['lastname'] . " | " . $this->client['address'] . ',' . $this->client['level'] . "ος Οροφος", "required" => true],
                    "techs" => $this->when(true, function () {
                        $technicians = array();
                        $technician_ids = array();
                        if ($this->techs != null) {
                            $techs = explode(',', $this->techs);
                            foreach ($techs as $tech) {
                                $techn = User::where('id', $tech)->where('active', true)->first();
                                if ($techn) {
                                    array_push($technicians, $techn['lastname'] . " " . $techn['firstname']);
                                    $techno = new \stdClass();
                                    $techno->id = $techn['id'];
                                    $techno->fullname = $techn['firstname'] . " " . $techn['lastname'];
                                    $techno->email = $techn['email'];
                                    $techno->telephone = $techn['telephone'];
                                    $techno->telephone2 = $techn['telephone2'];
                                    $techno->mobile = $techn['mobile'];
                                    array_push($technician_ids, $techno);
                                }
                            }

                            $technicians = ["roles" => array(5, 4, 3), "title" => "ΤΕΧΝΙΚΟΙ", "field" => "techs", "type" => "searchtechs", "page" => "tech", "value" => $technician_ids, "holder" => $technicians, "required" => false];
                        } else {
                            $technicians = ["roles" => array(5, 4, 3), "title" => "ΤΕΧΝΙΚΟΙ", "field" => "techs", "type" => "searchtechs", "page" => "tech", "value" => array(), "holder" => array(), "required" => false];
                        }
                        return $technicians;
                    }),
                    "marks" => $this->when(true, function () {
                        $markes = array();
                        $markes_ids = array();
                        if ($this->marks != null) {
                            $marks = explode(',', $this->marks);
                            foreach ($marks as $mark) {
                                $marka = Mark::where('id', $mark)->first();
                                if ($marka) {
                                    array_push($markes, $marka['manufacturer']['name'] . ", " . $marka['name']);
                                    $markno = new \stdClass();
                                    $markno->id = $marka['id'];
                                    $markno->fullname = $marka['manufacturer']['name'] . ", " . $marka['name'];
                                    array_push($markes_ids, $markno);
                                }
                            }

                            $markes = ["roles" => array(5, 4, 3), "title" => "ΣΥΣΚΕΥΕΣ", "field" => "marks", "type" => "searchtechs", "page" => "searchclientmarks/" . $this->client_id, "value" => $markes_ids, "holder" => $markes, "required" => false];
                        } else {
                            $markes = ["roles" => array(5, 4, 3), "title" => "ΣΥΣΚΕΥΕΣ", "field" => "marks", "type" => "searchtechs", "page" => "searchclientmarks/" . $this->client_id, "value" => array(), "holder" => array(), "required" => false];
                        }
                        return $markes;
                    }),
                    //"manufacturer" => ["roles" => array(5, 4, 3), "field" => "manufacturer_id", "value" => $this->manufacturer_id, "required" => true],
                    //"mark" => ["roles" => array(5, 4, 3), "field" => "mark_id", "value" => $this->mark_id, "required" => true],
                    //"devices" => ["roles" => array(5, 4, 3), "field" => "device_id", "value" => $this->device_id, "type" => "searchdevices", "title" => "ΣΥΣΚΕΥΗ", "page" => "devices", "holder" => $this->device['mark']['manufacturer']['name'] . " / " . $this->device['mark']['name'] . " / " . $this->device['name'], "required" => true],
                    "status" => ["roles" => array(5, 4, 3), "field" => "status", "value" => $this->status, "type" => "boolean", "title" => "ΚΑΤΑΣΤΑΣΗ", "radioItems" => [["id" => "Ολοκληρώθηκε", "title" => "ΟΛΟΚΛΗΡΩΘΗΚΕ"], ["id" => "Μη Ολοκληρωμένο", "title" => "ΜΗ ΟΛΟΚΛΗΡΩΜΕΝΟ"], ["id" => "Ακυρώθηκε", "title" => "ΑΚΥΡΩΘΗΚΕ"]], "required" => true],
                    "guarantee" => ["roles" => array(5, 4, 3), "field" => "guarantee", "value" => $this->guarantee, "type" => "boolean", "title" => "ΕΓΓΥΗΣΗ", "radioItems" => [["id" => 1, "title" => "ΜΕ ΕΓΓΥΗΣΗ"], ["id" => 0, "title" => "ΧΩΡΙΣ ΕΓΓΥΗΣΗ"]], "required" => true],
                    "appointment_pending" => ["roles" => array(5, 4, 3), "field" => "appointment_pending", "value" => $this->appointment_pending, "type" => "boolean", "title" => "ΑΝΑΜΟΝΗ ΡΑΝΤΕΒΟΥ", "radioItems" => [["id" => 0, "title" => "ΟΧΙ"], ["id" => 1, "title" => "ΝΑΙ"]], "required" => true],
                    "technician_left" => ["roles" => array(5, 4, 3), "field" => "technician_left", "value" => $this->technician_left, "type" => "boolean", "title" => "ΑΠΟΧΩΡΗΣΗ ΤΕΧΝΙΚΟΥ", "radioItems" => [["id" => 0, "title" => "ΟΧΙ"], ["id" => 1, "title" => "ΝΑΙ"]], "required" => true],
                    "technician_arrived" => ["roles" => array(5, 4, 3), "field" => "technician_arrived", "value" => $this->technician_arrived, "type" => "boolean", "title" => "ΑΦΗΞΗ ΤΕΧΝΙΚΟΥ", "radioItems" => [["id" => 0, "title" => "ΟΧΙ"], ["id" => 1, "title" => "ΝΑΙ"]], "required" => true],
                    "appointment_completed" => ["roles" => array(5, 4, 3), "field" => "appointment_completed", "value" => $this->appointment_completed, "type" => "boolean", "title" => "ΟΛΟΚΛΗΡΩΣΗ ΡΑΝΤΕΒΟΥ", "radioItems" => [["id" => 0, "title" => "ΟΧΙ"], ["id" => 1, "title" => "ΝΑΙ"]], "required" => true],
                    "appointment_needed" => ["roles" => array(5, 4, 3), "field" => "appointment_needed", "value" => $this->appointment_needed, "type" => "boolean", "title" => "ΑΝΑΓΚΗ ΓΙΑ ΝΕΟ ΡΑΝΤΕΒΟΥ", "radioItems" => [["id" => 0, "title" => "ΟΧΙ"], ["id" => 1, "title" => "ΝΑΙ"]], "required" => true],
                    "supplement_pending" => ["roles" => array(5, 4, 3), "field" => "supplement_pending", "value" => $this->supplement_pending, "type" => "boolean", "title" => "ΑΝΑΜΟΝΗ ΑΝΤΑΛΛΑΚΤΙΚΟΥ", "radioItems" => [["id" => 0, "title" => "ΟΧΙ"], ["id" => 1, "title" => "ΝΑΙ"]], "required" => true],
                    "service_completed" => ["roles" => array(5, 4, 3), "field" => "service_completed", "value" => $this->service_completed, "type" => "boolean", "title" => "ΣΕΡΒΙΣ ΟΛΟΚΛΗΡΩΘΗΚΕ", "radioItems" => [["id" => 0, "title" => "ΟΧΙ"], ["id" => 1, "title" => "ΝΑΙ"]], "required" => true],
                    "completed_no_transaction" => ["roles" => array(5, 4, 3), "field" => "completed_no_transaction", "value" => $this->completed_no_transaction, "type" => "boolean", "title" => "ΟΛΟΚΛΗΡΩΣΗ ΧΩΡΙΣ ΣΥΝΑΛΛΑΓΗ", "radioItems" => [["id" => 0, "title" => "ΟΧΙ"], ["id" => 1, "title" => "ΝΑΙ"]], "required" => true],
                    "appointment_start" => ["roles" => array(5, 4, 3, 2), "field" => "appointment_start", "title" => "ΕΝΑΡΞΗ ΡΑΝΤΕΒΟΥ", "type" => "datetime", "value" => $this->appointment_start, "required" => false],
                    "appointment_end" => ["roles" => array(5, 4, 3), "field" => "appointment_end", "title" => "ΛΗΞΗ ΡΑΝΤΕΒΟΥ", "type" => "datetime", "value" => $this->appointment_end, "required" => false],
                    "cost" => ["roles" => array(5, 4, 3), "field" => "cost", "value" => $this->cost, "type" => "float", "title" => "ΤΙΜΗ", "required" => false],
                    "manager_payment" => ["roles" => array(5, 4, 3, 2), "field" => "manager_payment", "value" => $this->manager_payment, "type" => "float", "title" => "ΠΛΗΡΩΜΗ ΔΙΑΧΕΙΡΙΣΤΗ", "required" => false],
                    "total_cost" => ["roles" => array(5), "field" => "total_cost", "value" => $this->manager_payment + $this->cost, "type" => "float", "title" => "ΣΥΝΟΛΙΚΟ ΚΟΣΤΟΣ", "required" => false],
                    "supplements" => ["roles" => array(5, 4, 3), "field" => "supplements", "title" => "ΑΝΤΑΛΛΑΚΤΙΚΑ(ΔΙΑΧΩΡΙΣΤΕ ΤΑ ΑΝΤΑΛΛΑΚΤΙΚΑ ΜΕ ',')", "type" => "text", "value" => $this->supplements, "required" => false],
                    "service_comments" => ["roles" => array(5, 4, 3), "field" => "service_comments", "value" => $this->service_comments, "type" => "text", "title" => "ΣΧΟΛΙΑ ΣΕΡΒΙΣ", "required" => false],
                    "comments" =>  ["roles" => array(5, 4, 3), "field" => "comments", "type" => "text", "title" => "ΓΕΝΙΚΑ ΣΧΟΛΙΑ", "value" => $this->comments, "required" => false],
                    "repeatable" => ["roles" => array(5, 4, 3), "field" => "repeatable", "value" => $this->repeatable, "type" => "boolean", "title" => "ΣΕΡΒΙΣ ΕΠΑΝΑΛΑΜΒΑΝΟΜΕΝΟ;", "radioItems" => [["id" => 1, "title" => "ΝΑΙ"], ["id" => 0, "title" => "ΟΧΙ"]], "required" => false],
                    "frequency" => ["roles" => array(5, 4, 3), "field" => "frequency", "value" => $this->frequency, "type" => "boolean", "title" => "ΕΠΑΝΑΛΗΨΗ ΚΑΘΕ:", "radioItems" => [["id" => "+3 months", "title" => "ΤΡΙΜΗΝΟ"], ["id" => "+6 months", "title" => "ΕΞΑΜΗΝΟ"], ["id" => "+1 year", "title" => "ΕΤΟΣ"],  ["id" => "+3 years", "title" => "ΤΡΙΕΤΙΑ"]], "required" => false]
                ])

            ];
    }
}
