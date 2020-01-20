<?php

namespace App\Http\CustomClasses\v1;

use Illuminate\Http\Request;

class AuthorityClass
{
    public static function getAuthorityLevel(Request $request)
    {
        $role_array = [];
        foreach ($request->user()->role as $role) {
            array_push($role_array, $role->id);
        }
        rsort($role_array);
        $highest_role = $role_array[0];
        return $highest_role;
        // $role_id = $request->user()->role()->first()->id;
    }
}
