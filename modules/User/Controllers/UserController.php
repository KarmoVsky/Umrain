<?php

namespace Modules\User\Controllers;

use App\Models\Locations\State;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Matrix\Exception;
use Modules\Boat\Models\Boat;
use Modules\Booking\Models\Service;
use Modules\Car\Models\Car;
use Modules\Event\Models\Event;
use Modules\Flight\Models\Flight;
use Modules\FrontendController;
use Modules\Hotel\Models\Hotel;
use Modules\Space\Models\Space;
use Modules\Tour\Models\Tour;
use Modules\User\Events\NewVendorRegistered;
use Modules\User\Events\UserSubscriberSubmit;
use Modules\User\Events\AddBusinessRequest;
use Modules\User\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\User\Models\User;
use Modules\Vendor\Models\VendorRequest;
use Validator;
use Modules\Booking\Models\Booking;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Modules\Booking\Models\Enquiry;
use Illuminate\Support\Str;
use Modules\Booking\Events\BookingUpdatedEvent;
use App\Models\Business;
use App\Models\Service as BusinessServices;
use Illuminate\Support\Facades\DB;

class UserController extends FrontendController
{
    use AuthenticatesUsers;

    protected $enquiryClass;
    private Booking $booking;

    public function __construct(Booking $booking, Enquiry $enquiry)
    {
        $this->enquiryClass = $enquiry;
        parent::__construct();
        $this->booking = $booking;
    }

    public function dashboard(Request $request)
    {
        $this->checkPermission('dashboard_vendor_access');
        $user_id = Auth::id();
        $data = [
            'cards_report'       => $this->booking->getTopCardsReportForVendor($user_id),
            'earning_chart_data' => $this->booking->getEarningChartDataForVendor(strtotime('monday this week'), time(), $user_id),
            'page_title'         => __("Vendor Dashboard"),
            'breadcrumbs'        => [
                [
                    'name'  => __('Dashboard'),
                    'class' => 'active'
                ]
            ]
        ];
        return view('User::frontend.dashboard', $data);
    }

    public function reloadChart(Request $request)
    {
        $chart = $request->input('chart');
        $user_id = Auth::id();
        switch ($chart) {
            case "earning":
                $from = $request->input('from');
                $to = $request->input('to');
                return $this->sendSuccess([
                    'data' => $this->booking->getEarningChartDataForVendor(strtotime($from), strtotime($to), $user_id)
                ]);
                break;
        }
    }

    public function profile(Request $request)
    {
        $user = Auth::user();
        $data = [
            'dataUser'         => $user,
            'page_title'       => __("Profile"),
            'breadcrumbs'      => [
                [
                    'name'  => __('Setting'),
                    'class' => 'active'
                ]
            ],
            'is_vendor_access' => $this->hasPermission('dashboard_vendor_access')
        ];
        return view('User::frontend.profile', $data);
    }

    public function profileUpdate(Request $request)
    {
        if (is_demo_mode()) {
            return back()->with('error', "Demo mode: disabled");
        }
        $user = Auth::user();
        $messages = [
            'user_name.required'      => __('The User name field is required.'),



            'phone.required' => __('The phone number field is required.'),
            'phone.unique' => __('This phone number is already in use.'),
        ];
        $request->validate([
            'first_name' => 'required|max:255',
            'last_name'  => 'required|max:255',
            'email'      => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'user_name' => [
                'nullable',
                'max:255',
                'min:4',
                'string',
                'alpha_dash',
                Rule::unique('users')->ignore($user->id)
            ],
            'phone'       => [
                'required',
                Rule::unique('users')->ignore($user->id)
            ],
            'country' => 'required',
            'state' => 'required',

        ], $messages);
        $input = $request->except('bio');
        $user->fill($input);
        $user->bio = clean($request->input('bio'));
        $user->birthday = date("Y-m-d", strtotime($user->birthday));
        $user->user_name = $request->input('user_name') ? Str::slug($request->input('user_name'), "_") : null;
        $user->save();
        return redirect()->back()->with('success', __('Update successfully'));
    }

