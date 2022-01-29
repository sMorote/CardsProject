<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserProfile
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
        $response = ['status'=>1, "msg"=>""];

        $user = $request->user();

        if ($user->role == "Administrador") {
            $response['msg']['info'] = 'No tienes permisos';
            return response()->json($response);
        }else{
            return $next($request);
        }
    }
}
