<?php

namespace Modules\Hotel;

use App\Models\Business;
//use GPBMetadata\Google\Api\Auth;
use Modules\Hotel\Models\Hotel;
use Modules\ModuleServiceProvider;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Helpers\SitemapHelper;
use Modules\User\Helpers\PermissionHelper;

class ModuleProvider extends ModuleServiceProvider
{

    public function boot(SitemapHelper $sitemapHelper)
    {

        $this->loadMigrationsFrom(__DIR__ . '/Migrations');

        if (is_installed() and Hotel::isEnable()) {

            $sitemapHelper->add("hotel", [app()->make(Hotel::class), 'getForSitemap']);
        }
        PermissionHelper::add([
            // Hotel
            'hotel_view',
            'hotel_create',
            'hotel_update',
            'hotel_delete',
            'hotel_manage_others',
            'hotel_manage_attributes',
            'hotel_room_manage',
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
        $noti_relation = \App\Models\BusinessRelation::where('service_type', 'hotel')->where('status', 'pending')->count();
        if (!Hotel::isEnable()) return [];
        return [
            'hotel' => [
                "position" => 32,
                'url'        => route('hotel.admin.index'),
                'title'      => __('Hotel :count', ['count' => $noti_relation ? sprintf('<span class="badge badge-warning">%d</span>', $noti_relation) : '']),
                'icon'       => 'fa fa-building-o',
                'permission' => 'hotel_view',
                'children'   => [
                    'add' => [
                        'url'        => route('hotel.admin.index'),
                        'title'      => __('All Hotels :count', ['count' => $noti_relation ? sprintf('<span class="badge badge-warning">%d</span>', $noti_relation) : '']),
                        'permission' => 'hotel_view',
                    ],
                    'create' => [
                        'url'        => route('hotel.admin.create'),
                        'title'      => __('Add new Hotel'),
                        'permission' => 'hotel_create',
                    ],
                    'attribute' => [
                        'url'        => route('hotel.admin.attribute.index'),
                        'title'      => __('Attributes'),
                        'permission' => 'hotel_manage_attributes',
                    ],
                    'room_attribute' => [
                        'url'        => route('hotel.admin.room.attribute.index'),
                        'title'      => __('Room Attributes'),
                        'permission' => 'hotel_manage_attributes',
                    ],
                    'recovery' => [
                        'url'        => route('hotel.admin.recovery'),
                        'title'      => __('Recovery'),
                        'permission' => 'hotel_view',
                    ],
                ]
            ]
        ];
    }

    public static function getBookableServices()
    {
        if (!Hotel::isEnable()) return [];
        return [
            'hotel' => Hotel::class
        ];
    }

    public static function getMenuBuilderTypes()
    {
        if (!Hotel::isEnable()) return [];
        return [
            'hotel' => [
                'class' => Hotel::class,
                'name'  => __("Hotel"),
                'items' => Hotel::searchForMenu(),
                'position' => 41
            ]
        ];
    }


    public static function getUserMenu()
    {

        $res = [];

        $user = Auth::user();

        $activeBusinessId = session('active_business_id');

        /* $businessowner=$business->where('create_user', $user->id)
        ->exists();//the user is business owner here */
        /* $isApproved =
           ( ($user->role_id == 2 || $user->role_id == 1) &&
           (
            $exists
           ))||
            ($exists)&&($user->businessRelations()
                ->where('business_id', $activeBusinessId)
                ->where('service_type', 'hotel')
                ->exists())
         ; */
        $business_row = Business::where('id', $activeBusinessId)->first(); //get the active business record
        $isOwner_business= Business::where('id', $activeBusinessId)
        ->where('create_user', $user->id)
        ->exists();

        $serviceExists = $business_row && $business_row->services()->where('service_id', 1)->exists();//the user is business owner and has business service
        $serviceForOwnerExists=$isOwner_business &&  $serviceExists;
        $serviceRelationExists = $user->businessRelations()                                 //user has business relations with this service
        ->where('business_id', $activeBusinessId)
        ->where('service_type', 'hotel')
        ->exists();

        $isApproved = $serviceForOwnerExists ||( $serviceExists && $serviceRelationExists);

        if (Hotel::isEnable() && $isApproved) {
            $res['hotel'] = [
                'url'   => route('hotel.vendor.index'),
                'title'      => __("Manage Hotel"),
                'icon'       => Hotel::getServiceIconFeatured(),
                'position'   => 30,
                'permission' => 'hotel_view',
                'children' => [
                    [
                        'url'   => route('hotel.vendor.index'),
                        'title'  => __("All Hotels"),
                    ],
                    [
                        'url'   => route('hotel.vendor.create'),
                        'title'      => __("Add Hotel"),
                        'permission' => 'hotel_create',
                    ],
                    [
                        'url'   => route('hotel.vendor.recovery'),
                        'title'      => __("Recovery"),
                        'permission' => 'hotel_create',
                    ],
                ]
            ];
        }
        return $res;
    }

    public static function getTemplateBlocks()
    {
        if (!Hotel::isEnable()) return [];
        return [
            'form_search_hotel' => "\\Modules\\Hotel\\Blocks\\FormSearchHotel",
            'list_hotel' => "\\Modules\\Hotel\\Blocks\\ListHotel",
        ];
    }
}
