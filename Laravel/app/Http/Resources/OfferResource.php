<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
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
            "client_id" => $this->client_id,
            "client_lastname" => $this->client['lastname'],
            "client_firstname" => $this->client['firstname'],
            "client_address" => $this->client['address'],
            "client_phone" =>  $this->when(true, function () {
                if ($this->client['telephone'] != null) return $this->client['telephone'];
                if ($this->client['telephone2'] != null) return $this->client['telephone2'];
                if ($this->client['mobile'] != null) return $this->client['mobile'];
            }),
            "offer_filename" => $this->offer_filename,
            // "status_id" => $this->status_id,
            // "status" => $this->status->names,
            "bullets" => $this->bullets,
            //"url" => url("api/v1/offers-file/" . $this->id)
            "url" => "offers-file/" . $this->id
        ];
    }
}
