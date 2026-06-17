<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsEntityManager
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || (!auth()->user()->is_entity_manager && !auth()->user()->is_admin)) {
            return redirect()->route('dashboard')->with('error', 'Apenas gestores de entidade ou administradores podem acessar esta área.');
        }

        return $next($request);
    }
}
