<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\UsersRoles;
use App\Role;

class UserResource extends JsonResource
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
            "lastname" => $this->lastname,
            "firstname" => $this->firstname,
            "email" => $this->email,
            "telephone" => $this->telephone,
            "telephone2" => $this->telephone2,
            "mobile" => $this->mobile,
            "active" => $this->active,
            "role_id"=> $this->role()->first()->id,
            "role_title" => $this->role()->first()->title,
            "manager_id" => $this->manager_id,
            "client_id" => $this->client_id

        ];
    }
}
