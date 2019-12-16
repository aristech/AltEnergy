<?php

namespace App\Http\Middleware;

use Closure;

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
        $role_id = $request->user()->role()->first()->id;
        if ($role_id < 4) {
            return response()->json(["message" => "Ο χρήστης με ρόλο " . $request->user()->role()->first()->title . " δεν μπορεί να έχει πρόσβαση στα στοιχεία αυτά"], 401);
        }
        return $next($request);
    }
}
