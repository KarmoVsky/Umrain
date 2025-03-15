@extends('admin.layouts.app')
@section('content')
    <div class="container py-5 px-4">
        @include('admin.message')
        <form id="form" action="{{ route('user.admin.business.approvedId2') }}" method="POST" class="needs-validation"
            novalidate>
            @csrf
            <input type="hidden" name="id" value="{{ $row->id }}">
            <div class="row">
                <div class="col-md-9">
                    <div class="panel"
                        style="direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}; text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}">
                        <div class="panel-title" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
                            <h2>{{ __('Edit') }}: {{ $row->business_name }}</h2>
                        </div>
                        <div class="panel-body">
                            <div class="bravo-vendor-form-register">

                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="business_name">{{ __('Business name') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="business_name" id="business_name"
                                                class="form-control @error('business_name') is-invalid @enderror"
                                                value="{{ old('business_name', $row->business_name) }}"
                                                placeholder="{{ __('Business name') }}" required>
                                            @error('business_name')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="business_name_id">{{ __('Business licience id') }}</label>
                                            <input type="text" name="business_licience_id" id="business_licience_id"
                                                class="form-control"
                                                value="{{ old('business_name_id', $row->business_name_id) }}"
                                                placeholder="{{ __('Business licience id') }}">
                                        </div>
                                    </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="phone" style="display: block">{{ __('Business Phone Number') }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" name="phone" id="phone"
                                                    class="form-control @error('phone') is-invalid @enderror"
                                                    value="{{ old('phone', $row->phone) }}"
                                                    placeholder="{{ __('Phone number') }}"
                                                    style="text-align: left; direction: ltr">
                                                @error('phone')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>

                                    <input type="hidden" id="country_code" name="country_code"
                                        value="{{ old('country_code', $row->country_code ?? '') }}">
                                        
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="email">{{ __('E-mail') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="email" name="email" id="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email', $row->email) }}" placeholder="{{ __('E-mail') }}"
                                                required>
                                            @error('email')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="address">{{ __('Address Line 1') }}</label>
                                            <input type="text" name="address" placeholder="{{ __('Address') }}"
                                                class="form-control" value="{{ old('address', $row->address) }}">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="address2">{{ __('Address Line 2') }}</label>
                                            <input type="text" name="address2" placeholder="{{ __('Address 2') }}"
                                                class="form-control" value="{{ old('address2', $row->address2) }}">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="">{{ __('Country') }} <span
                                                    class="text-danger">*</span></label>
                                            <select name="country"
                                                class="form-control @error('country') is-invalid @enderror"
                                                id="country-sms-testing" required>
                                                <option value="">{{ __('-- Select --') }}</option>
                                                @foreach (get_country_lists() as $id => $name)
                                                    <option @if ($row->country == $id) selected @endif
                                                        value="{{ $id }}">
                                                        {{ $name }}</option>
                                                @endforeach
                                            </select>
                                            @error('country')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('State') }} <span class="text-danger">*</span></label>
                                            <select name="state" class="form-control @error('state') is-invalid @enderror"
                                                id="state" required>
                                                <option value="">{{ __('-- Select --') }}</option>
                                                <!-- Options will be populated from JavaScript -->
                                            </select>
                                            @error('state')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('City') }} <span class="text-danger">*</span></label>
                                            <select name="city"
                                                class="form-control @error('city') is-invalid @enderror" id="city"
                                                required>
                                                <option value="">{{ __('-- Select --') }}</option>
                                            </select>
                                            @error('city')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('Zip Code') }}</label>
                                            <input type="text" value="{{ old('zip_code', $row->zip_code) }}"
                                                name="zip_code" placeholder="{{ __('Zip Code') }}" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{ __('Select the service you are authorized to sell') }}</label>
                                            <div class="row">

                                                @php
                                                    $activeServices = array_keys(get_bookable_services());

                                                    // $selectedServices = old('services', $requestServices);

                                                @endphp

                                                @foreach ($activeServices as $key => $name)
                                                    @php
                                                        $index = array_search($name, $all_services);
                                                        // $serviceId = array_search($name, $all_services);
                                                    @endphp
                                                    <div class="col-2">
                                                        <input type="checkbox" name="services[]"
                                                            id="service_{{ $name }}" value="{{ $index }}"
                                                            @if (in_array($index, $request_services)) checked @endif>
                                                        <label
                                                            for="service_{{ $name }}">{{ __(ucfirst($name)) }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @error('services')
                                                <div class="invalid-feedback" style="display: block">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    {{-- <div class="col-12">
                                            <div class="form-group">
                                                <label for="term">
                                                    <input id="term" type="checkbox" name="term"
                                                        class="mr-2 @error('term') is-invalid @enderror">
                                                    {!! __("I have read and accept the <a href=':link' target='_blank'>Terms and Privacy Policy</a>", [
                                                        'link' => get_page_url(setting_item('vendor_term_conditions')),
                                                    ]) !!}
                                                </label>
                                                @error('term')
                                                    <div class="invalid-feedback" style="display: block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div> --}}

                                    {{-- <div class="col-12 col-sm-4 col-md-3">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary form-submit w-100">
                                                    {{ __('Approve') }}
                                                    <span class="spinner-grow spinner-grow-sm icon-loading" role="status" aria-hidden="true"
                                                        style="display: none"></span>
                                                </button>
                                            </div>
                                        </div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="panel">
                        <div class="panel-title"><strong>{{ __('Publish') }}</strong></div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label>{{ __('Status') }}</label>
                                <select required class="custom-select" name="status">
                                    <option @if (old('status', $row->status) == 'approved') selected @endif value="approved">
                                        {{ __('Publish') }}</option>
                                    <option @if (old('status', $row->status) == 'draft') selected @endif value="draft">
                                        {{ __('Draft') }}</option>
                                    <option value="delete">{{ __('Delete') }}</option>
                                </select>
                            </div>
                            @if (is_admin())
                                {{-- @if (empty($user_type) or $user_type != 'vendor')
                                    <div class="form-group">
                                        <label>{{__('Role')}} <span class="text-danger">*</span></label>
                                        <select required class="form-control" name="role_id">
                                            <option value="">{{ __('-- Select --')}}</option>
                                            @foreach ($roles as $role)
                                                <option value="{{$role->id}}" @if (old('role_id', $row->user->role_id) == $role->id) selected @elseif(old('role_id')  == $role->id ) selected @elseif(request()->input("user_type")  == strtolower($role->name) ) selected @endif >{{ucfirst($role->name)}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif --}}
                                <div class="form-group">
                                    <label>{{ __('Email Verified?') }}</label>
                                    <select class="form-control" name="is_email_verified">
                                        <option value="">{{ __('No') }}</option>
                                        <option @if (old('is_email_verified', $row->email_verified_at ? 1 : 0)) selected @endif value="1">
                                            {{ __('Yes') }}</option>
                                    </select>
                                </div>
                                <div class="form-group" ">
                                                <label>{{ __('Display Profile') }}</label>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="is_visible" name="is_visible" value="1"
                                                    @if (old('is_visible', $row->is_visible)) checked @endif>
                                                    <label class="custom-control-label" for="is_visible">{{ __('Show Vendor Profile?') }}</label>
                                                </div>
                                            </div>
     @endif
                                </div>
                        </div>

                        <div class="panel">
                            <div class="panel-title"><strong>{{ __('Vendor') }}</strong></div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label>{{ __('Vendor Commission Type') }}</label>
                                    <div class="form-controls">
                                        <select name="vendor_commission_type" id="vendor_commission_type"
                                            class="form-control">
                                            <option value="default"
                                                {{ old('vendor_commission_type', $row->user->vendor_commission_type ?? '') == 'default' ? 'selected' : '' }}>
                                                {{ __('Default') }}</option>
                                            <option value="percent"
                                                {{ old('vendor_commission_type', $row->user->vendor_commission_type ?? '') == 'percent' ? 'selected' : '' }}>
                                                {{ __('Percent') }}</option>
                                            <option value="amount"
                                                {{ old('vendor_commission_type', $row->user->vendor_commission_type ?? '') == 'amount' ? 'selected' : '' }}>
                                                {{ __('Amount') }}</option>
                                            <option value="disable"
                                                {{ old('vendor_commission_type', $row->user->vendor_commission_type ?? '') == 'disable' ? 'selected' : '' }}>
                                                {{ __('Disable Commission') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('Vendor commission value') }}</label>
                                    <div class="form-controls">
                                        <input type="text" class="form-control" name="vendor_commission_amount"
                                            value="{{ old('vendor_commission_amount', $row->user->vendor_commission_amount ?? '') }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('Vendor Commission Calculate Way') }}</label>
                                    <div class="form-controls">
                                        <select name="vendor_commission_calculate_way" class="form-control">
                                            <option value="default"
                                                {{ old('vendor_commission_calculate_way', $row->user->vendor_commission_calculate_way ?? '') == 'default' ? 'selected' : '' }}>
                                                {{ __('Default') }}
                                            </option>
                                            <option value="addition"
                                                {{ old('vendor_commission_calculate_way', $row->user->vendor_commission_calculate_way ?? '') == 'addition' ? 'selected' : '' }}>
                                                {{ __('Addition') }}
                                            </option>
                                            <option value="dedict"
                                                {{ old('vendor_commission_calculate_way', $row->user->vendor_commission_calculate_way ?? '') == 'dedict' ? 'selected' : '' }}>
                                                {{ __('Dedict') }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('Vendor Commission Calculate Time') }}</label>
                                    <div class="form-controls">
                                        <select name="vendor_commission_calculate_time" class="form-control">
                                            <option value="default"
                                                {{ old('vendor_commission_calculate_time', $row->user->vendor_commission_calculate_time ?? '') == 'Default' ? 'selected' : '' }}>
                                                {{ __('Default') }}
                                            </option>
                                            <option value="one-time"
                                                {{ old('vendor_commission_calculate_time', $row->user->vendor_commission_calculate_time ?? '') == 'one-time' ? 'selected' : '' }}>
                                                {{ __('One-Time') }}
                                            </option>
                                            <option value="per-day"
                                                {{ old('vendor_commission_calculate_time', $row->user->vendor_commission_calculate_time ?? '') == 'per-day' ? 'selected' : '' }}>
                                                {{ __('Per-Day') }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group" data-condition="vendor_commission_type:not(percent)">
                                    <label>{{ __('Per Person') }}</label>
                                    <div class="form-controls">
                                        <select name="per_person" class="form-control">
                                            <option value="default"
                                                {{ old('per_person', $row->user->per_person ?? '') == 'default' ? 'selected' : '' }}>
                                                {{ __('Default') }}
                                            </option>
                                            <option value="1"
                                                {{ old('per_person', $row->user->per_person ?? '') == '1' ? 'selected' : '' }}>
                                                {{ __('Select') }}
                                            </option>
                                            <option value="0"
                                                {{ old('per_person', $row->user->per_person ?? '') == '0' ? 'selected' : '' }}>
                                                {{ __('Deselect') }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel">
                            <div class="panel-title"><strong>{{ __('Avatar') }}</strong></div>
                            <div class="panel-body">
                                <div class="form-group">
                                    {!! \Modules\Media\Helpers\FileHelper::fieldUpload('avatar_id', old('avatar_id', $row->avatar_id)) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="text-right">
                    <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i>
                        {{ __('Save Changes') }}</button>
                </div>
        </form>
    </div>
@endsection


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"></script>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        var input = document.querySelector("#phone");
        var phoneCodeInput = document.querySelector("#country_code");
        var storedData = null;

        try {
            if (phoneCodeInput && phoneCodeInput.value.trim() !== "") {
                storedData = JSON.parse(phoneCodeInput.value);
            }
        } catch (e) {
            console.error("Error parsing JSON from country_code:", e, phoneCodeInput.value);
            storedData = null;
        }

        var iti = window.intlTelInput(input, {
            initialCountry: storedData?.countryCode || "auto",
            separateDialCode: true,
            geoIpLookup: function(callback) {
                fetch('https://ipinfo.io?token=2dff839b1cadf7', {
                        cache: 'no-cache'
                    })
                    .then(response => response.json())
                    .then(data => callback(data.country))
                    .catch(() => callback('us'));
            },
            dropdownContainer: document.body,
            preferredCountries: ['us', 'gb', 'sy'],
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
        });

        input.addEventListener('countrychange', function() {
            updateCountryCodeInput();
        });

        // input.addEventListener('blur', function() {
        //     if (iti.isValidNumber()) {
        //         updateCountryCodeInput();
        //     } else {
        //         alert("Please enter a valid phone number.");
        //     }
        // });

        function updateCountryCodeInput() {
            var phoneCode = "+" + iti.getSelectedCountryData().dialCode;
            var countryCode = iti.getSelectedCountryData().iso2;
            var jsonData = {
                phoneCode: phoneCode,
                countryCode: countryCode
            };

            if (phoneCodeInput) {
                phoneCodeInput.value = JSON.stringify(jsonData);
                console.log("Updated country_code value:", phoneCodeInput.value);
            }
        }
    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const country = document.getElementById('country-sms-testing');
        const statesSelect = document.getElementById('state');
        const city = document.getElementById('city');

        var user_info = @json($row->user);

        statesSelect.disabled = true;
        city.disabled = true;

        function setDefaultOptionToSelectInput(selectElement, text, isDisabled = false) {
            selectElement.innerHTML = '';
            const defaultOption = document.createElement('option');
            defaultOption.textContent = `-- ${text} --`;
            defaultOption.value = '';
            selectElement.appendChild(defaultOption);
            selectElement.disabled = isDisabled;
        }

        function updateStates(countryValue) {
            setDefaultOptionToSelectInput(statesSelect, 'Select', true);
            setDefaultOptionToSelectInput(city, 'Select', true);

            fetch('/states/' + countryValue)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok: ' + response
                        .statusText);
                    return response.json();
                })
                .then(statesData => {
                    if (statesData.length > 0) {
                        statesSelect.disabled = false;
                        setDefaultOptionToSelectInput(statesSelect, 'Select');
                        statesSelect.setAttribute('required', 'required');

                        statesData.forEach(state => {
                            const option = document.createElement('option');
                            option.value = state.name; // تخزين اسم الولاية في value
                            option.dataset.stateCode = state
                                .state_code; // حفظ state_code في dataset
                            option.textContent = state.name;
                            if (state.name === user_info.state) { // مقارنة بناءً على اسم الولاية
                                option.selected = true;
                            }
                            statesSelect.appendChild(option);
                        });

                        if (statesSelect.value) {
                            const selectedOption = statesSelect.options[statesSelect.selectedIndex];
                            updateCities(countryValue, selectedOption.dataset.stateCode);
                        }
                    } else {
                        setDefaultOptionToSelectInput(statesSelect, 'No States');
                        statesSelect.removeAttribute('required');
                        city.disabled = true;
                    }
                })
                .catch(error => {
                    console.error('Error fetching states: ', error);
                });
        }

        function updateCities(countryCode, stateCode) {
            setDefaultOptionToSelectInput(city, 'Select', true);

            if (!stateCode) return;

            fetch(`/cities/${countryCode}/${stateCode}`) // استخدام state_code للبحث عن المدن
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok: ' + response
                        .statusText);
                    return response.json();
                })
                .then(citiesData => {
                    if (citiesData.length > 0) {
                        city.disabled = false;
                        setDefaultOptionToSelectInput(city, 'Select');
                        city.setAttribute('required', 'required');

                        citiesData.forEach(cityItem => {
                            const option = document.createElement('option');
                            option.value = cityItem.name;
                            option.textContent = cityItem.name;
                            if (cityItem.name === user_info.city) {
                                option.selected = true;
                            }
                            city.appendChild(option);
                        });
                    } else {
                        setDefaultOptionToSelectInput(city, 'No Cities');
                        city.removeAttribute('required');
                        city.disabled = true;
                    }
                })
                .catch(error => {
                    console.error('Error fetching cities: ', error);
                });
        }

        country.addEventListener('change', function() {
            updateStates(this.value);
        });

        statesSelect.addEventListener('change', function() {
            const selectedOption = statesSelect.options[statesSelect.selectedIndex];
            const stateCode = selectedOption.dataset.stateCode; // استخراج state_code
            updateCities(country.value, stateCode); // تمرير state_code للبحث عن المدن
        });

        if (country.value) {
            updateStates(country.value);
        }
    });
