<?php
namespace Modules\User\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\User\Models\User;
use Modules\FrontendController;
use App\Models\BusinessRelation;
use Illuminate\Support\Facades\Auth;
use App\Models\Business;
use Modules\User\Events\UserInvite;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

use Modules\User\Models\Role;
use Modules\User\Models\RolePermission;
use Modules\Boat\Models\Boat;
use Modules\Car\Models\Car;
use Modules\Event\Models\Event;
use Modules\Flight\Models\Flight;
use Modules\Hotel\Models\Hotel;
use Modules\Space\Models\Space;
use Modules\Tour\Models\Tour;
use Modules\Location\Models\Location;


class ManageUserController extends FrontendController {

    protected $businessRelationClass;

    public function __construct()
    {
        $this->businessRelationClass = BusinessRelation::class;
        $this->roleClass = Role::class;
        $this->businessClass = Business::class;
    }

    public function index(Request $request) {
        $user = Auth::user();
        $this->hasPermission('user_create');
        $query = $this->businessRelationClass::query();
        if ($request->s) {
            ## This part must be reviewed
            $query->whereHas('vendor', function($q) use ($request) {
                $q->where('name', 'like', '%'.$request->s.'%');
            });
        }
        $users = $query->where('business_id', session('active_business_id'))
        ->get()
        ->unique('user_id');

        //roles query
        $business = $this->businessClass::find(session('active_business_id'));
        $services = ($business) ? $business->services()->pluck('name') : collect([]);
        $roles = $this->roleClass::whereHas('permissions', function ($query) {
                $query->where('permission', 'dashboard_vendor_access');
            })
            ->whereHas('permissions', function ($query) use ($services) {
                $query->whereIn('permission', $services->map(fn($service) => $service.'_view'));
            }, '=', count($services))
            ->get();

        $models = [
            'hotel' => Hotel::class,
            'space' => Space::class,
            'car' => Car::class,
            // 'flight' => Flight::class,
            'event' => Event::class,
            'boat' => Boat::class,
            'tour' => Tour::class
        ];

        $locationsData = [];

        foreach ($services as $serviceType) {
            $model = $models[$serviceType] ?? null;

            if (!$model) continue;

            $servicesData = $model::
            whereHas('acceptedVendors', function($query) use ($user) {
                $query->where('business_id', session('active_business_id'));
            }
            )
            ->
            get(['id', 'title', 'location_id']);

            $groupedByLocation = [];
            foreach ($servicesData as $service) {
                $locationId = $service->location_id;

                if (!isset($groupedByLocation[$locationId])) {
                    $location = Location::find($locationId);
                    $groupedByLocation[$locationId] = [
                        'location_id' => $locationId,
                        'location_name' => $location->name ?? 'Unknown',
                        'services' => []
                    ];
                }

                $groupedByLocation[$locationId]['services'][] = [
                    'id' => $service->id,
                    'name' => $service->title
                ];
            }

            $locationsData[$serviceType] = array_values($groupedByLocation);
        }

        foreach ($models as $serviceType => $model) {
            if (!isset($locationsData[$serviceType])) {
                $locationsData[$serviceType] = [];
            }
        }
        $data = [
            'rows' => $users,
            'roles' => $roles,
            'services' => $services,
            'locations' => $locationsData,
            'business' => $business,
            'breadcrumbs' => [
                [
                    'name' => __('Users Management'),
                ]
            ]
        ];
        return view('User::frontend.vendor.index', $data);
    }

