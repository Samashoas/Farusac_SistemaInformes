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
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if(!Auth::check()){
            return redirect("/");
        }

        $userRole = strtolower(trim(Auth::user()->rol));

        // Construir lista de roles permitidos
        $allowed = [];
        foreach ($roles as $r) {
            $splitRoles = explode(',', $r);
            foreach ($splitRoles as $sr) {
                $allowed[] = strtolower(trim($sr));
            }
        }

        if(!in_array($userRole, $allowed)){
            abort(403, 'Acceso denegado, la cuenta no tiene los permisos para ver esta pagina');
        }    
        return $next($request);
    }
}
