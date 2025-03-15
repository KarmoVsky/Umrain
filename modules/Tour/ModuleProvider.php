<?php
namespace Modules\Tour;

use App\Models\Business;
use Modules\Tour\Models\Tour;
use Modules\ModuleServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Modules\Core\Helpers\SitemapHelper;
use Modules\User\Helpers\PermissionHelper;

class ModuleProvider extends ModuleServiceProvider
{
    public function boot(SitemapHelper $sitemapHelper)
    {
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');

        if(is_installed() and Tour::isEnable()){
            $sitemapHelper->add("tour",[app()->make(Tour::class),'getForSitemap']);
        }

        PermissionHelper::add([
            // Tour
            'tour_view',
            'tour_create',
            'tour_update',
            'tour_delete',
            'tour_manage_others',
            'tour_manage_attributes',
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

    public static function getBookableServices()
    {
        if(!Tour::isEnable()) return [];
        return [
            'tour' => Tour::class,
        ];
    }

    public static function getAdminMenu()
    {
        $res = [];
        if(Tour::isEnable()){
            $res['tour'] = [
                "position"=>40,
                'url'        => route('tour.admin.index'),
                'title'      => __("Tour"),
                'icon'       => 'icon ion-md-umbrella',
                'permission' => 'tour_view',
                'children'   => [
                    'tour_view'=>[
                        'url'        => route('tour.admin.index'),
                        'title'      => __('All Tours'),
                        'permission' => 'tour_view',
                    ],
                    'tour_create'=>[
                        'url'        => route('tour.admin.create'),
                        'title'      => __("Add Tour"),
                        'permission' => 'tour_create',
                    ],
                    'tour_category'=>[
                        'url'        => route('tour.admin.category.index'),
                        'title'      => __('Categories'),
                        'permission' => 'tour_manage_others',
                    ],
                    'tour_attribute'=>[
                        'url'        => route('tour.admin.attribute.index'),
                        'title'      => __('Attributes'),
                        'permission' => 'tour_manage_attributes',
                    ],
                    'tour_availability'=>[
                        'url'        => route('tour.admin.availability.index'),
                        'title'      => __('Availability'),
                        'permission' => 'tour_create',
                    ],
                    'tour_booking'=>[
                        'url'        => route('tour.admin.booking.index'),
                        'title'      => __('Booking Calendar'),
                        'permission' => 'tour_create',
                    ],
                    'recovery'=>[
                        'url'        => route('tour.admin.recovery'),
                        'title'      => __('Recovery'),
                        'permission' => 'tour_view',
                    ],
                ]
            ];
        }
        return $res;
    }


    public static function getUserMenu()
    {
        $res = [];

        $user = Auth::user();
        /* $isApproved = ($user->role_id == 2 || $user->role_id == 1)&& $user->business && $user->business->services()->where('service_id', 3)->exists(); */

        $activeBusinessId = session('active_business_id');
        /* $business = Business::where('id', $activeBusinessId)
        ->where('create_user', $user->id)
        ->first();

        $exists = $business && $business->services()->where('service_id', 3)->exists();

        $isApproved =
           ( ($user->role_id == 2 || $user->role_id == 1) &&
           (
            $exists
           ))||
            $user->businessRelations()
                ->where('business_id', $activeBusinessId)
                ->where('service_type', 'tour')
                ->exists()
         ; */

        $business_row = Business::where('id', $activeBusinessId)->first(); //get the active business record
        $isOwner_business= Business::where('id', $activeBusinessId)
        ->where('create_user', $user->id)
        ->exists();

        $serviceExists = $business_row && $business_row->services()->where('service_id', 3)->exists();//the user is business owner and has business service
        $serviceForOwnerExists=$isOwner_business &&  $serviceExists;
        $serviceRelationExists = $user->businessRelations()                                 //user has business relations with this service
        ->where('business_id', $activeBusinessId)
        ->where('service_type', 'tour')
        ->exists();

        $isApproved = $serviceForOwnerExists ||( $serviceExists && $serviceRelationExists);


        if(Tour::isEnable() && $isApproved){
            $res['tour'] = [
                'url'   => route('tour.vendor.index'),
                'title'      => __("Manage Tour"),
                'icon'       => Tour::getServiceIconFeatured(),
                'permission' => 'tour_view',
                'position'   => 40,
                'children'   => [
                    [
                        'url'   => route('tour.vendor.index'),
                        'title' => __("All Tours"),
                    ],
                    [
                        'url'        => route('tour.vendor.create'),
                        'title'      => __("Add Tour"),
                        'permission' => 'tour_create',
                    ],
                    [
                        'url'        => route('tour.vendor.availability.index'),
                        'title'      => __("Availability"),
                        'permission' => 'tour_create',
                    ],
                    [
                        'url'   => route('tour.vendor.recovery'),
                        'title'      => __("Recovery"),
                        'permission' => 'tour_create',
                    ],
                ]
            ];
        }
        return $res;
    }

    public static function getMenuBuilderTypes()
    {
        if(!Tour::isEnable()) return [];

        return [
            [
                'class' => \Modules\Tour\Models\Tour::class,
                'name'  => __("Tour"),
                'items' => \Modules\Tour\Models\Tour::searchForMenu(),
                'position'=>20
            ],
            [
                'class' => \Modules\Tour\Models\TourCategory::class,
                'name'  => __("Tour Category"),
                'items' => \Modules\Tour\Models\TourCategory::searchForMenu(),
                'position'=>30
            ],
        ];
    }

    public static function getTemplateBlocks(){
        if(!Tour::isEnable()) return [];

        return [
            'list_tours'=>"\\Modules\\Tour\\Blocks\\ListTours",
            'form_search_tour'=>"\\Modules\\Tour\\Blocks\\FormSearchTour",
            'box_category_tour'=>"\\Modules\\Tour\\Blocks\\BoxCategoryTour",
        ];
    }
}
