<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Damage;
use App\Eventt;
use App\Service;
//use App\Http\CustomClasses\v1\CalendarClass;
use App\Note;
use App\Device;
use App\DamageType;
use App\User;
use App\Mark;

class CalendarResource extends JsonResource
{
    public $calendar;

    public $calendarEntity;

    public function fetchCalendarEntity()
    {
        if ($this->damage_id != null) {
            $this->calendarEntity = Damage::where('id', $this->damage_id)->first();
        }

        if ($this->service_id != null) {
            $this->calendarEntity = Service::where('id', $this->service_id)->first();
        }

        if ($this->note_id != null) {
            $this->calendarEntity = Note::where('id', $this->note_id)->first();
        }
    }

    // public function __construct()
    // {
    //     $this->calendar = new CalendarClass($this->id, $this->damage_id, $this->service_id, $this->offer_id);
    // }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        date_default_timezone_set('Europe/Athens');

        $this->fetchCalendarEntity();

        return
            [
                "id" => $this->id,
                "type" => $this->type,
                "name" => $this->name,
                "event_id" => $this->when($this->damage_id != null || $this->service_id != null || $this->note_id != null, function () {
                    if ($this->damage_id != null) {
                        return $this->damage_id;
                    }

                    if ($this->event_id) {
                        return $this->event_id;
                    }

                    if ($this->service_id != null) {
                        return $this->service_id;
                    }

                    if ($this->note_id != null) {
                        return $this->note_id;
                    }
                }),
                "title" => $this->when($this->service_id != null || $this->damage_id != null || $this->service_id != null || $this->note_id != null, function () {
                    if ($this->damage_id != null) {
                        $damage = $this->calendarEntity;
                        // if ($damage['client']['telephone'] != null) {
                        if ($damage['techs']) {
                            $techs = explode(",", $damage['techs']);
                            $technician_array = array();
                            foreach ($techs as $tech) {
                                $technician = User::where('id', $tech)->first();
                                array_push($technician_array, $technician['lastname']);
                            }

                            $technicians = implode(", ", $technician_array);
                        } else {
                            $technicians = "Ν/Α";
                        }

                        if ($damage['appointment_start']) {
                            $time_start = date("H:i", strtotime($damage['appointment_start']));
                            /*
                            $date_array = explode(".", $damage['appointment_start']);
                            $newDateFormat = str_replace("T", " ", $date_array[0]);
                            $time_start = date("H:i", strtotime('+2 hours', strtotime($newDateFormat)));
                            */
                        } else {
                            $time_start = "?";
                        }

                        if ($damage['appointment_end']) {
                            /*
                            $date_array_end = explode(".", $damage['appointment_end']);
                            $newDateFormatEnd = str_replace("T", " ", $date_array_end[0]);
                            $time_end = date("H:i", strtotime('+2 hours', strtotime($newDateFormatEnd)));
                            */
                            $time_end = date("H:i", strtotime($damage['appointment_end']));
                        } else {
                            $time_end = "?";
                        }

                        //     $phone = $damage['client']['telephone'];
                        // } elseif ($damage['client']['telephone2'] != null) {
                        //     $phone = $damage['client']['telephone2'];
                        // } else {
                        //     $phone = $damage['client']['mobile'];
                        $client = $damage['client']['lastname'] !== null ? $damage['client']['lastname'] : "Ν/Α";
                        //$address = $damage['client']['address'] !== null ? $damage['client']['address'] : "N/A"; ->Removed 08/07/2020
                        $location = $damage['client']['location'] !== null ? $damage['client']['location'] : "Ν/Α";
                        //$status = $damage['status']; ->Removed 08/07/2020
                        // }
                        $html = "<div>";
                        $html .= "<b>Ωρα: </b>" . $time_start . " - " . $time_end . "<br>";
                        //$html .= "<b>Τύπος: </b>Βλάβη" . "<br>"; ->Removed 08/07/2020
                        $html .= $technicians == "" ? "" : "<b>Τεχνικοί: </b>" . $technicians . "<br>";
                        $html .= $client == "" ? "" : "<b>Πελάτης: </b>" . $client . "<br>";
                        //$html .= "<b>Κατάσταση:</b> " . $status . '<br>';  ->Removed 08/07/2020
                        //$html .= $address == "" ? "" : "<b>Διεύθυνση: </b>" . $address . "<br>";  ->Removed 08/07/2020
                        $html .= $location == "" ? "" : "<b>Περιοχή: </b>" . $location . "<br>";
                        $html .= "</div>";
                        return $html;
                        //                         texnikos - epwnumo
                        // p[erixh
                        // epitheto pelath

                    }

                    if ($this->event_id != null) {
                        return $this->calendarEntity['title'];
                    }

                    if ($this->service_id != null) {
                        $service = $this->calendarEntity;
                        // if ($service['client']['telephone'] != null) {
                        //     $phone = $service['client']['telephone'];
                        // } elseif ($service['client']['telephone2'] != null) {
                        //     $phone = $service['client']['telephone2'];
                        // } else {
                        //     $phone = $service['client']['mobile'];
                        // }
                        if ($service['techs']) {
                            $techs = explode(",", $service['techs']);
                            $technician_array = array();
                            foreach ($techs as $tech) {
                                $technician = User::where('id', $tech)->first();
                                array_push($technician_array, $technician['lastname']);
                            }

                            $technicians = implode(", ", $technician_array);
                        } else {
                            $technicians = "Ν/Α";
                        }

                        if ($service['appointment_start']) {
                            /*
                            $date_array = explode(".", $service['appointment_start']);
                            $newDateFormat = str_replace("T", " ", $date_array[0]);
                            $time_start = date("H:i", strtotime('+2 hours', strtotime($newDateFormat)));
                            */
                            $time_start = date("H:i", strtotime($service['appointment_start']));
                        } else {
                            $time_start = "?";
                        }

                        if ($service['appointment_end']) {
                            /*
                            $date_array_end = explode(".", $service['appointment_end']);
                            $newDateFormatEnd = str_replace("T", " ", $date_array_end[0]);
                            $time_end = date("H:i", strtotime('+2 hours', strtotime($newDateFormatEnd)));
                            */
                            $time_end = date("H:i", strtotime($service['appointment_end']));
                        } else {
                            $time_end = "?";
                        }

                        $client = $service['client']['lastname'] !== null ? $service['client']['lastname'] : "Ν/Α";
                        //$address = $service['client']['address'] !== null ? $service['client']['address'] : "N/A";  --> Removed 08/07/2020
                        $location = $service['client']['location'] !== null ? $service['client']['location'] : "Ν/Α";
                        //$status = $service['status']; --> Removed 08/07/2020


                        $html = "<div>";
                        $html .= "<b>Ωρα: </b>" . $time_start . " - " . $time_end . "<br>";
                        //$html .= "<b>Τύπος: </b>Σέρβις" . "<br>";  --> Removed 08/07/2020
                        $html .= $technicians == "" ? "" : "<b>Τεχνικοί: </b>" . $technicians . "<br>";
                        $html .= $client == "" ? "" : "<b>Πελάτης: </b>" . $client . "<br>";
                        //$html .= "<b>Κατάσταση: </b>" . $service['status'] . "<br>";  --> Removed 08/07/2020
                        //$html .= $address == "" ? "" : "<b>Διεύθυνση: </b>" . $address . "<br>";  --> Removed 08/07/2020
                        $html .= $location == "" ? "" : "<b>Περιοχή: </b>" . $location . "<br>";
                        $html .= "</div>";

                        return $html;

                        //return "Ωρα: " . $time_start . "-" . $time_end . " - " . "Τεχνικοι: " . $technicians . " - Πελάτης: " . $client . " - " . "Περιοχη: " . $location;
                    }

                    if ($this->note_id != null) {
                        $note = $this->calendarEntity;
                        //return Note::where('id', $this->note_id)->first()['title'];
                        if ($note['dateTime_start']) {
                            /*
                            $date_array = explode(".", $note['dateTime_start']);
                            $newDateFormat = str_replace("T", " ", $date_array[0]);
                            $time_start = date("H:i", strtotime('+2 hours', strtotime($newDateFormat)));
                            */
                            $time_start = date("H:i", strtotime($note['dateTime_start']));
                        } else {
                            $time_start = "?";
                        }

                        if ($note['dateTime_end']) {
                            /*
                            $date_array_end = explode(".", $note['dateTime_end']);
                            $newDateFormatEnd = str_replace("T", " ", $date_array_end[0]);
                            $time_end = date("H:i", strtotime('+2 hours', strtotime($newDateFormatEnd)));
                            */
                            $time_end = date("H:i", strtotime($note['dateTime_end']));
                        } else {
                            $time_end = "?";
                        }


                        $html = "<div>";
                        $html .= "<b>Ωρα: </b>" . $time_start . " - " . $time_end . "<br>";

                        $html .= $note['title'] == "" ? "" : "<b>Σημείωση: </b>" . $note['title'] . "<br>";
                        $html .= $note['location'] == "" ? "" : "<b>Περιοχή: </b>" . $note['location'] . "<br>";
                        $html .= "</div>";

                        return $html;
                    }
                }),
                "start" => $this->when($this->damage_id != null || $this->event_id != null || $this->note_id != null || $this->service_id != null, function () {
                    if ($this->damage_id != null) {
                        return $this->calendarEntity['appointment_start'];
                    }

                    if ($this->event_id != null) {
                        return $this->calendarEntity["event_start"];
                    }
                    if ($this->note_id != null) {
                        return $this->calendarEntity["dateTime_start"];
                    }
                    if ($this->service_id != null) {
                        return $this->calendarEntity["appointment_start"];
                    }
                }),
                "end" => $this->when($this->damage_id != null || $this->event_id != null || $this->note_id != null, function () {
                    if ($this->damage_id != null) {
                        return $this->calendarEntity['appointment_end'];
                    }

                    if ($this->event_id != null) {
                        return $this->calendarEntity["event_end"];
                    }
                    if ($this->note_id != null) {
                        return $this->calendarEntity["dateTime_end"];
                    }
                    if ($this->service_id != null) {
                        return $this->calendarEntity["appointment_end"];
                    }
                }),
                "all_day" => $this->when($this->note_id != null, function () {
                    return $this->calendarEntity["all_day"];
                }),
                "client_name" => $this->when($this->damage_id != null || $this->service_id != null, function () {
                    if ($this->damage_id != null) {
                        $current_damage = $this->calendarEntity;
                        $current_client = $current_damage['client'];
                        return $current_client['firstname'] . " " . $current_client['lastname'];
                    }

                    if ($this->service_id != null) {
                        $current_service = $this->calendarEntity;
                        $current_client = $current_service['client'];
                        return $current_client['firstname'] . " " . $current_client['lastname'];
                    }
                }),
                "client_address" => $this->when($this->damage_id != null || $this->service_id != null, function () {
                    if ($this->damage_id != null) {
                        $current_damage = $this->calendarEntity;
                        $current_client = $current_damage['client'];
                        //return $current_client['address'] . "," . $current_client['location'] . "," . $current_client['level'] . "ος Όροφος";
                        return $current_client['address'];
                    }

                    if ($this->service_id != null) {
                        $current_service = $this->calendarEntity;
                        $current_client = $current_service['client'];
                        //return $current_client['address'] . "," . $current_client['location'] . "," . $current_client['level'] . "ος Όροφος";
                        return $current_client['address'];
                    }
                }),
                "client_location" => $this->when($this->damage_id != null || $this->service_id != null, function () {
                    if ($this->damage_id != null) {
                        $current_damage = $this->calendarEntity;
                        $current_client = $current_damage['client'];
                        //return $current_client['address'] . "," . $current_client['location'] . "," . $current_client['level'] . "ος Όροφος";
                        return $current_client['location'];
                    }

                    if ($this->service_id != null) {
                        $current_service = $this->calendarEntity;
                        $current_client = $current_service['client'];
                        //return $current_client['address'] . "," . $current_client['location'] . "," . $current_client['level'] . "ος Όροφος";
                        return $current_client['location'];
                    }
                }),
                "client_level" =>   $this->when($this->damage_id != null || $this->service_id != null, function () {
                    if ($this->damage_id != null) {
                        $current_damage = $this->calendarEntity;
                        $current_client = $current_damage['client'];
                        //return $current_client['address'] . "," . $current_client['location'] . "," . $current_client['level'] . "ος Όροφος";
                        return $current_client['level'];
                    }

                    if ($this->service_id != null) {
                        $current_service = $this->calendarEntity;
                        $current_client = $current_service['client'];
                        //return $current_client['address'] . "," . $current_client['location'] . "," . $current_client['level'] . "ος Όροφος";
                        return $current_client['level'];
                    }
                }),
                "client_zipcode" =>   $this->when($this->damage_id != null || $this->service_id != null, function () {
                    if ($this->damage_id != null) {
                        $current_damage = $this->calendarEntity;
                        $current_client = $current_damage['client'];
                        //return $current_client['address'] . "," . $current_client['location'] . "," . $current_client['level'] . "ος Όροφος";
                        return $current_client['zipcode'];
                    }

                    if ($this->service_id != null) {
                        $current_service = $this->calendarEntity;
                        $current_client = $current_service['client'];
                        //return $current_client['address'] . "," . $current_client['location'] . "," . $current_client['level'] . "ος Όροφος";
                        return $current_client['zipcode'];
                    }
                }),
                "client_telephone" => $this->when($this->damage_id != null || $this->service_id != null, function () {
                    if ($this->damage_id != null) {
                        $current_damage = $this->calendarEntity;
                        $current_client = $current_damage['client'];
                        $tel_array = array();
                        if ($current_client['telephone'] != null || $current_client['telephone'] != "") {
                            array_push($tel_array, $current_client['telephone']);
                        }

                        if ($current_client['telephone2'] != null || $current_client['telephone2'] != "") {
                            array_push($tel_array, $current_client['telephone2']);
                        }

                        if ($current_client['mobile'] != null || $current_client['mobile'] != "") {
                            array_push($tel_array, $current_client['mobile']);
                        }

                        $phone_numbers = implode(", ", $tel_array);


                        return $phone_numbers;
                    }

                    if ($this->service_id != null) {
                        $current_service = $this->calendarEntity;
                        $current_client = $current_service['client'];
                        $tel_array = array();
                        if ($current_client['telephone'] != null || $current_client['telephone'] != "") {
                            array_push($tel_array, $current_client['telephone']);
                        }

                        if ($current_client['telephone2'] != null || $current_client['telephone2'] != "") {
                            array_push($tel_array, $current_client['telephone2']);
                        }

                        if ($current_client['mobile'] != null || $current_client['mobile'] != "") {
                            array_push($tel_array, $current_client['mobile']);
                        }

                        $phone_numbers = implode(", ", $tel_array);

                        return $phone_numbers;
                    }
                }),
                "status" => $this->when($this->damage_id != null || $this->service_id != null, function () {
                    if ($this->damage_id != null) {
                        $current_damage = $this->calendarEntity;

                        //$information = new \stdClass();
                        return  $current_damage["status"];
                    }

                    if ($this->service_id != null) {
                        $current_service = $this->calendarEntity;

                        //$information = new \stdClass();
                        return  $current_service["status"];
                    }
                }),
                "appointment_pending" => $this->when($this->damage_id || $this->service_id, function () {
                    if ($this->damage_id != null) {
                        $current_damage = $this->calendarEntity;

                        //$information = new \stdClass();
                        return  $current_damage["appointment_pending"];
                    }

                    if ($this->service_id != null) {
                        $current_service = $this->calendarEntity;

                        //$information = new \stdClass();
                        return  $current_service["appointment_pending"];
                    }
                }),
                "event_type" => $this->when($this->damage_id || $this->service_id, function () {
                    if ($this->damage_id != null) {
                        $current_damage = $this->calendarEntity;

                        //$information = new \stdClass();
                        return  DamageType::where("id", $current_damage["damage_type_id"])->first()["name"];
                    }

                    if ($this->service_id != null) {
                        $current_service = $this->calendarEntity;

                        //$information = new \stdClass();
                        //return  $current_service["appointment_pending"];
                        return  DamageType::where("id", $current_service["service_type_id2"])->first()["name"];
                    }
                }),
                "techs" => $this->when($this->damage_id || $this->service_id, function () {
                    if ($this->damage_id != null) {
                        $current = $this->calendarEntity;
                    } else {
                        $current = $this->calendarEntity;
                    }

                    if (!$current["techs"]) {
                        return "";
                    }
                    $techs = explode(",", $current["techs"]);
                    $technician_array = array();

                    foreach ($techs as $tech) {
                        $technician = User::where("id", $tech)->first();
                        array_push($technician_array, $technician['firstname'] . " " . $technician["lastname"]);
                    }
                    return implode(", ", $technician_array);
                    //$information = new \stdClass();

                }),
                "devices" =>  $this->when($this->damage_id || $this->service_id, function () {
                    if ($this->damage_id != null) {
                        $current = $this->calendarEntity;
                    } else {
                        $current = $this->calendarEntity;
                    }

                    if (!$current["marks"]) {
                        return "";
                    }
                    $devices = explode(",", $current["marks"]);
                    $device_array = array();

                    foreach ($devices as $device) {
                        $dev = Mark::where("id", $device)->first();
                        array_push($device_array, $dev['manufacturer']['name'] . "/" . $dev["name"]);
                    }
                    return implode(", ", $device_array);
                    //$information = new \stdClass();

                }),
                "appointment_pending" => $this->when($this->damage_id || $this->service_id, function () {
                    if ($this->damage_id) {
                        $current = $this->calendarEntity;
                    } else {
                        $current = $this->calendarEntity;
                    }

                    $appointment_pending = $current["appointment_pending"] == true ? "ΝΑΙ" : "ΟΧΙ";
                    return $appointment_pending;
                }),
                // $information->appointment_pending = $current_damage["appointment_pending"] == true ? "ΝΑΙ" : "ΟΧΙ";
                // //$information->device = Device::where("id", $current_damage["device_id"])->first()['name'];
                // $information->comments = $current_damage['damage_comments'] != null ? $current_damage['damage_comments'] : "";
                "task_comments" => $this->when($this->damage_id || $this->service_id, function () {
                    if ($this->damage_id) {
                        $current = $this->calendarEntity;
                        $response =  $current["damage_comments"];
                    } else {
                        $current = $this->calendarEntity;
                        $response =  $current["service_comments"];
                    }

                    if ($response) {
                        return $response;
                    } else {
                        return "";
                    }
                }),
                "textColor" => $this->when($this->note_id != null || $this->damage_id != null || $this->service_id != null, function () {
                    if ($this->damage_id != null) {
                        $dmg = $this->calendarEntity;
                        /*commented out on 22052020
                        if ($dmg['status'] != "Μη Ολοκληρωμένη") {
                            return "#ff0000";
                        } else {
                            return "#ffffff";
                        }
                        */
                        if ($dmg['status'] == "Ακυρώθηκε") {
                            return "#ffffff";
                        } else {
                            return "#000000";
                        }
                    }
                    if ($this->service_id != null) {

                        $service = $this->calendarEntity;
                        /*commented out on 22052020
                        if ($service['status'] != "Μη Ολοκληρωμένο") {
                            return "#ff0000";
                        } else {
                            return "#ffffff";
                        }
                        */
                        if ($service['status'] == "Ακυρώθηκε") {
                            return "#ffffff";
                        } else {
                            return "#000000";
                        }
                    }
                }),
                "color" => $this->when($this->note_id != null || $this->damage_id != null || $this->service_id != null, function () {
                    if ($this->note_id != null) {
                        $importance = $this->calendarEntity["importance"];
                        switch ($importance) {
                            case 0:
                                return "#ff0000";
                                break;
                            case 1:
                                return "#ffa500";
                                break;
                            case 2:
                                return "#008000";
                                break;
                            default:
                                return null;
                                break;
                        }
                    }
                    if ($this->damage_id != null) {
                        $dmg = $this->calendarEntity;
                        /*
                        $appointment_start  = $dmg['appointment_start'];
                        $app_start_array = explode('.', $appointment_start);
                        $formatted_appointment = str_replace('T', ' ', $app_start_array[0]);
                        $date_to_compare = strtotime($formatted_appointment) + 2 * 60 * 60;
                        if (time() - $date_to_compare > 0 && $dmg['status'] != "Ολοκληρώθηκε") {
                            return "#ff0000";
                        } else {
                            return "#5d5fea";
                        }*/
                        if ($dmg['status'] == "Ολοκληρώθηκε") {
                            return "#ff0000";
                        }

                        if ($dmg['status'] == "Μη Ολοκληρωμένη") {
                            return "#3ee110";
                        }

                        if ($dmg['status'] == "Ακυρώθηκε") {
                            return "#000000";
                        }
                    }
                    if ($this->service_id != null) {
                        $dmg = $this->calendarEntity;
                        /*
                        $appointment_start  = $dmg['appointment_start'];
                        $app_start_array = explode('.', $appointment_start);
                        $formatted_appointment = str_replace('T', ' ', $app_start_array[0]);
                        $date_to_compare = strtotime($formatted_appointment) + 2 * 60 * 60;
                        if (time() - $date_to_compare > 0 && $dmg['status'] != "Ολοκληρώθηκε") {
                            return "#ff0000";
                        } else {
                            return "#bd391b";
                        }
                        */
                        if ($dmg['status'] == "Ολοκληρώθηκε") {
                            return "#ff0000";
                        }

                        if ($dmg['status'] == "Μη Ολοκληρωμένο") {
                            return "#3ee110";
                        }

                        if ($dmg['status'] == "Ακυρώθηκε") {
                            return "#000000";
                        }
                    }
                })
            ];
    }
}
