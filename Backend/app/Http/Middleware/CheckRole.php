<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if(!Auth::check()){
            return redirect("/");
        }

        if(Auth::user()->rol !== $role){
            abort(403, 'Acceso denegado, la cuenta no tiene los permisos para ver esta pagina');
        }    
        return $next($request);
    }
}