    public function userDetails($id) {
        $business_id = session('active_business_id');

        // جلب المستخدم مع العلاقات الخاصة به في البزنس
        $user = User::with(['businessRelations' => function($query) use ($business_id) {
            $query->where('business_id', $business_id);
        }])->find($id);

        // جلب الخدمات التي يملكها المستخدم
        $userServices = $this->businessRelationClass::where('business_id', $business_id)
            ->where('user_id', $id)
            ->where('service_type', '!=', 'owner')
            ->where('status', 'approved')
            ->get(['service_type', 'service_id', 'role_id']);

        $servicesWithLocations = $userServices->map(function ($service) {
            switch ($service->service_type) {
                case 'hotel':
                    $location = Hotel::where('id', $service->service_id)->value('location_id');
                    break;
                case 'car':
                    $location = Car::where('id', $service->service_id)->value('location_id');
                    break;
                case 'space':
                    $location = Space::where('id', $service->service_id)->value('location_id');
                    break;
                case 'event':
                    $location = Event::where('id', $service->service_id)->value('location_id');
                    break;
                case 'boat':
                    $location = Boat::where('id', $service->service_id)->value('location_id');
                    break;
                case 'tour':
                    $location = Tour::where('id', $service->service_id)->value('location_id');
                    break;
                // case 'flight':
                //     $location = Flight::where('id', $service->service_id)->value('location_id');
                //     break;
                default:
                    $location = null;
            }

            return [
                'service_type' => $service->service_type,
                'service_id' => $service->service_id,
                'location_id' => $location
            ];
        });
        $userServicesByType = [];
        foreach ($servicesWithLocations as $service) {
            $userServicesByType[$service['service_type']][$service['location_id']][] = $service['service_id'];
        }
        $models = [
            'hotel' => Hotel::class,
            'space' => Space::class,
            'car' => Car::class,
            // 'flight' => Flight::class,
            'event' => Event::class,
            'boat' => Boat::class,
            'tour' => Tour::class
        ];

        $locationsData = [];

        foreach ($models as $serviceType => $model) {
            $servicesData = $model::whereHas('acceptedVendors', function($query) use ($business_id) {
                $query->where('business_id', $business_id);
            })->get(['id', 'title', 'location_id']);

            $groupedByLocation = [];
            $i = 1;
            foreach ($servicesData as $service) {
                $locationId = $service->location_id;
                $isLocationSelected = isset($userServicesByType[$serviceType][$locationId]);

                if (!isset($groupedByLocation[$locationId])) {
                    $location = Location::find($locationId);
                    $groupedByLocation[$locationId] = [
                        'location_id' => $locationId,
                        'location_name' => $location->name ?? 'Unknown',
                        'selected' => $isLocationSelected, // تحديد الموقع المختار
                        'services' => []
                    ];
                }

                $groupedByLocation[$locationId]['services'][] = [
                    'id' => $service->id,
                    'name' => $service->title,
                    'selected' => $isLocationSelected && in_array($service->id, $userServicesByType[$serviceType][$locationId] ?? [])
                ];
            }

            $locationsData[$serviceType] = array_values($groupedByLocation);
        }
        $services = Business::find($business_id)->services()->where('anisth_business_service.status', 'approved')->pluck('name');


        $business = $this->businessClass::find(session('active_business_id'));
        $services = ($business) ? $business->services()->pluck('name') : collect([]);
        $roles = $this->roleClass::whereHas('permissions', function ($query) {
                $query->where('permission', 'dashboard_vendor_access');
            })
            ->whereHas('permissions', function ($query) use ($services) {
                $query->whereIn('permission', $services->map(fn($service) => $service.'_view'));
            }, '=', count($services))
            ->get();
        $rolesWithSelected = $roles->map(function ($role) use ($userServices) {
            $role->selected = ($role->id == $userServices[0]['role_id']);
            return $role;
        });

        $data = [
            'user' => $user,
            'roles' => $roles,
            'locations' => $locationsData,
            'services' => $services,
            'breadcrumbs' => [
                ['name' => __('Users Management'), 'url' => route('user.vendor.index')],
                ['name' => __('Edit user')]
            ]
        ];

        return view('User::frontend.vendor.details', $data);
    }



    public function bulkEditUser(Request $request) {
        $this->hasPermission('user_update');
        $ids = $request->input('ids');
        $action = $request->input('action');
        if(empty($ids) or !is_array($ids)) {
            return redirect()->back()->with('error', __('Select at least 1 item!'));
        }
        if(empty($action)) {
            return redirect()->back()->with('error', __('Select an Action!'));
        }
        switch($action) {
            case 'delete':
                foreach($ids as $id) {
                    if($id == Auth::id()) continue;
                    $query = $this->businessRelationClass::where("user_id", $id);
                    if (!$this->hasPermission('hotel_manage_others')) {
                        // $query->where("create_user", Auth::id());
                        $this->checkPermission('hotel_delete');
                    }
                    $row = $query->first();
                    if(!empty($row)){
                        $row->delete();
                        // event(new UpdatedServiceEvent($row));

                    }
                }
                return redirect()->back()->with('success', __('Deleted success!'));
                break;
            default:
                // Change status
                foreach ($ids as $id) {
                    if($id == Auth::id()) continue;
                    $query = $this->businessRelationClass::where("user_id", $id);
                    if (!$this->hasPermission('hotel_manage_others')) {
                        // $query->where("create_user", Auth::id());
                        $this->checkPermission('hotel_update');
                    }
                    $row = $query->first();
                    if(!empty($row) and in_array($action, ['approved', 'draft', 'delete'])) {
                        $row->status  = $action;
                        $row->save();
                    }
                    // event(new UpdatedServiceEvent($row));

                }
                return redirect()->back()->with('success', __('Update success!'));
                break;
        }
    }

