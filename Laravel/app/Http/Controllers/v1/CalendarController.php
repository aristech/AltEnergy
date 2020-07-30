<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\CalendarResource;
use App\Calendar;
use App\Damage;
use App\Service;
use App\User;
use App\Mark;
use App\Http\CustomClasses\v1\AuthorityClass;

class CalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $highest_role = AuthorityClass::getAuthorityLevel($request);
        //if ($request->user()->role()->first()->id >= 3) {
        if ($highest_role >= 3) {
            //return CalendarResource::collection(Calendar::all()); commented out for removing notes
            $collection = CalendarResource::collection(Calendar::where('note_id', null)->get());
            $calendar = collect($collection)->filter()->values()->all();
            return response()->json(['data' => $calendar]);
        } elseif ($highest_role == 2) {
            //} elseif ($request->user()->role()->first()->id == 2) {
            //return response()->json(array(2, 3);
            $calendar = Calendar::where('id', '!=', null)->get();
            $calendar_array = array();
            foreach ($calendar as $entry) {
                $manager_obj = new \stdClass();
                if ($entry['damage_id'] != null) {
                    $damage = Damage::where('id', $entry['damage_id'])->first();
                    if ($damage['client']['manager']['id'] == $request->user()->manager_id) {
                        $manager_obj->id = $entry['id'];
                        $manager_obj->type = $entry['type'];
                        $manager_obj->name = $entry['name'];
                        $manager_obj->event_id = $entry['damage_id'];
                        if ($damage['client']['telephone'] != null) {
                            $phone = $damage['client']['telephone'];
                        } elseif ($damage['client']['telephone2'] != null) {
                            $phone = $damage['client']['telephone2'];
                        } else {
                            $phone = $damage['client']['mobile'];
                        }
                        $manager_obj->title =  $damage['type']['name'] . " - " . $damage['client']['firstname'] . " " . $damage['client']['lastname'] . " - " . $phone;
                        $manager_obj->start = $damage['appointment_start'];
                        $manager_obj->end = $damage['appointment_end'];
                        $manager_obj->client_name = $damage['client']['firstname'] . " " . $damage['client']['lastname'];
                        //$manager_obj->client_address = $damage['client']['address'] . "," . $damage['client']['location'] . "," . $damage['client']['level'] . "ος Όροφος";
                        $manager_obj->client_address = $damage['client']['address'];
                        $manager_obj->client_location = $damage['client']['location'];
                        $manager_obj->client_level = $damage['client']['level'];
                        $manager_obj->client_zipcode = $damage['client']['zipcode'];

                        $tel_array = array();
                        if ($damage['client']['telephone'] != null || $damage['client']['telephone'] != "") {
                            array_push($tel_array, $damage['client']['telephone']);
                        }

                        if ($damage['client']['telephone2'] != null || $damage['client']['telephone2'] != "") {
                            array_push($tel_array, $damage['client']['telephone2']);
                        }

                        if ($damage['client']['mobile'] != null || $damage['client']['mobile'] != "") {
                            array_push($tel_array, $damage['client']['mobile']);
                        }

                        $phone_numbers = implode(", ", $tel_array);

                        $temp_techs = $damage['techs'];
                        $temp_tech_array = explode(',', $temp_techs);
                        $tech_array = array();
                        foreach ($temp_tech_array as $temp_tech) {
                            $technician = User::where('id', $temp_tech)->first();
                            array_push($tech_array, $technician['firstname'] . " " . $technician['lastname']);
                        }
                        $manager_obj->techs = implode(', ', $tech_array);
                        $manager_obj->appointment_pending = $damage['appointment_pending'] == true ? "NAI" : "OXI";
                        $manager_obj->task_comments = $damage['damage_comments'] != null ? $damage['damage_comments'] : "";

                        $manager_obj->client_telephone = $phone_numbers;
                        $manager_obj->color = "#5d5fea";

                        $manager_obj->status = $damage['status'];

                        $temp_devices = $damage['marks'];
                        if ($temp_devices) {
                            $temp_device_array = explode(',', $temp_devices);
                            $device_array = array();
                            foreach ($temp_device_array as $device) {
                                $dev = Mark::where('id', $device)->first();
                                array_push($device_array, $dev['manufacturer']['name'] . "/" . $dev['name']);
                            }
                            $manager_obj->devices = implode(',', $device_array);
                        } else {
                            $manager_obj->devices = "";
                        }




                        array_push($calendar_array, $manager_obj);
                    }
                } elseif ($entry['service_id'] != null) {
                    $service = Service::where('id', $entry['service_id'])->first();
                    if ($service['client']['manager']['id'] == $request->user()->manager_id) {
                        $manager_obj->id = $entry['id'];
                        $manager_obj->type = $entry['type'];
                        $manager_obj->name = $entry['name'];
                        $manager_obj->event_id = $entry['service_id'];
                        if ($service['client']['telephone'] != null) {
                            $phone = $service['client']['telephone'];
                        } elseif ($service['client']['telephone2'] != null) {
                            $phone = $service['client']['telephone2'];
                        } else {
                            $phone = $service['client']['mobile'];
                        }
                        $manager_obj->title =  $service['type']['name'] . " - " . $service['client']['firstname'] . " " . $service['client']['lastname'] . " - " . $phone;
                        $manager_obj->start = $service['appointment_start'];
                        $manager_obj->end = $service['appointment_end'];
                        $manager_obj->client_name = $service['client']['firstname'] . " " . $service['client']['lastname'];
                        //$manager_obj->client_address = $service['client']['address'] . "," . $service['client']['location'] . "," . $service['client']['level'] . "ος Όροφος";
                        $manager_obj->client_address = $service['client']['address'];
                        $manager_obj->client_location = $service['client']['location'];
                        $manager_obj->client_zipcode = $service['client']['zipcode'];


                        $tel_array = array();
                        if ($service['client']['telephone'] != null || $service['client']['telephone'] != "") {
                            array_push($tel_array, $service['client']['telephone']);
                        }

                        if ($service['client']['telephone2'] != null || $service['client']['telephone2'] != "") {
                            array_push($tel_array, $service['client']['telephone2']);
                        }

                        if ($service['client']['mobile'] != null || $service['client']['mobile'] != "") {
                            array_push($tel_array, $service['client']['mobile']);
                        }

                        $phone_numbers = implode(", ", $tel_array);

                        $manager_obj->client_telephone = $phone_numbers;

                        $temp_techs = $service['techs'];
                        if ($temp_techs) {
                            $tech_array = array();
                            $temp_tech_array = explode(',', $temp_techs);
                            foreach ($temp_tech_array  as $tech) {
                                $technician = User::where('id', $tech)->first();
                                array_push($tech_array, $technician['firstname'] . " " . $technician['lastname']);
                            }
                            $manager_obj->techs = implode(', ', $tech_array);
                        } else {
                            $manager_obj->techs = "";
                        }

                        $temp_devices = $service['marks'];
                        if ($temp_devices) {
                            $tech_array = array();
                            $temp_tech_array = explode(',', $temp_techs);
                            foreach ($temp_tech_array as $tech) {
                                $technician = User::where('id', $tech)->first();
                                array_push($tech_array, $technician['firstname'] . " " . $technician['lastname']);
                            }
                            $manager_obj->techs = implode(', ', $tech_array);
                        } else {
                            $manager_obj->techs = "";
                        }

                        $manager_obj->status = $service['status'];
                        $manager_obj->appointment_pending = $service['appointment_pending'] == true ? "NAI" : "OXI";
                        $manager_obj->task_comments = $service['service_comments'] != null ? $service['service_comments'] : "";


                        $manager_obj->color = "#bd391b";

                        array_push($calendar_array, $manager_obj);
                    }
                }
            }
            return response()->json(['data' => $calendar_array]);
        } else {
            return response()->json(["message" => "Ο χρήστης αυτός δεν μπορεί να έχει πρόσβαση στο ημερολόγιο"], 401);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // $type = $request->type;
        // $id = $request->event_id;
        // $start = $request->start;
        // $end = $request->end;

        // switch ($type) {
        //     case "damages":
        //        $damage = Damage::find($id);
        //        if(!$damage)
        //        {
        //           return response()->json(["message" => "Δεν βρέθηκε η βλάβη"],404);
        //        }
        //        $damage->update(["appointment_start"=>$start,"appointment_end"=>$end]);
        //        return response()->json(["message" => "Εγινε μετακίνηση στο ραντεβού για τις ".$end],200);
        //        break;
        //     case "events":
        //         code to be executed if n=label2;
        //         break;
        //     case label3:
        //         code to be executed if n=label3;
        //         break;
        //     ...
        //     default:
        //         return response()->json(["message" => "Δεν επιτρέπεται η ενέργεια"],401);
        // }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
