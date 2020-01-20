<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\CustomClasses\v1\AuthorityClass;

class AdminOnlyMiddleware
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
        // $role_array = [];
        // foreach ($request->user()->role as $role) {
        //     array_push($role_array, $role->id);
        // }
        // rsort($role_array);
        // $highest_role = $role_array[0];
        $highest_role = AuthorityClass::getAuthorityLevel($request);
        // $role_id = $request->user()->role()->first()->id;
        // $role_id = $request->user()->role()->first()->id;
        if ($highest_role < 4) {
            return response()->json(["message" => "Δεν μπορείτε να έχετε πρόσβαση στα στοιχεία αυτά"], 401);
        }
        return $next($request);
    }
}
