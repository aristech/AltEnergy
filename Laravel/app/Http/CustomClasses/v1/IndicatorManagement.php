<?php

namespace App\Http\CustomClasses\v1;

use App\Damage;
use App\Service;
use App\Eventt;
use App\Client;

class IndicatorManagement
{
    public $indications = array();

    public function getDamageIndicators()
    {
        $damages = Damage::where('status', 'Μη ολοκληρωμένη')->where('appointment_completed', false)->where('appointment_start', '!=', null)->get();
        foreach ($damages as $damage) {
            $appointment_start = $damage["appointment_start"];
            $appointment_start_timestamp = strtotime($appointment_start);
            $appointment_start_now = strtotime("now");

            $appointment_transformation = str_replace('T', ' ', $appointment_start);
            $appointment_trans_2 = explode('.', $appointment_transformation);
            $app = strtotime($appointment_trans_2[0]);


            $client = Client::where('id', $damage['client_id'])->first();
            //$client_fullname = $client['firstname']." ".$client["lastname"];
            //start check fullname
            if ($client['firstname'] && $client['lastname']) {
                $client_fullname = $client['firstname'] . " " . $client['lastname'];
            }

            if (!$client['firstname'] && !$client['lastname']) {
                $client_fullname = "";
            }

            if (!$client['firstname'] && $client['lastname']) {
                $client_fullname = $client['lastname'];
            }

            if ($client['firstname'] && !$client['lastname']) {
                $client_fullname = $client['firstname'];
            }
            //end

            if ($appointment_start_now - $app > 0) {
                $obj = new \stdClass();
                $obj->type = "damages";
                $obj->displaytype = "βλαβη";
                $obj->title = $damage['type']['name'];
                $obj->id = $damage['id'];
                $obj->client = $client_fullname;
                $obj->address = $client['address'];
                $obj->delayed_date = date("F j, Y, g:i a", $app);

                array_push($this->indications, $obj);
            }
        }
    }

    public function getServiceIndicators()
    {
        $services = Service::where('status', 'Μη ολοκληρωμένο')->where('appointment_start', '!=', null)->get();
        foreach ($services as $service) {
            $service_start = $service["appointment_start"];
            $event_start_timestamp = strtotime($service_start);
            $service_start_now = strtotime("now");

            $service_transformation = str_replace('T', ' ', $service_start);
            $service_trans = explode('.', $service_transformation);
            $service_time = strtotime($service_trans[0]);

            $client = Client::where('id', $service['client_id'])->first();
            //$client_fullname = $client['firstname']." ".$client["lastname"];
            //start check fullname
            if ($client['firstname'] && $client['lastname']) {
                $client_fullname = $client['firstname'] . " " . $client['lastname'];
            }

            if (!$client['firstname'] && !$client['lastname']) {
                $client_fullname = "";
            }

            if (!$client['firstname'] && $client['lastname']) {
                $client_fullname = $client['lastname'];
            }

            if ($client['firstname'] && !$client['lastname']) {
                $client_fullname = $client['firstname'];
            }
            //end

            if ($service_start_now - $service_time > 0) {
                $obj = new \stdClass();
                $obj->type = "services";
                $obj->id = $service['id'];
                $obj->title = $service['type']['name'];
                $obj->displaytype = "σερβις";
                $obj->client = $client_fullname;
                $obj->address = $client['address'];
                $obj->delayed_date = date("F j, Y, g:i a", $service_time);

                array_push($this->indications, $obj);
            }
        }
    }

    public function getEventIndicators()
    {
        $events = Eventt::where('status', 'Μη ολοκληρωμένο')->where('event_start', '!=', null)->get();
        foreach ($events as $event) {
            $event_start = $event["event_start"];
            $event_start_timestamp = strtotime($event_start);
            $event_start_now = strtotime("now");

            $event_transformation = str_replace('T', ' ', $event_start);
            $event_trans = explode('.', $event_transformation);
            $event_time = strtotime($event_trans[0]);

            if ($event_start_now - $event > 0) {
                $obj = new \stdClass();
                $obj->type = "events";
                $obj->title = $event['title'];
                $obj->displaytype = "task/λοιπα";
                $obj->id = $event['id'];
                $obj->delayed_date = date("F j, Y, g:i a", $event_time);

                array_push($this->indications, $obj);
            }
        }
    }
}
