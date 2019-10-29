<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Damage;
use App\Eventt;
use App\Service;
use App\Http\CustomClasses\v1\CalendarClass;
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
            "event_id" => $this->when($this->damage_id != null || $this->service_id != null || $this->note_id != null, function()
            {
                if($this->damage_id != null)
                {
                    return $this->damage_id;
                }

                if($this->event_id)
                {
                    return $this->event_id;
                }

                if($this->service_id != null)
                {
                    return $this->service_id;
                }

                if($this->note_id != null)
                {
                    return $this->note_id;
                }
            }),
            "title" => $this->when($this->service_id != null || $this->damage_id != null || $this->service_id != null || $this->note_id != null , function()
            {
                if($this->damage_id != null)
                {
                    return Damage::where('id',$this->damage_id)->first()['type']['name'];
                }

                if($this->event_id != null)
                {
                    return Eventt::where('id',$this->event_id)->first()['title'];
                }

                if($this->service_id != null)
                {
                    return Service::where('id',$this->service_id)->first()['type']['name'];
                }

                if($this->note_id != null)
                {
                    return Note::where('id',$this->note_id)->first()['title'];
                }
            }),
            "start" => $this->when($this->damage_id != null || $this->event_id != null || $this->note_id != null , function()
            {
                if($this->damage_id != null)
                {
                    return Damage::where('id',$this->damage_id)->first()['appointment_start'];
                }

                if($this->event_id != null)
                {
                    return Eventt::where('id',$this->event_id)->first()["event_start"];
                }
                if($this->note_id != null)
                {
                    return Note::where('id',$this->note_id)->first()["dateTime_start"];
                }

            }),
            "end" => $this->when($this->damage_id != null || $this->event_id != null || $this->note_id != null, function()
            {
                if($this->damage_id != null)
                {
                   return Damage::where('id',$this->damage_id)->first()['appointment_end'];
                }

                if($this->event_id != null)
                {
                   return Eventt::where('id',$this->event_id)->first()["event_end"];
                }
                if($this->note_id != null)
                {
                    return Note::where('id',$this->note_id)->first()["dateTime_end"];
                }

            }),
            "all_day" => $this->when($this->note_id != null, function()
            {
                return Note::where('id',$this->note_id)->first()["all_day"];
            })
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
        ];
    }
}
