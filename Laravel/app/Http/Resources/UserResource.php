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
                "role_id" => $this->when(true, function () {
                    $role_array = [];
                    $user_roles = UsersRoles::where('user_id', $this->id)->get();
                    foreach ($user_roles as $role) {
                        array_push($role_array, $role['role_id']);
                    }
                    rsort($role_array);
                    $highest_role = $role_array[0];
                    return $highest_role;
                }),
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
                "role_title" => $this->when(true, function () {
                    $role_array = [];
                    $user_roles = UsersRoles::where('user_id', $this->id)->get();
                    foreach ($user_roles as $role) {
                        array_push($role_array, $role['role_id']);
                    }
                    rsort($role_array);
                    $title_array = array();
                    foreach ($role_array as $role) {
                        $title = Role::where('id', $role)->first()['title'];
                        array_push($title_array, $title);
                    }
                    //$highest_role = $role_array[0];
                    return implode(", ", $title_array);
                }),
                "manager_id" => $this->manager_id,
                "client_id" => $this->client_id
            ];
    }
}