</script>


<script>
    //This script returns the phone field to 100% width after the country code library reduced the width
    document.addEventListener("DOMContentLoaded", function() {
        const phoneFieldWrapper = document.querySelector('.iti.iti--allow-dropdown.iti--separate-dial-code');

        if (phoneFieldWrapper) {
            phoneFieldWrapper.style.width = "100%";
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const country = document.getElementById('country-sms-testing');
        const statesSelect = document.getElementById('state');
        const city = document.getElementById('city');

        var user_info = @json($row);

        statesSelect.disabled = true;
        city.disabled = true;

        function setDefaultOptionToSelectInput(selectElement, text, isDisabled = false) {
            selectElement.innerHTML = '';
            const defaultOption = document.createElement('option');
            defaultOption.textContent = `-- ${text} --`;
            defaultOption.value = '';
            selectElement.appendChild(defaultOption);
            selectElement.disabled = isDisabled;
        }

        function updateStates(countryValue) {
            setDefaultOptionToSelectInput(statesSelect, 'Select', true);
            setDefaultOptionToSelectInput(city, 'Select', true);

            fetch('/states/' + countryValue)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok: ' + response
                        .statusText);
                    return response.json();
                })
                .then(statesData => {
                    if (statesData.length > 0) {
                        statesSelect.disabled = false;
                        setDefaultOptionToSelectInput(statesSelect, 'Select');
                        statesSelect.setAttribute('required', 'required');

                        statesData.forEach(state => {
                            const option = document.createElement('option');
                            option.value = state.name; // تخزين اسم الولاية في value
                            option.dataset.stateCode = state
                                .state_code; // حفظ state_code في dataset
                            option.textContent = state.name;
                            if (state.name === user_info.state) { // مقارنة بناءً على اسم الولاية
                                option.selected = true;
                            }
                            statesSelect.appendChild(option);
                        });

                        if (statesSelect.value) {
                            const selectedOption = statesSelect.options[statesSelect.selectedIndex];
                            updateCities(countryValue, selectedOption.dataset.stateCode);
                        }
                    } else {
                        setDefaultOptionToSelectInput(statesSelect, 'No States');
                        statesSelect.removeAttribute('required');
                        city.disabled = true;
                    }
                })
                .catch(error => {
                    console.error('Error fetching states: ', error);
                });
        }

        function updateCities(countryCode, stateCode) {
            setDefaultOptionToSelectInput(city, 'Select', true);

            if (!stateCode) return;

            fetch(`/cities/${countryCode}/${stateCode}`) // استخدام state_code للبحث عن المدن
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok: ' + response
                        .statusText);
                    return response.json();
                })
                .then(citiesData => {
                    if (citiesData.length > 0) {
                        city.disabled = false;
                        setDefaultOptionToSelectInput(city, 'Select');
                        city.setAttribute('required', 'required');

                        citiesData.forEach(cityItem => {
                            const option = document.createElement('option');
                            option.value = cityItem.name;
                            option.textContent = cityItem.name;
                            if (cityItem.name === user_info.city) {
                                option.selected = true;
                            }
                            city.appendChild(option);
                        });
                    } else {
                        setDefaultOptionToSelectInput(city, 'No Cities');
                        city.removeAttribute('required');
                        city.disabled = true;
                    }
                })
                .catch(error => {
                    console.error('Error fetching cities: ', error);
                });
        }

        country.addEventListener('change', function() {
            updateStates(this.value);
        });

        statesSelect.addEventListener('change', function() {
            const selectedOption = statesSelect.options[statesSelect.selectedIndex];
            const stateCode = selectedOption.dataset.stateCode; // استخراج state_code
            updateCities(country.value, stateCode); // تمرير state_code للبحث عن المدن
        });

        if (country.value) {
            updateStates(country.value);
        }
    });
</script>
