<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\CustomClasses\v1\AuthorityClass;

class AdminAndTechsMiddleware
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
        $highest_role = AuthorityClass::getAuthorityLevel($request);
        if ($highest_role < 3) {
            return response()->json(["message" => "Δεν μπορείτε να έχετε πρόσβαση στα στοιχεία αυτά"], 401);
        }
        return $next($request);
    }
}
