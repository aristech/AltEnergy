<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\FreeAppointment;

class FreeAppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "event_id" => $this->id,
            'type' => 'appointments',
            "title" => $this->when(true, function () {
                if (count($this->users) > 0) {
                    $technicians_array = array();
                    foreach ($this->users as $user) {
                        array_push($technicians_array, $user->firstname . " " . $user->lastname);
                    }
                    $technicians = implode(", ", $technicians_array);
                } else {
                    $technicians = '';
                }

                if ($this->appointment_start) {
                    $date_array = explode(".", $this->appointment_start);
                    $newDateFormat = str_replace("T", " ", $date_array[0]);
                    $time_start = date("H:i", strtotime('+2 hours', strtotime($newDateFormat)));
                } else {
                    $time_start = "?";
                }

                if ($this->appointment_end) {
                    $date_array_end = explode(".", $this->appointment_end);
                    $newDateFormatEnd = str_replace("T", " ", $date_array_end[0]);
                    $time_end = date("H:i", strtotime('+2 hours', strtotime($newDateFormatEnd)));
                } else {
                    $time_end = "?";
                }

                $html =  "<div>";
                $html .= "<b>Ωρα: </b>" . $time_start . " - " . $time_end . "<br>";
                $html .= "<b>Tιτλος: </b>" . $this->appointment_title . "<br>";
                $html .= "<b>Τεχνικοί: </b>" . $technicians . "<br>";
                $html .= $this->appointment_completed == 0 ? "<b>Κατάσταση: </b>" . "Δεν Ολοκληρώθηκε" . '<br>' : "<b>Κατάσταση: </b>" . "Ολοκληρώθηκε" . '<br>';
                $html .= $this->appointment_location == "" ? "" : "<b>Περιοχή: </b>" . $this->appointment_location . "<br>";
                $html .= "</div>";
                return $html;
            }),
            "name" => "Ραντεβου",
            "resource" => "appointments",
            "appointment_title" => $this->appointment_title,
            "start" => $this->appointment_start,
            "end" => $this->appointment_end,
            "appointment_description" => $this->appointment_description,
            "tech_names" => $this->when(true, function () {
                $techs = $this->users;
                $tech_array = array();
                foreach ($techs as $tech) {
                    array_push($tech_array, $tech->lastname);
                }
                return implode(', ', $tech_array);
            }),
            "techs" => $this->when(true, function () {
                $techs = $this->users;
                $tech_array = array();
                foreach ($techs as $tech) {
                    array_push($tech_array, $tech->id);
                }
                return $tech_array;
            }),
            "appointment_completed" => $this->appointment_completed,
            "appointment_status" => $this->when(true, function () {
                if ($this->appointment_completed) {
                    return "Ολοκληρώθηκε";
                } else {
                    return "Δεν Ολοκληρώθηκε";
                }
            }),
            "appointment_location" => $this->appointment_location,
            "appointment_start" => $this->appointment_start,
            "client_address" => $this->location,
            "appointment_end" => $this->appointment_end,
            "editable" => array([
                "resource" => "appointments",
                "id" => $this->id,
                "type" => "appointments",
                "appointment_title" =>  ["roles" => array(5, 4, 3), "field" => "appointment_title", "type" => "text", "title" => "ΤΙΤΛΟΣ/ΣΥΝΤΟΜΗ ΠΕΡΙΓΡΑΦΗ", "value" => $this->appointment_title, "required" => true],
                "appointment_description" =>  ["roles" => array(5, 4, 3), "field" => "appointment_description", "type" => "text", "title" => "ΑΝΑΛΥΤΙΚΟΤΕΡΗ ΠΕΡΙΓΡΑΦΗ", "value" => $this->appointment_description, "required" => true],
                "appointment_description" =>  ["roles" => array(5, 4, 3), "field" => "appointment_location", "type" => "text", "title" => "ΠΕΡΙΟΧΗ", "value" => $this->appointment_location, "required" => false],
                "appointment_completed" => ["roles" => array(5, 4, 3), "field" => "appointment_completed", "value" => $this->appointment_completed, "type" => "boolean", "title" => "ΚΑΤΑΣΤΑΣΗ ΡΑΝΤΕΒΟΥ", "radioItems" => [["id" => 1, "title" => "ΟΛΟΚΛΗΡΩΘΗΚΕ"], ["id" => 0, "title" => "ΔΕΝ ΟΛΟΚΛΗΡΩΘΗΚΕ"]], "required" => true],
                "techs" =>  $this->when(true, function () {
                    $technicians = array();
                    $technician_ids = array();
                    if (count($this->users) > 0) {
                        foreach ($this->users as $tech) {
                            array_push($technicians, $tech->lastname . " " . $tech->firstname);
                            $techno = new \stdClass();
                            $techno->id = $tech->id;
                            $techno->fullname = $tech->firstname . " " . $tech->lastname;
                            $techno->email = $tech->email;
                            $techno->telephone = $tech->telephone;
                            $techno->telephone2 = $tech->telephone2;
                            $techno->mobile = $tech->mobile;
                            array_push($technician_ids, $techno);
                        }
                        $technicians = ["roles" => array(5, 4, 3), "title" => "ΤΕΧΝΙΚΟΙ", "field" => "techs", "type" => "searchtechs", "page" => "tech", "value" => $technician_ids, "holder" => $technicians, "required" => false];
                    } else {
                        $technicians = ["roles" => array(5, 4, 3), "title" => "ΤΕΧΝΙΚΟΙ", "field" => "techs", "type" => "searchtechs", "page" => "tech", "value" => array(), "holder" => array(), "required" => false];
                    }
                    return $technicians;
                }),

                "appointment_start" => ["roles" => array(5, 4, 3), "field" => "appointment_start", "title" => "ΕΝΑΡΞΗ ΡΑΝΤΕΒΟΥ", "type" => "datetime", "value" => $this->appointment_start, "required" => true],
                "appointment_end" => ["roles" => array(5, 4, 3), "field" => "appointment_end", "title" => "ΛΗΞΗ ΡΑΝΤΕΒΟΥ", "type" => "datetime", "value" => $this->appointment_end, "required" => true]
            ]),
            "color" => $this->when(true, function () {
                if ($this->appointment_start) {
                    $appointment_start  = $this->appointment_start;
                    $app_start_array = explode('.', $appointment_start);
                    $formatted_appointment = str_replace('T', ' ', $app_start_array[0]);
                    $date_to_compare = strtotime($formatted_appointment) + 2 * 60 * 60;
                    if (time() - $date_to_compare > 0 && !$this->appointment_completed) {
                        return "#ff0000";
                    }
                }
            }),
            "textColor" => $this->when(
                true,
                function () {
                    if ($this->appointment_completed) {
                        return "#ff0000";
                    }
                }
            ),
        ];
    }
}
