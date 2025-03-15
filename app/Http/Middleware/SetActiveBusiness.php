<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

class SetActiveBusiness
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $activeBusinessId = Session::get('active_business_id');

            $activeBusiness = null;

            if (!$activeBusinessId) {
                $activeBusiness = $user->businessRelations()->with('business')->first()?->business;
                if ($activeBusiness) {
                    Session::put('active_business_id', $activeBusiness->id);
                }
            } else {
                $activeBusiness = $user->businessRelations()
                    ->where('business_id', $activeBusinessId)
                    ->with('business')
                    ->first()?->business;
            }

            // Attach active business to user object
           // $user->activeBusiness = $activeBusiness;
        }

        return $next($request);
    }
}
