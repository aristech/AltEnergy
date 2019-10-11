<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
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
            "service_type" => $this->type['name'],
            "service_comments" => $this->damage_comments,
            "cost" => $this->cost,
            "guarantee" => $this->guarantee,
            "status" => $this->status,
            "appointment_pending" => $this->appointment_pending,
            "technician_left" => $this->technician_left,
            "technician_arrived" => $this->technician_arrived,
            "appointment_completed" => $this->appointment_completed,
            "appointment_needed" => $this->appointment_needed,
            "supplement_pending" => $this->supplement_pending,
            "service_done" => $this->service_done,
            "completed_no_transaction" => $this->completed_no_transaction,
            "client_id" => $this->client_id,
            "client_lastname" => $this->client['lastname'],
            "client_firstname" => $this->client['firstname'],
            "client_address" => $this->client['address'],
            "client_phone" =>  $this->when(true, function () {
                if($this->client['telephone'] != null) return $this->client['telephone'];
                if($this->client['telephone2'] != null) return $this->client['telephone2'];
                if($this->client['mobile'] != null) return $this->client['mobile'];
            }),
            "manufacturer_id" => $this->manufacturer_id,
            "manufacturer" => $this->device['mark']['manufacturer']['name'],
            "mark_id" => $this->mark_id,
            "mark" => $this->device['mark']['name'],
            "device_id" => $this->device_id,
            "device" => $this->device['name'],
            "supplements" => $this->supplements,
            "comments" => $this->comments,
            "appointment_start" => $this->appointment_start,
            "appointment_end" => $this->appointment_end,
            "user_id" => $this->user_id,
            "user" => $this->user['lastname'],
            "repeatable" => $this->repeatable,
            "frequency" => $this->frequency
        ];
    }
}
