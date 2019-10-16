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
        $damages = Damage::where('status','Μη ολοκληρωμένη')->where('appointment_completed',false)->where('appointment_start','!=',null)->get();
        foreach($damages as $damage)
        {
            $appointment_start = $damage["appointment_start"];
            $appointment_start_timestamp = strtotime($appointment_start);
            $appointment_start_now = strtotime("now");

            $client = Client::where('id',$damage['client_id'])->first();
            $client_fullname = $client['firstname']." ".$client["lastname"];

            if($appointment_start_now - $appointment_start_timestamp > 0)
            {
                $obj = new \stdClass();
                $obj->type = "damages";
                $obj->displaytype = "βλαβη";
                $obj->title = $damage['type']['name'];
                $obj->id = $damage['id'];
                $obj->client = $client_fullname;
                $obj->address = $client['address'];
                $obj->delayed_date = $damage['appointment_start'];

                array_push($this->indications, $obj);
            }
        }
    }

    public function getEventIndicators()
    {
        $events = Eventt::where('status','Μη ολοκληρωμένο')->where('event_start','!=',null)->get();
        foreach($events as $event)
        {
            $event_start = $event["event_start"];
            $event_start_timestamp = strtotime($event_start);
            $event_start_now = strtotime("now");

            if($event_start_now - $event_start_timestamp > 0)
            {
                $obj = new \stdClass();
                $obj->type = "events";
                $obj->title = $event['title'];
                $obj->displaytype = "task/λοιπα";
                $obj->id = $event['id'];
                $obj->name = $event['type']['name'];
                $obj->delayed_date = $event_start;

                array_push($this->indications, $obj);
            }
        }
    }

}
