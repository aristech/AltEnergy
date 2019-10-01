<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Role;

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
            "id" => $this->id,
            "fullname" => $this->lastname." ".$this->firstname,
            "email" => $this->email,
            "role_id"=> $this->role_id,
            "role_title" => function()
            {
                $role = Role::where('id',$this->role_id)->first();
                return $role->title;
            }
        ];
    }
}