    public function deleteRelatedUser(Request $request) {

        $id = $request->query('id');
        $user = Auth::user();
        if(empty($id)) {
            return redirect()->route('user.vendor.index');
        }
        $rows = $this->businessRelationClass::where('user_id', '!=', $user->id)
        ->where('user_id', $id)->where('business_id', session('active_business_id'))
        ->get();

        if(!empty($rows)) {
            $rows->each(function ($user) {
                $user->delete();
            });
        }

        $user_removed = User::find($rows[0]->user_id);
        try {
            event(new UserInvite($user_removed, Business::find(session('active_business_id')), 'remove'));
        } catch(\Exception $e) {
            info($e->getMessage());
        }
        return redirect()->route('user.vendor.index')->with('success', 'User deleted successfully!');
    }

    //////////////////// Leave Business
    public function leaveBusiness(Request $request) {
        $businesses_count = $request->query('count');
        $business_id = session('active_business_id');
        $user = Auth::user();
        if(empty($business_id)) {
            return redirect()->back()->with('error', 'Business relation not found.');
        }
        $rows = $this->businessRelationClass::where('user_id', '=', $user->id)->where('business_id', $business_id)->get();
        if ($rows->isEmpty()) {
            return redirect()->back()->with('error', 'Business relation not found.');
        }
        //$isOwner = $rows->contains(fn($row) => $row->service_type === 'owner');
        $isOwner=Business::where('id',$business_id)->where('create_user',$user->id)->exists();
        $anotherOwnerAvailab = $this->businessRelationClass::where('business_id', $business_id)
        ->where('user_id', '!=', $user->id)
        ->exists();
        if (!$isOwner) {   /*leaving user is not owner*/
            $rows->each(fn($row) => $row->delete());
            Session::forget('active_business_id');
            if ($businesses_count == 1) {
                if($user->role_id == 2)
                    $user->role_id = 3;
                    $user->save();
            }
        }
        elseif ($isOwner && !$anotherOwnerAvailab) {   /* owner and no other user related to this business */
            /* $rows->each(fn($row) => $row->delete());
            Session::forget('active_business_id'); */
            return redirect()->route('user.vendor.index')->with('warning', 'You cannot leave as you are the only business user.');
        } else {
            return redirect()->route('user.vendor.index')->with('warning', 'You Should Set Another Owner First.');
        }

        try {
            event(new UserInvite($rows->vendor, $rows->business, 'remove'));
        } catch(\Exception $e) {
            info($e->getMessage());
        }
        return redirect()->back()->with('success', 'Leaved Business successfully!');
    }

    public function updateBusinessOwner(Request $request)
    {
        $new_owner_id = $request->query('id');
        $old_owner_id = Auth::id();
        $business_id = session('active_business_id');
        $business=Business::where('id', $business_id)->where('create_user',$old_owner_id)->first();
        if($business){
           $business->create_user = $new_owner_id;
           $business->save();
        }
        else{
            return redirect()->back()->with('error', 'You Are Not The Business Owner.<br>You Can not Update Business Ownership.');
        }

        $existingServices = BusinessRelation::where('user_id', $new_owner_id)
        ->select('business_id','service_id', 'service_type')
        ->get()
        /* ->map(fn ($relation) => $relation->service_id . '-' . $relation->service_type) */
        ->toArray();

        $relations = BusinessRelation::where('user_id', $old_owner_id)->get();

        $newRelations = [];
        foreach ($relations as $relation) {
            $exists = collect($existingServices)->contains(function ($existing) use ($relation) {
                return $existing['business_id'] == $relation->business_id &&
                       $existing['service_id'] == $relation->service_id &&
                       $existing['service_type'] == $relation->service_type;
            });

            /* $key = $relation->service_id . '-' . $relation->service_type; */
            if (!$exists) { // Check if it already exists
                $newRelations[] = [
                    'user_id'      => $new_owner_id,
                    'business_id'  => $relation->business_id,
                    'service_id'   => $relation->service_id,
                    'service_type' => $relation->service_type,
                    'created_at'   => $relation->created_at,
                    'updated_at'   => $relation->updated_at,
                    'role_id'      => $relation->role_id,
                    'sub_region'   => $relation->sub_region,
                    'country'      => $relation->country,
                    'start_date'   => $relation->start_date,
                    'end_date'     => $relation->end_date,
                    'status'       => $relation->status
                ];
            }


        }
        if (!empty($newRelations)) {
            BusinessRelation::insert($newRelations);
        }
        return redirect()->back()->with('success', 'Business ownership updated successfully.<br>New owner has full access.<br>Now You can Leave Business.');
    }

    ////////////// Switch Business
    public function switchBusiness(Request $request)
    {
        $businessId = $request->query('id');//id is business_id for selected business

        $user = Auth::user();
        $validBusiness = $user->businessRelations()->where('business_id', $businessId)->exists();

        if ($validBusiness) {
            session(['active_business_id' => $businessId]);
            return back()->with('success', 'Switched business successfully.');
        }

        return back()->with('error', 'You do not have access to this business.');
    }

