<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MarkResource extends JsonResource
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
            "name" => $this->name,
            "manufacturer_id" => $this->manufacturer['id'],
            "manufacturer_name" => $this->manufacturer['name']

        ];
    }
}
