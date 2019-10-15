<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TechResource extends JsonResource
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
            "fullname" => $this->firstname." ".$this->lastname,
            "email"=> $this->email,
            "telephone"=> $this->telephone,
            "telephone2"=> $this->telephone2,
            "mobile"=> $this->mobile
        ];
    }
}
