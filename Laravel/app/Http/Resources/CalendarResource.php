<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Damage;
use App\Http\CustomClasses\v1\CalendarClass;

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
            "title" =>$this->when($this->service_id != null, function(){ return Damage::where('id',$this->damage_id)->first()->type->name;}),
            "title" =>$this->when($this->offer_id != null, function(){ return Damage::where('id',$this->damage_id)->first()->type->name;}),
            "title" =>$this->when($this->damage_id != null, function(){ return Damage::where('id',$this->damage_id)->first()->type->name;}),
            "date" =>$this->when($this->service_id != null, function(){ return Damage::where('id',$this->damage_id)->first()->appointment_start;}),
            "date" =>$this->when($this->offer_id != null, function(){ return Damage::where('id',$this->damage_id)->first()->appointment_start;}),
            "date" =>$this->when($this->damage_id != null, function(){ return Damage::where('id',$this->damage_id)->first()->appointment_start;})

        ];
    }
}
