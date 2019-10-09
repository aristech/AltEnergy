<?php

namespace App\Http\CustomClasses\v1;

use App\Damage;

class CalendarClass
{
    private $id = null;
    private $damage_id = null;
    private $service_id = null;
    private $offer_id = null;


    public function __construct($id, $damage_id, $service_id, $offer_id)
    {
        $this->id = $id;
        $this->damage_id = $damage_id;
        $this->service_id = $service_id;
        $this->offer_id = $offer_id;
    }

    public function eventName()
    {
        $damage = Damage::where('id',$this->damage_id)->where('status','Μη Ολοκληρωμένη')->first();
        return $damage['type']['name'];
    }
}
