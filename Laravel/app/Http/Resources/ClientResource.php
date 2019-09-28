<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
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
        "lastname" => $this->lastname,
        "firstname"=> $this->firstname,
        "afm"=> $this->afm,
        "doy"=> $this->doy,
        "telephone"=> $this->telephone,
        "telephone2"=> $this->telephone2,
        "mobile"=> $this->mobile,
        "address"=> $this->address,
        "zipcode"=> $this->zipcode,
        "location"=> $this->location,
        "email"=> $this->email,
        "level"=> $this->level,
        "manager_id"=> $this->manager['id'],
        "manager_lastname"=> $this->manager['lastname'],
        "manager_firstname"=> $this->manager['firstname'],
        "manager_telephone"=> $this->manager['telephone'],
        "manager_telephone2"=> $this->manager['telephone2'],
        "manager_mobile" => $this->manager['mobile'],
        "manager_email" => $this->manager['email']
        ];
    }
}
