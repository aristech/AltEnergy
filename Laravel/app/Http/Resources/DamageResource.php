<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
        return [
            "id" => $this->id,
            "damage_type" => $this->type['name'],
            "damage_comments" => $this->damage_comments,
            "cost" => $this->cost,
            "guarantee" => $this->guarantee,
            "status" => $this->status,
            "estimation_appointment" => $this->estimation_appointment,
            "cost_information" => $this->cost_information,
            "supplement_available" => $this->supplement_available,
            "fixing_appointment" => $this->fixing_appointment,
            "damage_fixed" => $this->damage_fixed,
            "damage_paid" => $this->damage_paid,
            "client_id" => $this->client_id,
            "client_lastname" => $this->client['lastname'],
            "client_firstname" => $this->client['firstname'],
            "manufacturer_id" => $this->manufacturer_id,
            "manufacturer" => $this->device['mark']['manufacturer']['name'],
            "mark_id" => $this->mark_id,
            "mark" => $this->device['mark']['name'],
            "device_id" => $this->device_id,
            "device" => $this->device['name'],
            "supplement" => $this->supplement,
            "comments" => $this->comments,
            "repeatable" => $this->repeatable,
            "repeat_frequency" => $this->repeat_frequency,
            "repeat_type" => $this->repeat_type,
            "appointment_start" => $this->appointment_start,
            "appointment_end" => $this->appointment_end,
            "user_id" => $this->user_id,
            "user" => $this->user['lastname']
        ];
    }
}
