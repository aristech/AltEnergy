<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Role;

class TechSearchResource extends JsonResource
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
            "id" => $this->user_id,
            "lastname" => $this->lastname,
            "firstname" => $this->firstname,
            "email" => $this->email,
            "telephone" => $this->telephone,
            "telephone2" => $this->telephone2,
            "mobile" => $this->mobile,
            "active" => $this->active,
            "role_id"=> $this->role_id,
            "role_title" => Role::where('id',$this->role_id)->first()->title,
            "manager_id" => $this->manager_id,
            "client_id" => $this->client_id
        ];
    }
}
