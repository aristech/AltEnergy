<?php

namespace App\Http\Middleware;

use Closure;

class UserIsActive
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
        if($request->user()->active == false)
        {
            return response()->json(["message" => "Ο χρήστης δεν είναι πλέον ενεργός!"],401);
        }

        return $next($request);
    }
}
