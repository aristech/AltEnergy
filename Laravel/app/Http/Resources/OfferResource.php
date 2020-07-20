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
            "client_fullname" => $this->client['lastname'] . " " . $this->client['firstname'],
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
            "offer_bullets" => $this->when(true, function () {
                $bulletsArray = array();
                foreach ($this->bullets as $bullet) {
                    if ($bullet['pivot']['quantity'] > 1) {
                        array_push($bulletsArray, $bullet['description'] . " x " . $bullet['pivot']['quantity']);
                    } else {
                        array_push($bulletsArray, $bullet['description']);
                    }
                }

                return implode(' | ', $bulletsArray);
            }),
            "amount" => $this->amount,
            //"url" => url("api/v1/offers-file/" . $this->id)
            //"offer_url" => url("/api/v1/offers-file/" . $this->id),
            "offer_url" => url("api/v1/offers-file/" . $this->id),
            "created_at" => date('m/d/Y', strtotime($this->created_at))
        ];
    }
}
