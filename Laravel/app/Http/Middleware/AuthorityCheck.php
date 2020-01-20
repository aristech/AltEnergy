<?php

namespace App\Http\Middleware;

use Closure;

class AuthorityCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $role_array = [];
        foreach ($request->user()->role as $role) {
            array_push($role_array, $role->id);
        }
        rsort($role_array);
        $highest_role = $role_array[0];

        if ($highest_role < 4) {
            return response()->json(["message" => "Ο χρήστης δεν μπορεί να κάνει την ενέργεια αυτή"], 401);
        }

        return $next($request);
    }
}
