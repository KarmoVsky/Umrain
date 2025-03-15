<?php
namespace Modules\Event;
use App\Models\Business;
use Modules\News\Models\News;
use Modules\Event\Models\Event;
use Modules\ModuleServiceProvider;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Helpers\SitemapHelper;
use Modules\User\Helpers\PermissionHelper;

class ModuleProvider extends ModuleServiceProvider
{

    public function boot(SitemapHelper $sitemapHelper){

        $this->loadMigrationsFrom(__DIR__ . '/Migrations');

        if(is_installed() and Event::isEnable()){

            $sitemapHelper->add("event",[app()->make(Event::class),'getForSitemap']);
        }
        PermissionHelper::add([
            // Event
            'event_view',
            'event_create',
            'event_update',
            'event_delete',
            'event_manage_others',
            'event_manage_attributes',
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
        if(!Event::isEnable()) return [];
        return [
            'event'=>[
                "position"=>50,
                'url'        => route('event.admin.index'),
                'title'      => __('Event'),
                'icon'       => 'ion-ios-calendar',
                'permission' => 'event_view',
                'children'   => [
                    'add'=>[
                        'url'        => route('event.admin.index'),
                        'title'      => __('All Events'),
                        'permission' => 'event_view',
                    ],
                    'create'=>[
                        'url'        => route('event.admin.create'),
                        'title'      => __('Add new Event'),
                        'permission' => 'event_create',
                    ],
                    'attribute'=>[
                        'url'        => route('event.admin.attribute.index'),
                        'title'      => __('Attributes'),
                        'permission' => 'event_manage_attributes',
                    ],
                    'availability'=>[
                        'url'        => route('event.admin.availability.index'),
                        'title'      => __('Availability'),
                        'permission' => 'event_create',
                    ],
                    'recovery'=>[
                        'url'        => route('event.admin.recovery'),
                        'title'      => __('Recovery'),
                        'permission' => 'event_view',
                    ],
                ]
            ]
        ];
    }

    public static function getBookableServices()
    {
        if(!Event::isEnable()) return [];
        return [
            'event'=>Event::class
        ];
    }

    public static function getMenuBuilderTypes()
    {
        if(!Event::isEnable()) return [];
        return [
            'event'=>[
                'class' => Event::class,
                'name'  => __("Event"),
                'items' => Event::searchForMenu(),
                'position'=>51
            ]
        ];
    }

    public static function getUserMenu()
    {
        $user = Auth::user();
        /* $isApproved = ($user->role_id == 2|| $user->role_id == 1) && $user->business && $user->business->services()->where('service_id', 6)->exists(); */

        $activeBusinessId = session('active_business_id');
        /* $business = Business::where('id', $activeBusinessId)
        ->where('create_user', $user->id)
        ->first();

        $exists = $business && $business->services()->where('service_id', 6)->exists();

        $isApproved =
           ( ($user->role_id == 2 || $user->role_id == 1) &&
           (
            $exists
           ))||
            $user->businessRelations()
                ->where('business_id', $activeBusinessId)
                ->where('service_type', 'event')
                ->exists()
         ;
 */
        $business_row = Business::where('id', $activeBusinessId)->first(); //get the active business record
        $isOwner_business= Business::where('id', $activeBusinessId)
        ->where('create_user', $user->id)
        ->exists();

        $serviceExists = $business_row && $business_row->services()->where('service_id', 6)->exists();//the user is business owner and has business service
        $serviceForOwnerExists=$isOwner_business &&  $serviceExists;
        $serviceRelationExists = $user->businessRelations()                                 //user has business relations with this service
        ->where('business_id', $activeBusinessId)
        ->where('service_type', 'event')
        ->exists();

        $isApproved = $serviceForOwnerExists ||( $serviceExists && $serviceRelationExists);
        if(!Event::isEnable() || !$isApproved) return [];
        return [
            'event' => [
                'url'   => route('event.vendor.index'),
                'title'      => __("Manage Event"),
                'icon'       => Event::getServiceIconFeatured(),
                'position'   => 80,
                'permission' => 'event_view',
                'children' => [
                    [
                        'url'   => route('event.vendor.index'),
                        'title'  => __("All Events"),
                    ],
                    [
                        'url'   => route('event.vendor.create'),
                        'title'      => __("Add Event"),
                        'permission' => 'event_create',
                    ],
                    'availability'=>[
                        'url'        => route('event.vendor.availability.index'),
                        'title'      => __('Availability'),
                        'permission' => 'event_create',
                    ],
                    [
                        'url'   => route('event.vendor.recovery'),
                        'title'      => __("Recovery"),
                        'permission' => 'event_create',
                    ],
                ]
            ],
        ];
    }

    public static function getTemplateBlocks(){
        if(!Event::isEnable()) return [];
        return [
            'form_search_event'=>"\\Modules\\Event\\Blocks\\FormSearchEvent",
            'list_event'=>"\\Modules\\Event\\Blocks\\ListEvent",
            'event_term_featured_box'=>"\\Modules\\Event\\Blocks\\EventTermFeaturedBox",
        ];
    }
}
