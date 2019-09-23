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
            "fullname" => $this->lastname." ".$this->firstname,
            "email" => $this->email,
            "role_id"=> $this->role()->first()->id,
            "role_title" => $this->role()->first()->title

        ];
    }
}
