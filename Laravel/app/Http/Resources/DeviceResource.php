<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeviceResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            "mark_id" => $this->mark->id,
            "mark_name" => $this->mark['name'],
            "manufacturer_id" => $this->mark['manufacturer']['id'],
            "manufacturer_name" => $this->mark['manufacturer']['name']
        ];
    }
}
