@extends('layouts.user')
@section('content')
{{-- @php
    dd(request()->route());
@endphp --}}
<div class="mt-3">
    @include('admin.message')
</div>
<div class="d-flex justify-content-between align-items-center">
    <h2 class="title-bar no-border-bottom">
        {{__("All Users")}}
    </h2>
    <div class="title-actions">
        @if (Auth::user()->hasPermission('dashboard_vendor_access')&& Auth::user()->hasPermission('user_create'))
            <a href="" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#exampleModalCenter">{{ __('Invite member')}}</a>
        @endif
        <a class="btn btn-warning btn-icon btn-sm" href="{{ route("user.admin.export") }}" target="_blank" title="{{ __("Export to excel") }}">
            <i class="icon ion-md-cloud-download"></i> {{ __("Export to excel") }}
        </a>
    </div>
</div>
<div class="filter-div filter-div-news d-flex justify-content-between  mb-3">
    <div class="col-left">
        {{-- @if(!empty($rows))
            <form method="get" action="{{route('user.vendor.bulkEdit')}}"
                  class="filter-form filter-form-left d-flex justify-content-start">
                {{csrf_field()}}
                <select name="action" class="form-control mr-3">
                    <option value="">{{__(" Bulk Actions ")}}</option>
                    <option value="approved">{{__(" Publish ")}}</option>
                    <option value="draft">{{__(" Move to Draft ")}}</option>
                    <option value="delete">{{__(" Delete ")}}</option>
                </select>
                <button data-confirm="{{__("Do you want to delete?")}}" class="py-2 btn-info btn btn-icon dungdt-apply-form-btn" type="button">{{__('Apply')}}</button>
            </form>
        @endif --}}
    </div>
    <div class="col-left">
        <form method="get" action="{{route('user.vendor.index')}} " class="filter-form filter-form-right d-flex justify-content-end flex-column flex-sm-row" role="search">
            <input type="text" name="s" value="{{ Request()->s }}" placeholder="{{__('Search by name')}}"
                   class="form-control mr-3">
            <div class="flex-shrink-0">
                <button class="btn-info btn btn-icon btn_search py-2" type="submit">{{__('Search User')}}</button>
            </div>
        </form>
    </div>
