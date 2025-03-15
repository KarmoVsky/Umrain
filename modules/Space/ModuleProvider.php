<?php
namespace Modules\Space;
use App\Models\Business;
use Modules\Space\Models\Space;
use Modules\ModuleServiceProvider;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Helpers\SitemapHelper;
use Modules\User\Helpers\PermissionHelper;

class ModuleProvider extends ModuleServiceProvider
{

    public function boot(SitemapHelper $sitemapHelper){

        $this->loadMigrationsFrom(__DIR__ . '/Migrations');

        if(is_installed() and Space::isEnable()){
            $sitemapHelper->add("space",[app()->make(Space::class),'getForSitemap']);
        }

        PermissionHelper::add([
            // Space
            'space_view',
            'space_create',
            'space_update',
            'space_delete',
            'space_manage_others',
            'space_manage_attributes',
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
        if(!Space::isEnable()) return [];
        return [
            'space'=>[
                "position"=>41,
                'url'        => route('space.admin.index'),
                'title'      => __('Space'),
                'icon'       => 'ion ion-md-home',
                'permission' => 'space_view',
                'children'   => [
                    'add'=>[
                        'url'        => route('space.admin.index'),
                        'title'      => __('All Spaces'),
                        'permission' => 'space_view',
                    ],
                    'create'=>[
                        'url'        => route('space.admin.create'),
                        'title'      => __('Add new Space'),
                        'permission' => 'space_create',
                    ],
                    'attribute'=>[
                        'url'        => route('space.admin.attribute.index'),
                        'title'      => __('Attributes'),
                        'permission' => 'space_manage_attributes',
                    ],
                    'availability'=>[
                        'url'        => route('space.admin.availability.index'),
                        'title'      => __('Availability'),
                        'permission' => 'space_create',
                    ],
                    'recovery'=>[
                        'url'        => route('space.admin.recovery'),
                        'title'      => __('Recovery'),
                        'permission' => 'space_view',
                    ],

                ]
            ]
        ];
    }

    public static function getBookableServices()
    {
        if(!Space::isEnable()) return [];
        return [
            'space'=>Space::class
        ];
    }

    public static function getMenuBuilderTypes()
    {
        if(!Space::isEnable()) return [];
        return [
            'space'=>[
                'class' => Space::class,
                'name'  => __("Spaces"),
                'items' => Space::searchForMenu(),
                'position'=>41
            ]
        ];
    }

    public static function getUserMenu()
    {
        $res = [];

        $user = Auth::user();
        /* $isApproved = ($user->role_id == 2 || $user->role_id == 1)&& $user->business && $user->business->services()->where('service_id', 5)->exists(); */

        $activeBusinessId = session('active_business_id');
        /* $business = Business::where('id', $activeBusinessId)
        ->where('create_user', $user->id)
        ->first();

        $exists = $business && $business->services()->where('service_id', 5)->exists();

        $isApproved =
           ( ($user->role_id == 2 || $user->role_id == 1) &&
           (
            $exists
           ))||
            $user->businessRelations()
                ->where('business_id', $activeBusinessId)
                ->where('service_type', 'space')
                ->exists()
         ; */

        $business_row = Business::where('id', $activeBusinessId)->first(); //get the active business record
        $isOwner_business= Business::where('id', $activeBusinessId)
        ->where('create_user', $user->id)
        ->exists();

        $serviceExists = $business_row && $business_row->services()->where('service_id', 5)->exists();//the user is business owner and has business service
        $serviceForOwnerExists=$isOwner_business &&  $serviceExists;
        $serviceRelationExists = $user->businessRelations()                                 //user has business relations with this service
        ->where('business_id', $activeBusinessId)
        ->where('service_type', 'space')
        ->exists();

        $isApproved = $serviceForOwnerExists ||( $serviceExists && $serviceRelationExists);


        if (Space::isEnable() &&$isApproved) {
            $res['space'] = [
                'url'        => route('space.vendor.index'),
                'title'      => __("Manage Space"),
                'icon'       => Space::getServiceIconFeatured(),
                'position'   => 50,
                'permission' => 'space_view',
                'children'   => [
                    [
                        'url'   => route('space.vendor.index'),
                        'title' => __("All Spaces"),
                    ],
                    [
                        'url'        => route('space.vendor.create'),
                        'title'      => __("Add Space"),
                        'permission' => 'space_create',
                    ],
                    [
                        'url'        => route('space.vendor.availability.index'),
                        'title'      => __("Availability"),
                        'permission' => 'space_create',
                    ],
                    [
                        'url'   => route('space.vendor.recovery'),
                        'title'      => __("Recovery"),
                        'permission' => 'space_create',
                    ],
                ]
            ];
        }
        return $res;
    }

    public static function getTemplateBlocks(){
        if(!Space::isEnable()) return [];
        return [
            'form_search_space'=>"\\Modules\\Space\\Blocks\\FormSearchSpace",
            'list_space'=>"\\Modules\\Space\\Blocks\\ListSpace",
            'space_term_featured_box'=>"\\Modules\\Space\\Blocks\\SpaceTermFeaturedBox",
        ];
    }
}
