<?php
namespace Modules\Flight;
use App\Models\Business;
use Modules\Flight\Models\Flight;
use Modules\ModuleServiceProvider;
use Illuminate\Support\Facades\Auth;
use Modules\User\Helpers\PermissionHelper;

class ModuleProvider extends ModuleServiceProvider
{

    public function boot(){

        $this->loadMigrationsFrom(__DIR__ . '/Migrations');

        PermissionHelper::add([
            'flight_view',
            'flight_create',
            'flight_update',
            'flight_delete',
            'flight_manage_others',
            'flight_manage_attributes',
        ]);
    }
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouterServiceProvider::class);
    }

    public static function getAdminMenu()
    {
        if(!Flight::isEnable()) return [];
        return [
            'flight'=>[
                "position"=>41,
                'url'        => route('flight.admin.index'),
                'title'      => __('Flight'),
                'icon'       => 'ion ion-md-airplane',
                'permission' => 'flight_view',
                'children'   => [
                    'add'=>[
                        'url'        => route('flight.admin.index'),
                        'title'      => __('All Flights'),
                        'permission' => 'flight_view',
                    ],
                    'create'=>[
                        'url'        => route('flight.admin.create'),
                        'title'      => __('Add new Flight'),
                        'permission' => 'flight_create',
                    ],
                    'airline'=>[
                        'url'        => route('flight.admin.airline.index'),
                        'title'      => __('Airline'),
                    ],
                    'airport'=>[
                        'url'        => route('flight.admin.airport.index'),
                        'title'      => __('Airport'),
                    ],
                    'seat_type'=>[
                        'url'        => route('flight.admin.seat_type.index'),
                        'title'      => __('Seat Type'),
                    ],
                    'attribute'=>[
                        'url'        => route('flight.admin.attribute.index'),
                        'title'      => __('Attributes'),
                        'permission' => 'flight_manage_attributes',
                    ],
                ]
            ]
        ];
    }

    public static function getBookableServices()
    {
        if(!Flight::isEnable()) return [];
        return [
            'flight'=>Flight::class
        ];
    }

    public static function getMenuBuilderTypes()
    {
        return [];
    }

    public static function getUserMenu()
    {
        $res = [];

        $user = Auth::user();
        /* $isApproved = ($user->role_id == 2|| $user->role_id == 1)&& $user->business && $user->business->services()->where('service_id', 4)->exists(); */

        $activeBusinessId = session('active_business_id');
        /* $business = Business::where('id', $activeBusinessId)
        ->where('create_user', $user->id)
        ->first();

        $exists = $business && $business->services()->where('service_id', 4)->exists();

        $isApproved =
           ( ($user->role_id == 2 || $user->role_id == 1) &&
           (
            $exists
           ))||
            $user->businessRelations()
                ->where('business_id', $activeBusinessId)
                ->where('service_type', 'flight')
                ->exists()
         ; */
        $business_row = Business::where('id', $activeBusinessId)->first(); //get the active business record
        $isOwner_business= Business::where('id', $activeBusinessId)
        ->where('create_user', $user->id)
        ->exists();

        $serviceExists = $business_row && $business_row->services()->where('service_id', 4)->exists();//the user is business owner and has business service
        $serviceForOwnerExists=$isOwner_business &&  $serviceExists;
        $serviceRelationExists = $user->businessRelations()                                 //user has business relations with this service
        ->where('business_id', $activeBusinessId)
        ->where('service_type', 'flight')
        ->exists();

        $isApproved = $serviceForOwnerExists ||( $serviceExists && $serviceRelationExists);

        if (Flight::isEnable() && $isApproved) {
            $res['flight'] = [
                'url'        => route('flight.vendor.index'),
                'title'      => __("Manage Flight"),
                'icon'       => Flight::getServiceIconFeatured(),
                'position'   => 60,
                'permission' => 'flight_view',
                'children'   => [
                    [
                        'url'   => route('flight.vendor.index'),
                        'title' => __("All Flights"),
                    ],
                    [
                        'url'        => route('flight.vendor.create'),
                        'title'      => __("Add Flights"),
                        'permission' => 'flight_create',
                    ],
                ]
            ];
        }
        return $res;
    }

    public static function getTemplateBlocks(){
        if(!Flight::isEnable()) return [];
        return [
            'form_search_flight'=>"\\Modules\\Flight\\Blocks\\FormSearchFlight",
        ];
    }
}