</div>
<div class="panel mt-3">
    <div class="panel-body">
        <form action="" class="bravo-form-item">
            <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th width="60px" style="border-top: 0"><input type="checkbox" class="check-all" ></th>
                    <th style="border-top: 0; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">{{__('Name')}}</th>
                    <th style="border-top: 0">{{__('Email')}}</th>
                    <th style="border-top: 0">{{__('Phone')}}</th>
                    <th style="border-top: 0; width: 150px">{{__('Role')}}</th>
                    <th style="border-top: 0" class="date">{{ __('Date')}}</th>
                    <th style="border-top: 0">{{__('Status')}}</th>
                    <th style="border-top: 0"></th>
                    <th style="border-top: 0"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                @php
                     $countryData = json_decode($row->vendor?->country_code, true);
                     $phoneCode = $countryData['phoneCode'] ?? null;
                @endphp
                    <tr>
                        <td><input type="checkbox" name="ids[]" value="{{$row->vendor->id ?? ''}}" class="check-item"></td>
                        <td class="title" style="white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                            <a class="title" href="{{ Auth::user()->hasPermission('dashboard_vendor_access') && Auth::user()->hasPermission('user_update')
                                ? route('user.vendor.details', ['id' => $row->vendor->id])
                                : '#' }}"
                                style="{{ !(Auth::user()->hasPermission('dashboard_vendor_access') && Auth::user()->hasPermission('user_delete')) ? 'pointer-events: none; cursor: default;' : '' }}">
                                {{$row->vendor->first_name.' '.$row->vendor->last_name}}</a>
                        </td>
                        <td style="max-width: 200px;word-wrap: break-word;">{{$row->vendor->email}}
                            @if($row->vendor->email_verified_at)
                                <i class="fa fa-check-circle text-success" title="{{__("Verified")}}"></i>
                            @else
                                <i class="fa fa-info-circle text-warning" title="{{__("Not Verified")}}"></i>
                            @endif
                        </td>
                        <td>{{$phoneCode.' '}}{{$row->vendor->phone}}</td>
                        <td>
                            {{$row->role->name ?? ''}}
                        </td>
                        <td>{{ display_date($row->vendor->created_at)}}</td>
                        <td><span class="badge badge-{{ $row->status }}">{{ $row->status }}</span></td>
                        <td>
                            @if($row->user_id != Auth::id())
                              @if (Auth::user()->hasPermission('dashboard_vendor_access')&& Auth::user()->hasPermission('user_delete'))
                                  <span role="button" class="btn btn-danger btn-sm btn-remove-item cursor-pointer" data-toggle="modal" data-target="#deleteConfirmModal">
                                   <i class="fa fa-trash"></i>
                                  </span>
                              @endif

                            @endif
                        </td>
                        @if(Auth::id() == $business->create_user)
                        <td>
                            @if($row->user_id != $business->create_user)
                            <a href="{{ route('user.vendor.setowner', ['id'=>$row->user_id]) }}"><span role="button" class="btn  btn-sm btn-remove-item cursor-pointer">{{-- <i class="fa fa-user-plus"></i> --}}<i class="fa fa-key" style="color: #d2b960;"></i></span></a>
                            @endif
                        </td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
            </div>
        </form>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">{{ __('Invite member') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('user.vendor.invite') }}" method="POST" id="user-form" class="needs-validation" novalidate>
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="user-search"><strong>{{ __('Search Member') }}</strong></label>
                        <div id="user-search-wrapper" class="d-flex flex-wrap align-items-center">
                            <input type="text" id="user-search" class="form-control mb-2" placeholder="{{ __('Type email to search for members') }}">
                            <div id="selected-users-list" class="d-flex flex-wrap ml-2">

                            </div>
                        </div>
                        <div id="user-error-message" class="text-danger mt-2" style="display: none;">
                            {{ __('Please select at least one user.') }}
                        </div>
                        <div id="error-message" style="color: red; display: none; margin-top: 5px;"></div>
                    </div>
                    <div class="form-group">
                        <label for="roles"><strong>{{ __('Select a role') }}</strong></label>
                        <select name="role" id="role" class="form-control mb-2 w-50" required>
                            <option value="">{{ __('-- Select a role --') }}</option>
                            @foreach ($roles as $role)
                            <option value="{{ $role->id }}" class="">{{ ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                        <label for="" class="text-secondary">{{ __("You can change this later") }}</label>
                    </div>
                    <div class="form-group">
                        <label for="services"><strong>{{ __('Select a service(s)') }}</strong></label>
                        @foreach ($services as $service)
                            <div class="row mt-1" style="border: 0px solid red">
                                <div class="col-2" style="text-align: end; padding: 5px;">
                                    <label for="">{{ ucfirst($service) }}</label>
                                </div>
                                <div class="col-5 pl-0">
                                    <select name="{{ $service }}_locations[]" id="{{ $service }}_locations" class="form-control mb-2 d-inline-block select2 select2_locations" multiple="multiple">
                                        <option value="">{{ __('-- Select location(s) --') }}</option>
                                        @if (isset($locations[$service]))
                                            @foreach ($locations[$service] as $item)
                                                <option value="{{ $item['location_id'] }}">{{ ucfirst($item['location_name']) }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-5 pl-0">
                                    <select name="{{ $service }}_services[]" id="{{ $service }}_services" class="form-control mb-2 select2 select2_services" multiple="multiple">
                                        <option value="">{{ __('-- Select service(s) --') }}</option>
                                    </select>
                                </div>
                            </div>
                        @endforeach
                        <div id="error-services-message" style="color: red; display: none; margin-top: 5px;"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="addButton" type="submit" class="btn btn-primary">{{ __('Add') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- <div class="modal fade" id="setOwner" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">{{ __('Set Owner') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('user.vendor.setowner') }}" method="POST" id="owner-form" class="needs-validation" novalidate>
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <div id="user-error-message" class="text-danger mt-2" style="display: none;">
                            {{ __('Please select at least one user.') }}
                        </div>
                        <div id="error-message" style="color: red; display: none; margin-top: 5px;"></div>
                    </div>
                    <div class="form-group">
                        <label for="roles"><strong>{{ __('Select new owner') }}</strong></label>
                        <select name="ownerid" id="ownerid" class="form-control mb-2" required>
                            <option value="">{{ __('-- Select new owner --') }}</option>
                            @foreach ($rows as $row)
                                 @if ($row->vendor->id  != Auth::user()->id)
                                     <option value="{{ $row->vendor->id }}" class="">{{ $row->vendor->email}}</option>
                                 @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="setButton" type="submit" class="btn btn-primary">{{ __('Set') }}</button>
                </div>
            </form>
        </div>
    </div>
</div> --}}

@if($row->user_id != Auth::id())
<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">×</button>
                <div class="bootbox-body">{{ __('Do you want to delete?') }}</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-defautl btn-sm" data-dismiss="modal">{{ __('Cancel') }}</button>
                <a id="confirmDeleteBtn" href="{{ route('user.vendor.delete', ['id'=>$row->user_id]) }}" class="btn btn-primary btn-sm">{{ __('Confirm') }}</a>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // تفعيل مكتبة Select2 على جميع القوائم المنسدلة التي تحتوي على class="select2"
        $('.select2').select2({
            placeholder: "{{ __('-- Select location(s) --') }}",
            allowClear: true
        });

        // تحويل بيانات المواقع والخدمات من PHP إلى JavaScript باستخدام JSON
        var locationsData = @json($locations);

        console.log('تم تحميل بيانات المواقع والخدمات: ', locationsData);

        // تفعيل التغيير في المواقع لتحديث قائمة الخدمات فقط لقوائم الخدمات
        $('.select2_locations').on('change', function() {
            console.log('تم تغيير الموقع!');

            var selectedLocations = $(this).val() || [];
            var serviceType = $(this).attr('id').split('_')[0];
            var servicesDropdown = $("#" + serviceType + "_services");

            // الاحتفاظ بالخدمات المختارة مسبقًا
            var selectedServices = servicesDropdown.val() || [];

            // إعادة بناء قائمة الخدمات بناءً على المواقع المختارة
            var newServices = new Set();

            selectedLocations.forEach(function(locationId) {
                locationsData[serviceType].forEach(function(location) {
                    if (location.location_id == locationId) {
                        location.services.forEach(function(serviceItem) {
                            newServices.add(serviceItem);
                        });
                    }
                });
            });

            // تحديث قائمة الخدمات في الـ select
            servicesDropdown.empty().append('<option value="">{{ __('-- Select service(s) --') }}</option>');

            newServices.forEach(function(serviceItem) {
                servicesDropdown.append('<option value="' + serviceItem.id + '">' + serviceItem.name + '</option>');
            });

            // إزالة الخدمات المختارة التي لم تعد مرتبطة بالمواقع المحددة
            selectedServices = selectedServices.filter(serviceId => {
                return [...newServices].some(serviceItem => serviceItem.id == serviceId);
            });

            servicesDropdown.val(selectedServices).trigger('change');
        });
        $('form').on('submit', function(e) {
            var selectedServices = [];

            $('.select2_services').each(function() {
                var selectedValues = $(this).val();
                if (selectedValues && selectedValues.length > 0) {
                    selectedServices.push(selectedValues);
                }
            });

            if (selectedServices.length === 0) {
                e.preventDefault();

                const errorMessageElement = document.getElementById('error-services-message');
                errorMessageElement.textContent = "{{ __('Please select at least one service') }}";
                errorMessageElement.style.display = 'block';

                setTimeout(() => {
                    const errorMessageElement = document.getElementById('error-services-message');
                    console.log(errorMessageElement);
                    errorMessageElement.style.display = 'none';
                }, 3000);
            }
        });

    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('user-search');
    const selectedUsersList = document.getElementById('selected-users-list');
    const addButton = document.getElementById('addButton');
    let selectedUsers = [];

    searchInput.addEventListener('input', function () {
        if (searchInput.value.trim() != '') {
            addButton.innerHTML = "{{ __('Add') }}";
        } else if(searchInput.value.trim() === '') {
            addButton.innerHTML = "{{ __('Invite') }}";
        }
    });

    searchInput.addEventListener('keydown', function (event) {
        if (event.key === ' ' || event.key === 'Enter') {
            const email = searchInput.value.trim();
            if (validateEmail(email)) {
                addEmailToList(email);
                searchInput.value = '';
            } else {
                showValidationError();
            }
            event.preventDefault();
        }
    });

    addButton.addEventListener('click', function (event) {
        if (selectedUsers.length === 0 && searchInput.value.trim() === '') {
            event.preventDefault();
            showValidationError('{{ __("Please enter a valid email") }}');
        }
        if(searchInput.value.trim() != '') {
            event.preventDefault();
            if(!validateEmail(searchInput.value.trim())) {
                showValidationError('{{ __("Please enter a valid email") }}');
            } else {
                const email = searchInput.value.trim();
                addEmailToList(email);
                searchInput.value = '';
            }
        }
    });

    function validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function addEmailToList(email) {
        if (selectedUsers.some(user => user.email === email)) {
            showValidationError('{{ __("Email already added.") }}');
            return;
        }

        const badge = document.createElement('div');
        badge.className = 'badge-item d-flex justify-content-between align-items-center';
        badge.style.marginRight = '7px';

        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.className = 'btn btn-sm remove-email';
        removeButton.textContent = '×';
        removeButton.addEventListener('click', function () {
            badge.remove();
            selectedUsers = selectedUsers.filter(user => user.email !== email);

        });
        badge.appendChild(removeButton);

        const emailSpan = document.createElement('span');
        emailSpan.textContent = email;
        badge.appendChild(emailSpan);

        const validationIcon = document.createElement('i');
        validationIcon.className = 'ml-2 fa';
        badge.appendChild(validationIcon);
        selectedUsersList.appendChild(badge);
        selectedUsers.push({ email });
        if(selectedUsers.length > 0) {
            addButton.innerHTML = "{{ __('Invite') }}";
        }

        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'emails[]';
        hiddenInput.value = email;
        badge.appendChild(hiddenInput);
        validateEmailOnServer(email, validationIcon);
    }

    function validateEmailOnServer(email, iconContainer) {
        fetch(`/user/vendors/search?query=${encodeURIComponent(email)}`)
            .then(response => response.json())
            .then(data => {
                iconContainer.innerHTML = '';
                if (data.exists) {
                    if (!data.email_verified_at) {
                        const yellowIcon = document.createElement('i');
                        yellowIcon.classList.add('fa', 'fa-info-circle', 'text-warning');
                        yellowIcon.title = 'Email exists but is not verified';
                        iconContainer.appendChild(yellowIcon);
                    } else {
                        const greenIcon = document.createElement('i');
                        greenIcon.classList.add('fa', 'fa-check-circle', 'text-success');
                        greenIcon.title = 'Email exists and is verified';
                        iconContainer.appendChild(greenIcon);
                    }
                } else {
                    const redIcon = document.createElement('i');
                    redIcon.classList.add('fa', 'fa-times-circle', 'text-danger');
                    redIcon.title = 'Email does not exist';
                    iconContainer.appendChild(redIcon);
                }
            })
            .catch(error => {
                console.error('Error validating email:', error);
                const warningIcon = document.createElement('i');
                warningIcon.classList.add('fa', 'fa-exclamation-circle', 'text-warning');
                warningIcon.title = 'Email not verified';
                iconContainer.innerHTML = '';
                iconContainer.appendChild(warningIcon);
            });
    }

    function showValidationError(message) {
        if(typeof message != 'undefined') {
            const errorMessageElement = document.getElementById('error-message');
            errorMessageElement.textContent = message;
            errorMessageElement.style.display = 'block';
        }
        searchInput.classList.add('is-invalid');

        setTimeout(() => {
            const errorMessageElement = document.getElementById('error-message');
            errorMessageElement.style.display = 'none';
            searchInput.classList.remove('is-invalid');
        }, 3000);
    }
});

</script>

<style>
    #user-results {
        position: absolute;
        width: 100%;
        z-index: 10;
    }
    .list-group-item {
        cursor: pointer;
    }
    .list-group-item:hover {
        background-color: #f0f0f0;
    }
    #selected-users-list .badge-item {
        display: flex;
        align-items: center;
        background-color: #f0f0f0;
        border: 1px solid #ddd;
        border-radius: 2px;
        padding: 2px 5px;
        margin: 5px 0;
    }
    .badge-item .remove-email {
        font-weight: bold;
        cursor: pointer;
        background: none;
        border: none;
    }
    .badge-item .remove-email:hover {
    }
    #addButton:focus,
    #addButton:active {
        background-color: #198754 !important;
        outline: #198754 !important
    }
    .badge-item .remove-email {
    font-weight: bold;
    cursor: pointer;
    background: none;
    border: none;
}
.fa {
    margin-left: 5px;
}
.fa-shield-check {
    content: url('fa fa-user');
}
.fa-shield {
    content: url('fa fa-user');
}


</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<style>
    /* تأكد أن القوائم تأخذ العرض الكامل */
    .select2 {
        width: 100% !important;
    }

    /* تنسيق القوائم المنسدلة داخل الـ row */
    .row.mb-3 {
        display: flex;
        align-items: center;
    }

    /* جعل حقل الخدمة يأخذ العرض المناسب */
    .col-2 {
        text-align: right;
        padding-right: 15px;
    }

    /* لضبط عرض select2 وضمان عدم تداخلها */
    .col-5.pl-0 {
        padding-left: 0;
    }
</style>
