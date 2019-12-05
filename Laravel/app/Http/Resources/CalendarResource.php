<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Damage;
use App\Eventt;
use App\Service;
//use App\Http\CustomClasses\v1\CalendarClass;
use App\Note;

class CalendarResource extends JsonResource
{
    public $calendar;

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
                        $damage = Damage::where('id', $this->damage_id)->first();
                        if ($damage['client']['telephone'] != null) {
                            $phone = $damage['client']['telephone'];
                        } elseif ($damage['client']['telephone2'] != null) {
                            $phone = $damage['client']['telephone2'];
                        } else {
                            $phone = $damage['client']['mobile'];
                        }
                        return $damage['type']['name'] . " - " . $damage['client']['firstname'] . " " . $damage['client']['lastname'] . " - " . $phone;
                    }

                    if ($this->event_id != null) {
                        return Eventt::where('id', $this->event_id)->first()['title'];
                    }

                    if ($this->service_id != null) {
                        $service = Service::where('id', $this->service_id)->first();
                        if ($service['client']['telephone'] != null) {
                            $phone = $service['client']['telephone'];
                        } elseif ($service['client']['telephone2'] != null) {
                            $phone = $service['client']['telephone2'];
                        } else {
                            $phone = $service['client']['mobile'];
                        }
                        return $service['type']['name'] . " - " . $service['client']['firstname'] . " " . $service['client']['lastname'] . " - " . $phone;
                    }

                    if ($this->note_id != null) {
                        return Note::where('id', $this->note_id)->first()['title'];
                    }
                }),
                "start" => $this->when($this->damage_id != null || $this->event_id != null || $this->note_id != null || $this->service_id != null, function () {
                    if ($this->damage_id != null) {
                        return Damage::where('id', $this->damage_id)->first()['appointment_start'];
                    }

                    if ($this->event_id != null) {
                        return Eventt::where('id', $this->event_id)->first()["event_start"];
                    }
                    if ($this->note_id != null) {
                        return Note::where('id', $this->note_id)->first()["dateTime_start"];
                    }
                    if ($this->service_id != null) {
                        return Service::where('id', $this->service_id)->first()["appointment_start"];
                    }
                }),
                "end" => $this->when($this->damage_id != null || $this->event_id != null || $this->note_id != null, function () {
                    if ($this->damage_id != null) {
                        return Damage::where('id', $this->damage_id)->first()['appointment_end'];
                    }

                    if ($this->event_id != null) {
                        return Eventt::where('id', $this->event_id)->first()["event_end"];
                    }
                    if ($this->note_id != null) {
                        return Note::where('id', $this->note_id)->first()["dateTime_end"];
                    }
                    if ($this->service_id != null) {
                        return Service::where('id', $this->service_id)->first()["appointment_end"];
                    }
                }),
                "all_day" => $this->when($this->note_id != null, function () {
                    return Note::where('id', $this->note_id)->first()["all_day"];
                }),
                "client_name" => $this->when($this->damage_id != null || $this->service_id != null, function () {
                    if ($this->damage_id != null) {
                        $current_damage = Damage::where('id', $this->damage_id)->first();
                        $current_client = $current_damage['client'];
                        return $current_client['firstname'] . " " . $current_client['lastname'];
                    }

                    if ($this->service_id != null) {
                        $current_service = Service::where('id', $this->service_id)->first();
                        $current_client = $current_service['client'];
                        return $current_client['firstname'] . " " . $current_client['lastname'];
                    }
                }),
                "client_address" => $this->when($this->damage_id != null || $this->service_id != null, function () {
                    if ($this->damage_id != null) {
                        $current_damage = Damage::where('id', $this->damage_id)->first();
                        $current_client = $current_damage['client'];
                        return $current_client['address'] . "," . $current_client['location'] . "," . $current_client['level'] . "ος Όροφος";
                    }

                    if ($this->service_id != null) {
                        $current_service = Service::where('id', $this->service_id)->first();
                        $current_client = $current_service['client'];
                        return $current_client['address'] . "," . $current_client['location'] . "," . $current_client['level'] . "ος Όροφος";
                    }
                }),
                "client_telephone" => $this->when($this->damage_id != null || $this->service_id != null, function () {
                    if ($this->damage_id != null) {
                        $current_damage = Damage::where('id', $this->damage_id)->first();
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
                        $current_service = Service::where('id', $this->service_id)->first();
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

                // "startRecur" => $this->when($this->service_id != null, function()
                // {
                //     return Service::where('id',$this->service_id)->first()['appointment_start'];
                // }),
                // "endRecur" => $this->when($this->service_id != null, function()
                // {
                //     return Service::where('id',$this->service_id)->first()['appointment_end'];
                // }),
                // "frequency" => $this->when($this->service_id != null , function()
                // {
                //     $service = Service::where('repeatable',true)->get()->first();
                //     return $service['frequency'];
                // })
                "color" => $this->when($this->note_id != null || $this->damage_id != null || $this->service_id != null, function () {
                    if ($this->note_id != null) {
                        $importance = Note::where('id', $this->note_id)->first()["importance"];
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
                        return "#5d5fea";
                    }
                    if ($this->service_id != null) {
                        return "#bd391b";
                    }
                })
            ];
    }
}
