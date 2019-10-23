<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\User;

class DamageResource extends JsonResource
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
                "id" => $this->id,
                "damage_type" => $this->type['name'],
                "damage_comments" => $this->damage_comments,
                "cost" => $this->cost,
                "guarantee" => $this->guarantee,
                "status" => $this->status,
                "appointment_pending" => $this->appointment_pending,
                "technician_left" => $this->technician_left,
                "technician_arrived" => $this->technician_arrived,
                "appointment_completed" => $this->appointment_completed,
                "appointment_needed" => $this->appointment_needed,
                "supplement_pending" => $this->supplement_pending,
                "damage_fixed" => $this->damage_fixed,
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
                "supplement" => $this->supplement,
                "comments" => $this->comments,
                "appointment_start" => $this->appointment_start,
                "appointment_end" => $this->appointment_end,
                "techs" => $this->when(true, function () {
                    $technicians = array();
                    if ($this->techs == null) {
                        return $technicians;
                    }
                    $techs = explode(',', $this->techs);
                    foreach ($techs as $tech) {
                        $techn = User::where('id', $tech)->first();
                        $technician = new \stdClass();
                        $technician->tech_id = $tech;
                        $technician->tech_fullname = $techn['lastname'] . " " . $techn['firstname'];
                        array_push($technicians, $technician);
                    }
                    return $technicians;
                }),

                "editable" => array([
                    "resource" => "damages",
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
                    "damage" => ["field" => "damage_type_id", "value" => $this->damage_type_id, "type" => "search", "title" => "Τύπος βλάβης", "page" => "damagetypes", "holder" => $this->type['name'], "required" => false],
                    "client" => ["field" => "client_id", "value" => $this->client_id, "type" => "search", "title" => "Πελάτης", "page" => "clients", "holder" => $this->client['firstname'] . " " . $this->client['lastname'] . " | " . $this->client['address'], "required" => true],
                    "techs" => $this->when(true, function () {
                        $technicians = array();
                        $technician_ids = array();
                        if ($this->techs != null)
                        {
                            $techs = explode(',', $this->techs);
                            foreach ($techs as $tech) {
                                $techn = User::where('id', $tech)->first();
                                array_push($technicians, $techn['lastname'] . " " . $techn['firstname']);
                                $techno = new \stdClass();
                                $techno->id = $techn['id'];
                                $techno->fullname = $techn['lastname'] . " " . $techn['firstname'];
                                $techno->email = $techn['email'];
                                $techno->telephone = $techn['telephone'];
                                $techno->telephone2 = $techn['telephone2'];
                                $techno->mobile = $techn['mobile'];
                                array_push($technician_ids, $techno);
                            }

                            $technicians = ["title" => "Τεχνικοί", "field" => "techs", "type" => "searchtechs", "page" => "tech", "value" => $technician_ids, "holder" => $technicians, "required" => false];
                        }
                        else
                        {
                            $technicians = ["title" => "Τεχνικοί", "field" => "techs", "type" => "searchtechs", "page" => "tech", "value" => [], "holder" => [], "required" => false];
                        }

                        return $technicians;
                    }),
                    "manufacturer" => ["field" => "manufacturer_id", "value" => $this->manufacturer_id, "required" => true],
                    "mark" => ["field" => "mark_id", "value" => $this->mark_id, "required" => true],
                    "devices" => ["field" => "device_id", "value" => $this->device_id, "type" => "searchdevices", "title" => "Συσκευή", "page" => "devices", "holder" => $this->device['mark']['manufacturer']['name'] . " / " . $this->device['mark']['name'] . " / " . $this->device['name'], "required" => true],
                    "status" => ["field" => "status", "value" => $this->status, "type" => "boolean", "title" => "Κατάσταση", "radioItems" => [["id" => "Ολοκληρωμένη", "title" => "Ολοκληρωμένη"], ["id" => "Μη Ολοκληρωμένη", "title" => "Μη Ολοκληρωμένη"], ["id" => "Ακυρώθηκε", "title" => "Ακυρώθηκε"]], "required" => true],
                    "guarantee" => ["field" => "guarantee", "value" => $this->guarantee, "type" => "boolean", "title" => "Εγγύηση", "radioItems" => [["id" => "1", "title" => "Με εγγύηση"], ["id" => 0, "title" => "Χωρίς εγγύηση"]], "required" => true],
                    "appointment_pending" => ["field" => "appointment_pending", "value" => $this->appointment_pending, "type" => "boolean", "title" => "Αναμονή ραντεβού", "radioItems" => [["id" => "0", "title" => "Οχι"], ["id" => 1, "title" => "Ναι"]], "required" => true],
                    "technician_left" => ["field" => "technician_left", "value" => $this->technician_left, "type" => "boolean", "title" => "Αποχώρηση Τεχνικού", "radioItems" => [["id" => "0", "title" => "Οχι"], ["id" => 1, "title" => "Ναι"]], "required" => true],
                    "technician_arrived" => ["field" => "technician_arrived", "value" => $this->technician_arrived, "type" => "boolean", "title" => "Άφηξη Τεχνικού", "radioItems" => [["id" => "0", "title" => "Οχι"], ["id" => 1, "title" => "Ναι"]], "required" => true],
                    "appointment_completed" => ["field" => "appointment_completed", "value" => $this->appointment_completed, "type" => "boolean", "title" => "Ολοκλήρωση Ραντεβού", "radioItems" => [["id" => "0", "title" => "Οχι"], ["id" => 1, "title" => "Ναι"]], "required" => true],
                    "appointment_needed" => ["field" => "appointment_needed", "value" => $this->appointment_needed, "type" => "boolean", "title" => "Ανάγκη για Νέο Ραντεβού", "radioItems" => [["id" => "0", "title" => "Οχι"], ["id" => 1, "title" => "Ναι"]], "required" => true],
                    "supplement_pending" => ["field" => "supplement_pending", "value" => $this->supplement_pending, "type" => "boolean", "title" => "Αναμονή Ανταλλακτικού", "radioItems" => [["id" => "0", "title" => "Οχι"], ["id" => 1, "title" => "Ναι"]], "required" => true],
                    "damage_fixed" => ["field" => "damage_fixed", "value" => $this->damage_fixed, "type" => "boolean", "title" => "Βλάβη Επιδιορθώθηκε", "radioItems" => [["id" => "0", "title" => "Οχι"], ["id" => 1, "title" => "Ναι"]], "required" => true],
                    "completed_no_transaction" => ["field" => "completed_no_transaction", "value" => $this->completed_no_transaction, "type" => "boolean", "title" => "Ολοκλήρωση χωρίς συναλλαγή", "radioItems" => [["id" => "0", "title" => "Οχι"], ["id" => 1, "title" => "Ναι"]], "required" => true],
                    "appointment_start" => ["field" => "appointment_start", "title" => "Έναρξη Ραντεβού", "type" => "datetime", "value" => $this->appointment_start, "required" => false],
                    "appointment_end" => ["field" => "appointment_end", "title" => "Λήξη Ραντεβού", "type" => "datetime", "value" => $this->appointment_end, "required" => false],
                    "cost" => ["field" => "cost", "value" => $this->cost, "type" => "float", "title" => "Τιμή", "required" => false],
                    "supplement" => ["field" => "supplement", "title" => "Ανταλλακτικά(Διαχωρίστε τα ανταλλακτικά με ',')", "type" => "text", "value" => $this->supplement, "required" => false],
                    "damage_comments" => ["field" => "damage_comments", "value" => $this->damage_comments, "type" => "text", "title" => "Σχόλια Βλάβης", "required" => false],
                    "comments" =>  ["field" => "comments", "type" => "text", "title" => "Γενικά Σχόλια", "value" => $this->comments, "required" => false]
                ])
            ];
    }
}