    public function bookingHistory(Request $request)
    {
        $user_id = Auth::id();
        $data = [
            'bookings' => $this->booking->getBookingHistory($request->input('status'), $user_id),
            'statues'     => config('booking.statuses'),
            'breadcrumbs' => [
                [
                    'name'  => __('Booking History'),
                    'class' => 'active'
                ]
            ],
            'page_title'  => __("Booking History"),
        ];
        return view('User::frontend.bookingHistory', $data);
    }

    public function subscribe(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|max:255'
        ]);
        $check = Subscriber::withTrashed()->where('email', $request->input('email'))->first();
        if ($check) {
            if ($check->trashed()) {
                $check->restore();
                return $this->sendSuccess([], __('Thank you for subscribing'));
            }
            return $this->sendError(__('You are already subscribed'));
        } else {
            $a = new Subscriber();
            $a->email = $request->input('email');
            $a->first_name = $request->input('first_name');
            $a->last_name = $request->input('last_name');
            $a->save();

            event(new UserSubscriberSubmit($a));

            return $this->sendSuccess([], __('Thank you for subscribing'));
        }
    }

    public function upgradeVendor(Request $request)
    {
        $user = Auth::user();
        $vendorRequest = VendorRequest::query()->where("user_id", $user->id)->where("status", "pending")->first();
        if (!empty($vendorRequest)) {
            return redirect()->back()->with('warning', __("You have just done the become vendor request, please wait for the Admin's approved"));
        }
        $vendorRequest = VendorRequest::query()->where("user_id", $user->id)->where("status", "approved")->first();
        if (!empty($vendorRequest)) {
            return redirect()->back()->with('warning', __("You are already a vendor"));
        }
        // check vendor auto approved
        $vendorAutoApproved = setting_item('vendor_auto_approved');
        $dataVendor['role_request'] = setting_item('vendor_role');
        if ($vendorAutoApproved) {
            if ($dataVendor['role_request']) {
                $user->assignRole($dataVendor['role_request']);
            }
            $dataVendor['status'] = 'approved';
            $dataVendor['approved_time'] = now();
        } else {
            $dataVendor['status'] = 'pending';
        }
        $vendorRequestData = $user->vendorRequest()->save(new VendorRequest($dataVendor));
        // try {
        //     event(new NewVendorRegistered($user, $vendorRequestData));
        // } catch (Exception $exception) {
        //     Log::warning("NewVendorRegistered: " . $exception->getMessage());
        // }
        return redirect()->back()->with('success', __('Request vendor success!'));
    }



    public function showApplicationForm(Request $request)
    {
        $user = Auth::user();
        $business = Business::query()
            ->where('create_user', $user->id)
            ->where(function ($query) {
                $query->where('status', 'pending')
                    ->orWhere('status', 'approved');
            })
            ->first();
        if (!empty($business)) {
            switch ($business->status) {
                case 'pending':
                    return redirect()->route('user.profile.index')->with('warning', __("You have already submitted a request. Please wait for Admin approval."));
                    break;
                case 'approved':
                    return redirect()->back()->with('warning', __("You are already a vendor"));
                    break;
                default:
                    return redirect()->back();
                    break;
            }
        }

        $message = "";
        $type = '';
        if (!$user->phone) {
            $type = 'phone';
            $message = __("Phone is required field");
        } else if (!$user->email) {
            $type = 'email';
            $message = __("Email is Required");
        } else if (!$user->country) {
            $type = 'country';
            $message = __("The Country is required");
        } else if (!$user->state) {
            $type = 'state';
            $message = __("The State is required");
        } else if (!$user->city && State::where('name', $user->state)->first()->cities->count() > 0) {
            $type = 'city';
            $message = __("The City is required");
        }

        if ($message != "") {
            return redirect()->back()->with($type, $message);
        }

        $services = BusinessServices::pluck('name', 'id');

        $data = [
            'user'     => $user,
            'services' => $services->toArray(),
        ];
        return view('User::frontend.application_form', $data);
    }


    public function submitApplicationForm(Request $request)
    {
        $user = Auth::user();

        $business = Business::where('create_user', $user->id)->whereIn('status', ['approved', 'pending'])->first();
        if ($business) {
            return redirect()->back()->with('error', __('You already have an approved business record.'));
        }

        $request->validate([
            'business_name' => 'required|string|max:255',
            'phone' => [
                'required',
                'min:9',
                Rule::unique('users')->ignore($user->id)
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
                Rule::unique('anisth_business'),
            ],
            'country' => 'required|string',
            'state' => 'required|string',
            'city' => 'required|string',
            'term' => 'accepted',
            'services' => 'required|array|min:1',
            'services.*' => 'string|distinct',
        ], [
            'services.required' => __('You should select at least one service'),
            'term.accepted' => __('You must accept the Terms and Privacy Policy to proceed.'),
            'email.unique' => __('This email is already taken by another user, please choose a different one.'),
        ]);

        if ($user->email === $request->email) {
            return back()->withErrors([
                'email' => __('You are using this email for your personal account. Please enter a different email address for your business.')
            ]);
        }


        try {
            DB::beginTransaction();
            $business = Business::create([
                'business_name' => $request->input('business_name'),
                'business_name_id' => $request->input('business_name_id'),
                'country_code' => $request->input('country_code'),
                'phone'        => $request->input('phone'),
                'email'        => $request->input('email'),
                'address'      => $request->input('address'),
                'address2'     => $request->input('address2'),
                'status'       => 'pending',
                'country'      => $request->input('country'),
                'state'        => $request->input('state'),
                'city'         => $request->input('city'),
                'zip_code'     => $request->input('zip_code'),
                'create_user'      => $user->id,
            ]);
            $servicesIds = BusinessServices::pluck('id')->toArray();
            $services = $request->input('services', []);
            foreach ($services as $serviceId) {
                if (in_array($serviceId, $servicesIds)) {
                    $business->services()->attach($serviceId, ['status' => 'pending']);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            Log::error("Failed to update user: " . $e->getMessage());
            return redirect()->back()->with('error', __('Failed to update user information. Please try again.'));
        }

        $vendorAutoApproved = setting_item('vendor_auto_approved');
        $dataVendor['role_request'] = setting_item('vendor_role');
        if ($vendorAutoApproved) {
            if ($dataVendor['role_request']) {
                $user->assignRole($dataVendor['role_request']);
            }
            $dataVendor['status'] = 'approved';
            $dataVendor['approved_time'] = now();
        } else {
            $dataVendor['status'] = 'pending';
        }
        $vendorRequestData['status'] = 'pending';

        // $vendorRequestData = $user->vendorRequest()->save(new VendorRequest($dataVendor));

        try {
            $admins = User::where('role_id', 1)->get();
            event(new AddBusinessRequest($admins, $business));
        } catch (Exception $exception) {
            Log::warning("NewVendorRegistered: " . $exception->getMessage());
        }
        return redirect()->route('user.profile.index')->with('success', __('Request vendor success!'));
    }



    public function permanentlyDelete(Request $request)
    {
        if (is_demo_mode()) {
            return back()->with('error', "Demo mode: disabled");
        }
        if (!empty(setting_item('user_enable_permanently_delete'))) {
            $user = Auth::user();
            \DB::beginTransaction();
            try {
                Service::where('author_id', $user->id)->delete();
                Tour::where('author_id', $user->id)->delete();
                Car::where('author_id', $user->id)->delete();
                Space::where('author_id', $user->id)->delete();
                Hotel::where('author_id', $user->id)->delete();
                Event::where('author_id', $user->id)->delete();
                Boat::where('author_id', $user->id)->delete();
                Flight::where('author_id', $user->id)->delete();
                $user->sendEmailPermanentlyDelete();
                $user->delete();
                \DB::commit();
                Auth::logout();
                if (is_api()) {
                    return $this->sendSuccess([], 'Deleted');
                }
                return redirect(route('home'));
            } catch (\Exception $exception) {
                \DB::rollBack();
            }
        }
        if (is_api()) {
            return $this->sendError('Error. You can\'t permanently delete');
        }
        return back()->with('error', __('Error. You can\'t permanently delete'));
    }

    public function changeBookingStatusByCustomer($booking_id, Request $request)
    {
        $status = $request->input('status');

        if (!empty(setting_item("hotel_allow_customer_can_change_their_booking_status")) && !empty($status) && !empty($booking_id)) {
            $query = Booking::where("id", $booking_id);
            $query->where("customer_id", Auth::id());
            $item = $query->first();
            if (!empty($item)) {
                if ($status === 'cancelled') {
                    $item->status = $status;
                    $item->save();
                    event(new BookingUpdatedEvent($item));
                    return redirect()->back()->with('success', __('Booking status updated successfully!'));
                } else {
                    return redirect()->back()->with('error', __('You can only cancel the booking.'));
                }
            }
            return redirect()->back()->with('error', __('Booking not found!'));
        }
        return redirect()->back()->with('error', __('You do not have permission to change booking status.'));
    }



    public function business(Request $request)
    {
        $user = Auth::user();
        $id = session('active_business_id');
        $roleId = $user->role_id;

        $hasPermission = DB::table('core_role_permissions')
            ->where('role_id', $roleId)
            ->where('permission', 'business_update')
            ->exists();

        $row = Business::with(['user'])->where('id', $id)->first();

        if (empty($row)) {
            return redirect()->route('user.vendor.index');
        }

        $all_services = BusinessServices::pluck('name', 'id');

        $data = [
            'dataUser'         => $user,
            'hasPermission' => $hasPermission,
            'all_services' => $all_services->toArray(),
            'request_services' => $row->services->pluck('id')->toArray(),
            'page_title'       => __("Business Profile"),
            'row' => $row,
            'breadcrumbs'      => [
                [
                    'name'  => __('Setting'),
                    'class' => 'active'
                ]
            ],
            'is_vendor_access' => $this->hasPermission('dashboard_vendor_access')
        ];
        // dd($row->services->pluck('id')->toArray());
        return view('User::frontend.businessProfile', $data);
    }

    public function businessUpdate(Request $request)
    {
        if (is_demo_mode()) {
            return back()->with('error', "Demo mode: disabled");
        }

        $user = Auth::user();
        $id = session('active_business_id'); // استرجاع ID العمل النشط من الجلسة

        // التحقق من أن هناك نشاطًا تجاريًا بهذا ID
        $business = Business::find($id);
        if (!$business) {
            return redirect()->back()->with('error', __('Business not found!'));
        }

        // التحقق من صحة البيانات المدخلة
        $request->validate([
            'business_name' => 'required|string|max:255',
            'phone' => [
                'nullable',
                'min:9',
                Rule::unique('anisth_business')->ignore($id, 'id'),
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        // بدء المعاملة (Transaction) لضمان سلامة البيانات
        DB::transaction(function () use ($request, $business, $id) {
            // تحديث بيانات العمل التجاري
            $business->update([
                'business_name' => $request->business_name,
                'business_name_id' => $request->business_licience_id,
                'email' => $request->email,
                'email_verified_at' => $request->is_email_verified ? now() : null,
                'phone' => $request->phone,
                'country_code' => $request->country_code,
                'address' => $request->address,
                'address2' => $request->address2,
                'country' => $request->country,
                'state' => $request->state,
                'city' => $request->city,
                'zip_code' => $request->zip_code,
                'avatar_id' => $request->avatar_id,
                'approved_by' => Auth::id(),
                'approved_time' => now(),
            ]);

            // تحديث الخدمات المرتبطة بالنشاط التجاري
            $servicesIds = $request->input('services', []);
            $existingServiceIds = DB::table('anisth_business_service')
                ->where('business_id', $id)
                ->pluck('service_id')
                ->toArray();

            // تحديد الخدمات الجديدة التي يجب إضافتها
            $newServices = array_diff($servicesIds, $existingServiceIds);
            foreach ($newServices as $serviceId) {
                DB::table('anisth_business_service')->insert([
                    'business_id' => $id,
                    'service_id' => $serviceId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // تحديد الخدمات التي يجب إزالتها
            $removedServices = array_diff($existingServiceIds, $servicesIds);
            DB::table('anisth_business_service')
                ->where('business_id', $id)
                ->whereIn('service_id', $removedServices)
                ->delete();
        });

        return redirect()->back()->with('success', __('Updated successfully!'))->with('business', $business);
    }
}
