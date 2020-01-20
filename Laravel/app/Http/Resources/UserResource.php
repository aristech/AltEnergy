<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\UsersRoles;
use App\Role;
use stdClass;

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
                "role_id" => $this->role()->first()->id,
                "roles" => $this->when(true, function () {
                    $user_roles_before = $this->role;
                    $users_roles = array();
                    foreach ($user_roles_before as $role) {
                        array_push($users_roles, $role->id);
                    }
                    $roles = array();
                    $allRoles = Role::where('id', '>', '1')->where('id', '<', '5')->get();
                    foreach ($allRoles as $rolle) {
                        $role = new \stdClass();
                        $role->id = $rolle['id'];
                        $role->name =  "role_id";
                        $role->title = $rolle['title'];
                        if (in_array($rolle['id'], $users_roles)) {
                            $role->checked = true;
                        } else {
                            $role->checked = false;
                        }
                        array_push($roles, $role);
                    }

                    return $roles;
                }),
                "role_title" => $this->role()->first()->title,
                "manager_id" => $this->manager_id,
                "client_id" => $this->client_id
            ];
    }
}