    public function searchUser(Request $request) {
        $query = $request->input('query');
        if (!$query) {
            return response()->json([
                'exists' => false,
                'email_verified_at' => null,
            ]);
        }
        $user = User::where('email', $query)->first();
        if ($user) {
            return response()->json([
                'exists' => true,
                'email_verified_at' => $user->email_verified_at,
            ]);
        }
        return response()->json(['exists' => false]);
    }

    public function inviteUser(Request $request) {
        $vendor = Auth::user();
        $business = Business::find(session('active_business_id'));
        if(empty($business)) {
            return redirect()->route('user.vendor.index')->with('error', 'Error');
        }
        $businessId = $business->id;
        $request->validate([
            'emails' => 'required|array|min:1',
            'emails.*' => 'email|distinct|exists:users,email',
            'role' => 'required|integer|exists:core_roles,id',
            'hotel_services' => 'nullable|array|exists:bravo_hotels,id',
            'car_services' => 'nullable|array|exists:bravo_cars,id',
            'space_services' => 'nullable|array|exists:bravo_spaces,id',
            'flight_services' => 'nullable|array|exists:bravo_flight,id',
            'event_services' => 'nullable|array|exists:bravo_events,id',
            'boat_services' => 'nullable|array|exists:bravo_boats,id',
            'tour_services' => 'nullable|array|exists:bravo_tours,id',
        ]);
        $emails = $request->input('emails');
        $role = $request->input('role');
        $res = [];
        foreach($emails as $email) {
            $user = User::where('email', $email)->first();
            if(!empty($user)) {
                $services = [
                    'hotel' => 'hotel_services',
                    'tour' => 'tour_services',
                    'car' => 'car_services',
                    'space' => 'space_services',
                    'event' => 'event_services',
                    'flight' => 'flight_services',
                    'boat' => 'boat_services',
                ];

                foreach ($services as $type => $requestKey) {
                    if ($request->has($requestKey)) {
                        foreach ($request->input($requestKey) as $serviceId) {
                            BusinessRelation::updateOrCreate(
                                [
                                    'business_id' => $businessId,
                                    'role_id' => $role,
                                    'user_id' => $user->id,
                                    'service_id' => $serviceId,
                                    'service_type' => $type,
                                ],
                                [
                                    'status' => 'approved',
                                ]
                            );
                        }
                    }
                }
                // $user->update(['role_id' => 2]);
                try {
                    event(new UserInvite($user, $business, 'add'));
                } catch(\Exception $e) {
                    info($e);
                }
            }
        }
        return redirect()->back()->with('success', 'Updated successfully!');
    }

    public function updateUserServices(Request $request) {
        $business_id = session('active_business_id');

        $user = User::with(['businessRelations' => function($query) use ($business_id) {
            $query->where('business_id', $business_id);
        }])->find($request->user_id);

        $serviceTypes = [
            'hotel_services',
            'car_services',
            'boat_services',
            'space_services',
            'event_services',
            'tour_services',
            // 'flight_services'
        ];

        foreach ($serviceTypes as $type) {
            $incomingServices = $request->input($type, []);

            $existingServices = $user->businessRelations->where('service_type', str_replace('_services', '', $type))->pluck('service_id')->toArray();

            $servicesToAdd = array_diff($incomingServices, $existingServices);
            $servicesToRemove = array_diff($existingServices, $incomingServices);

            foreach ($servicesToAdd as $serviceId) {
                $user->businessRelations()->create([
                    'business_id' => $business_id,
                    'role_id' => $request->role,
                    'service_id' => $serviceId,
                    'service_type' => str_replace('_services', '', $type),
                    'status' => 'approved'
                ]);
            }

            if (!empty($servicesToRemove)) {
                $user->businessRelations()
                    ->where('business_id', $business_id)
                    ->where('service_type', str_replace('_services', '', $type))
                    ->whereIn('service_id', $servicesToRemove)
                    ->delete();
            }

            // Handle full deletion when type is missing from request
            if (!array_key_exists($type, $request->all())) {
                $user->businessRelations()
                    ->where('business_id', $business_id)
                    ->where('service_type', str_replace('_services', '', $type))
                    ->delete();
            }
        }

        if($request->input('role') != $user->businessRelations[0]['role_id']) {
            $rows = BusinessRelation::where('business_id', $business_id)
                ->where('user_id', $request->user_id)->update([
                    'role_id' => $request->input('role')
                ]);
        }
        return redirect()->back()->with('success', __('User updated successfully'));
    }

}

