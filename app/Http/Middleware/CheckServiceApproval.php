<?php

namespace App\Http\Middleware;


use Closure;
use App\Models\Business;
use Modules\Car\Models\Car;
use Illuminate\Http\Request;
use Modules\Boat\Models\Boat;
use Modules\Tour\Models\Tour;
use Modules\Event\Models\Event;
use Modules\Hotel\Models\Hotel;
use Modules\Space\Models\Space;
use Modules\Flight\Models\Flight;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckServiceApproval
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $serviceType)
    {
        $user = Auth::user();
        $activeBusinessId = session('active_business_id');

        if (!Hotel::isEnable() && $serviceType == 'hotel') {
            //return redirect()->route('home')->with('error', ucfirst($serviceType) . ' is not enabled.');
            abort(404, 'Approval is required to access '.ucfirst($serviceType).' Service' );
        }

        if (!Space::isEnable() && $serviceType == 'space') {
            //return redirect()->route('home')->with('error', ucfirst($serviceType) . ' is not enabled.');
            abort(404, 'Approval is required to access '.ucfirst($serviceType).' Service' );
        }

        if (!Car::isEnable() && $serviceType == 'car') {
            //return redirect()->route('home')->with('error', ucfirst($serviceType) . ' is not enabled.');
            abort(404, 'Approval is required to access '.ucfirst($serviceType).' Service' );
        }

        if (!Tour::isEnable() && $serviceType == 'tour') {
            //return redirect()->route('home')->with('error', ucfirst($serviceType) . ' is not enabled.');
            abort(404, 'Approval is required to access '.ucfirst($serviceType).' Service' );
        }

        if (!Event::isEnable() && $serviceType == 'event') {
            //return redirect()->route('home')->with('error', ucfirst($serviceType) . ' is not enabled.');
            abort(404, 'Approval is required to access '.ucfirst($serviceType).' Service' );
        }

        if (!Boat::isEnable() && $serviceType == 'boat') {
            //return redirect()->route('home')->with('error', ucfirst($serviceType) . ' is not enabled.');
            abort(404, 'Approval is required to access '.ucfirst($serviceType).' Service' );
        }

        if (!Flight::isEnable() && $serviceType == 'flight') {
            //return redirect()->route('home')->with('error', ucfirst($serviceType) . ' is not enabled.');
            abort(404, 'Approval is required to access '.ucfirst($serviceType).' Service' );
        }
            $business = Business::where('id', $activeBusinessId)
           ->where('create_user', $user->id)
           ->first();

            $isApproved =
           ( ($user->role_id == 2 || $user->role_id == 1) &&
           (
            $business && $business->services()->where('name', $serviceType)->exists()
           ))||
            $user->businessRelations()
                ->where('business_id', $activeBusinessId)
                ->where('service_type', $serviceType)
                ->exists()
         ;

       if (!$isApproved) {
             //return redirect()->back()->with('error', 'You are not approved for ' . ucfirst($serviceType) . '.');
             abort(404, 'Approval is required to access '.ucfirst($serviceType).' Service' );
        }
        return $next($request);
    }
}
