<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Api
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (getActive())
        {
            return screaming('active');
        }

        $bearer = $request->bearerToken();
        if (strpos($bearer, '|'))
        {
            $bearer = explode('|', $bearer);
            $bearer = $bearer[1];
        }

        if ($token = DB::table('personal_access_tokens')->where('token', hash('sha256', $bearer))->first())
        {
            if ($user = User::find($token->tokenable_id))
            {
                Auth::login($user);
            }
        }

        return $next($request);
    }
}
