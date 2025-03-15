<?php

namespace Modules\User\Admin;

use App\Models\Business;
use App\Models\BusinessRelation;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Modules\AdminController;
use Modules\User\Models\Role;
use Modules\User\Models\User;
use Modules\User\Events\ApprovalRejectionBusines;

class VendorController extends AdminController
{
    public function index()
    {
        $this->checkPermission('user_view');

        $listBusiness = Business::query();
        $data = [
            'rows' => $listBusiness->whereHas('user')->whereNotIn('status', ['deleted'])->with(['user', 'approvedBy'])->orderBy('id', 'desc')->paginate(20),
            'roles' => Role::all(),
        ];
        return view('User::admin.vendor.index', $data);
    }
    public function userAddBusinessApproved(Request $request)
    {
        $this->checkPermission('user_create');
        $ids = $request->input('ids');
        $action = $request->input('action');
        if (is_string($ids)) {
            $ids = json_decode($ids, true);
        }
        if (empty($ids)) {
            return redirect()->back()->with('error', __('Select at leas 1 item!'));
        }

        if (empty($action)) {
            return redirect()->back()->with('error', __('Select an Action!'));
        }

        switch ($action) {
            case "delete":
                foreach ($ids as $id) {
                    $query = Business::find($id);
                    if (!empty($query)) {
                        $query->update(['status' => 'deleted']);
                        event(new ApprovalRejectionBusines($query->user, $query));
                        $servicesIds = $query->services->pluck('id');
                        $query->services()->detach($servicesIds);
                        $query->businessRelations()->delete();
                        $query->delete();
                    }
                }
                return redirect()->back()->with('success', __('Deleted success!'));
                break;
            case "draft":
                foreach ($ids as $id) {
                    $query = Business::find($id);
                    if (!empty($query)) {
                        $query->update(['status' => $action]);
                    }
                }
                return redirect()->back()->with('success', __('Drafted successfully!'));
                break;
            case 'approved':
                foreach ($ids as $id) {
                    $businessRequest = Business::find($id);
                    if (!empty($businessRequest)) {
                        try {
                            DB::beginTransaction();
                            $servicesIds = $businessRequest->services->pluck('id');
                            $businessRequest->services()->updateExistingPivot($servicesIds, ['status' => 'approved']);
                            $businessRequest->update(['status' => $action, 'approved_by' => Auth::id(), 'approved_time' => now()]);
                            $businessRelation = BusinessRelation::firstOrCreate([ // temporary row will deleted later
                                'business_id' => $businessRequest->id,
                                'role_id' => setting_item('vendor_role'),
                                'user_id' => $businessRequest->create_user,
                                'service_type' => 'owner',
                                'status' => 'approved'
                            ]);
                            $user = User::find($businessRequest->create_user);
                            if (!empty($user)) {
                                $user->assignRole(setting_item('vendor_role'));
                                $user->update(['business_name' => $businessRequest->business_name]);
                            }
                            DB::commit();
                        } catch (\Exception $e) {
                            DB::rollback();
                        }
                        event(new ApprovalRejectionBusines($user, $businessRequest));
                    }
                }
                return redirect()->back()->with('success', __('Updated successfully!'));
                break;
            default:
                return redirect()->back();
                break;
        }
    }
    public function userAddBusinessApprovedId($id)
    {

        $this->checkPermission('user_create');
        if (empty($id)) {
            return redirect()->back()->with('error', __('Error'));
        }

        $businessRequest = Business::find($id);
        if (!empty($businessRequest)) {
            $servicesIds = $businessRequest->services->pluck('id');
            $businessRequest->services()->updateExistingPivot($servicesIds, ['status' => 'approved']);
            $businessRequest->update(['status' => 'approved', 'approved_time' => now(), 'approved_by' => Auth::id()]);
            $businessRelation = BusinessRelation::firstOrCreate([ // temporary row will deleted later
                'business_id' => $businessRequest->id,
                'role_id' => setting_item('vendor_role'),
                'user_id' => $businessRequest->create_user,
                'service_type' => 'owner',
                'status' => 'approved'
            ]);
            $user = User::find($businessRequest->create_user);
            if (!empty($user)) {
                $user->assignRole(setting_item('vendor_role'));
                $user->update(['business_name' => $businessRequest->business_name]);
            }

            event(new ApprovalRejectionBusines($user, $businessRequest));
            return redirect()->route('user.admin.vendor.index')->with('success', __('Updated successfully!'));
        }
        return redirect()->back()->with('error', __('Error!'));
    }
    public function userAddBusinessApprovedId2(Request $request)
    {
        $this->checkPermission('user_create');
        $status = $request->input('status');
        $servicesIds = $request->input('services');
        $id = $request->input('id');
        $user_request_services_ids = DB::table('anisth_business_service')->where('business_id', $id)->get()->pluck('service_id')->toArray();
        $commonIds = array_values(array_intersect($servicesIds, $user_request_services_ids));
        if (empty($status)) {
            return redirect()->route('user.admin.vendor.index')->with('error', __('Select an Action!'));
        }
        switch ($status) {
            case "delete":
                $query = Business::find($id);
                if (!empty($query)) {
                    $query->services()->detach();
                    $query->update(['status' => 'deleted']);
                    event(new  ApprovalRejectionBusines($query->user, $query));
                    $query->delete();
                }
                return redirect()->route('user.admin.vendor.index')->with('success', __('Deleted success!'));
                break;
            case 'draft':
            case 'approved':
                $query = Business::find($id);
                $user = $query->user;

                $request->validate([
                    'business_name' => 'required|string|max:255',
                    // 'phone' => [
                    //     'nullable',
                    //     'string',
                    //     'min:9',
                    //     Rule::unique('anisth_business', 'phone')->ignore($id),
                    // ],

                    'phone' => [
                        'nullable',
                        'string',
                        'min:9',
                        Rule::unique('anisth_business')
                            ->where(function ($query) use ($request) {
                                return $query->where('country_code', $request->country_code);
                            })
                            ->ignore($id), // Ignore the current business when updating
                    ],

                    'email' => [
                        'required',
                        'email',
                        'max:255',
                        Rule::unique('users')->ignore($user->id),
                    ],
                    'country' => 'required|string',
                    'state' => 'required|string',
                    'city' => 'required|string',
                    'services' => 'required|array|min:1',
                    'services.*' => 'string|distinct',
                ], [
                    'services.required' => __('You should select at least one service'),
                    'term.accepted' => __('You must accept the Terms and Privacy Policy to proceed.'),
                    'email.unique' => __('This email is already taken by another user, please choose a different one.'),
                    'phone.unique' => __('This phone number is already taken. Please enter a different one.'),
                    'phone.min' => __('The phone number must be at least 9 digits long.'),
                ]);
                $exists = Business::where('country_code', $request->country_code)
                    ->where('phone', $request->phone)
                    ->where('id', '!=', $id)
                    ->exists();

                if ($exists) {
                    return redirect()->back()->withErrors([
                        'phone' => __('This phone number is already registered in this country.'),
                    ]);
                }
                if ($user->email === $request->email) {
                    return back()->withErrors([
                        'email' => __('You are using this email for your personal account. Please enter a different email address for your business.'),
                    ]);
                }
                if (!empty($query)) {
                    DB::beginTransaction();
                    $query->update([
                        'business_name' => $request->business_name,
                        'business_name_id' => $request->business_licience_id,
                        'country_code' => $request->country_code,
                        'phone' => $request->phone ?: null,
                        'email' => $request->email,
                        'email_verified_at' => $request->is_email_verified ? now() : null,
                        'address' => $request->address,
                        'address2' => $request->address2,
                        'status' => $status,
                        'country' => $request->country,
                        'state' => $request->state,
                        'city' => $request->city,
                        'zip_code' => $request->zip_code,
                        'avatar_id' => $request->avatar_id,
                        'approved_by' => Auth::id(),
                        'approved_time' => now(),
                    ]);
                    if ($status === 'approved') {
                        $query->services()->updateExistingPivot($commonIds, ['status' => 'approved']);
                        $query->services()->detach(array_diff($user_request_services_ids, $servicesIds)); //delete services that are not accepted by admin
                        $query->services()->attach(array_diff($servicesIds, $user_request_services_ids), ['status' => 'approved']); // add services that the admin has added himself
                        // $user = User::find($query->user_id);
                        $businessRelation = BusinessRelation::firstOrCreate([ // temporary row will deleted later
                            'business_id' => $query->id,
                            'role_id' => setting_item('vendor_role'),
                            'user_id' => $query->create_user,
                            'service_type' => 'owner',
                            'status' => 'approved'
                        ]);
                        if (!empty($user)) {
                            try {
                                $user->assignRole(setting_item('vendor_role'));
                                $user->update([
                                    $user->vendor_commission_type           = $request->vendor_commission_type,
                                    $user->vendor_commission_amount         = $request->vendor_commission_amount,
                                    $user->vendor_commission_calculate_way  = $request->vendor_commission_calculate_way,
                                    $user->vendor_commission_calculate_time = $request->vendor_commission_calculate_time,
                                    $user->per_person                       = $request->per_person,
                                ]);
                            } catch (\Exception $e) {
                                DB::rollback();
                                return redirect()->route('user.admin.vendor.index');
                            }
                        }
                        DB::commit();
                        event(new ApprovalRejectionBusines($user, $query));
                        return redirect()->back()->with('success', __('Updated successfully!'));
                        break;
                    } else {
                        DB::commit();
                        return redirect()->back()->with('success', __('Drafted successfully!'));
                        break;
                    }
                }
            default:
                return redirect()->back()->with('error', __('Select an Action!'));
                break;
        }
    }
    public function getBusinessById($id)
    {
        $this->checkPermission('user_view');
        $row = Business::with(['user'])->where('id', $id)->first();
        if (empty($row)) {
            return redirect()->route('user.admin.vendor.index');
        }
        $all_services = Service::pluck('name', 'id');

        $data = [
            'row' => $row,
            'roles' => Role::all(),
            'all_services' => $all_services->toArray(),
            'request_services' => $row->services->pluck('id')->toArray(),
            'breadcrumbs' => [
                [
                    'name' => __("All Vendors"),
                    'url' => route('user.admin.vendor.index'),
                ],
                [
                    'name' => __("Edit User: #:id", ['id' => $row->id]),
                    'class' => 'active',
                ],
            ],
        ];
        return view('User::admin.vendor.detail', $data);
    }
}
