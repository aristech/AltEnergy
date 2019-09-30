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
            "appointment_pending" => $this->appointment_pending,
            "technician_left" => $this->technician_left,
            "technician_arrived" => $this->technician_arrived,
            "appointment_completed" => $this->appointment_completed,
            "appointment_needed" => $this->appointment_needed,
            "supplement_pending" => $this->supplement_pending,
            "damage_fixed" => $this->damage_fixed,
            "completed_no_transaction" => $this->completed_no_transaction,
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
            "appointment_start" => $this->appointment_start,
            "appointment_end" => $this->appointment_end,
            "user_id" => $this->user_id,
            "user" => $this->user['lastname']
        ];
    }
}
