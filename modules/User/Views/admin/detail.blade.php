@extends('admin.layouts.app')

@section('content')
    <form action="{{ route('user.admin.store', ['id' => $row->id ?? -1]) }}" method="post" class="needs-validation"
        novalidate>
        @csrf
        <div class="container">
            <div class="d-flex justify-content-between mb20">
                <div class="">
                    <h1 class="title-bar">{{ $row->id ? 'Edit: ' . $row->getfirstLastName() : 'Add new user' }}</h1>
                </div>
            </div>


            @include('admin.message')
            <div class="row">
                <div class="col-md-9">
                    <div class="panel">
                        <div class="panel-title"><strong>{{ __('User Info') }}</strong></div>
                        <div class="panel-body">
                            <div class="row">



                                {{-- <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Business name') }}</label>
                                        <input type="text" value="{{ old('business_name', $row->business_name) }}"
                                            required name="business_name" placeholder="{{ __('Business name') }}"
                                            class="form-control">
                                    </div>
                                </div> --}}

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('E-mail') }}</label>
                                        <input type="email" required value="{{ old('email', $row->email) }}"
                                            placeholder="{{ __('Email') }}" name="email" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('User name') }}</label>
                                        <input type="text" name="user_name"
                                            value="{{ old('user_name', $row->user_name) }}"
                                            placeholder="{{ __('User name') }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('First name') }}</label>
                                        <input type="text" required value="{{ old('first_name', $row->first_name) }}"
                                            name="first_name" placeholder="{{ __('First name') }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Last name') }}</label>
                                        <input type="text" required value="{{ old('last_name', $row->last_name) }}"
                                            name="last_name" placeholder="{{ __('Last name') }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Phone Number') }}</label>
                                        <input type="tel" id="phone" name="phone" class="form-control"
                                            value="{{ old('phone', $row->phone) }}" placeholder="{{ __('Phone') }}"
                                            required>
                                    </div>
                                </div>
                                <input type="hidden" id="country_code" name="country_code"
                                    value="{{ old('country_code', $row->country_code ?? '') }}">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Birthday') }}</label>
                                        <input type="text"
                                            value="{{ old('birthday', $row->birthday ? date('Y/m/d', strtotime($row->birthday)) : '') }}"
                                            placeholder="{{ __('Birthday') }}" name="birthday"
                                            class="form-control has-datepicker input-group date">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Address Line 1') }}</label>
                                        <input type="text" value="{{ old('address', $row->address) }}"
                                            placeholder="{{ __('Address') }}" name="address" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Address Line 2') }}</label>
                                        <input type="text" value="{{ old('address2', $row->address2) }}"
                                            placeholder="{{ __('Address 2') }}" name="address2" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="">{{ __('Country') }}</label>
                                        <select name="country" class="form-control" id="country-sms-testing" required>
                                            <option value="">{{ __('-- Select --') }}</option>
                                            @foreach (get_country_lists() as $id => $name)
                                                <option @if ($row->country == $id) selected @endif
                                                    value="{{ $id }}">{{ $name }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('State') }}</label>
                                        <select name="state" class="form-control" id="state">
                                            <option value="">{{ __('-- Select --') }}</option>
                                            <!-- Options will be populated from JavaScript -->
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('City') }}</label>
                                        <select name="city" class="form-control" id="city">
                                            <option value="">{{ __('-- Select --') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Zip Code') }}</label>
                                        <input type="text" value="{{ old('zip_code', $row->zip_code) }}" name="zip_code"
                                            placeholder="{{ __('Zip Code') }}" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">{{ __('Biographical') }}</label>
                                <div class="">
                                    <textarea name="bio" class="d-none has-ckeditor" cols="30" rows="10">{{ old('bio', $row->bio) }}</textarea>
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
                                    <option @if (old('status', $row->status) == 'publish') selected @endif value="publish">
                                        {{ __('Publish') }}</option>
                                    <option @if (old('status', $row->status) == 'blocked') selected @endif value="blocked">
                                        {{ __('Blocked') }}</option>
                                </select>
                            </div>
                            @if (is_admin())
                                @if (empty($user_type) or $user_type != 'vendor')
                                    <div class="form-group">
                                        <label>{{ __('Role') }} <span class="text-danger">*</span></label>
                                        <select required class="form-control" name="role_id">
                                            <option value="">{{ __('-- Select --') }}</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}"
                                                    @if (old('role_id', $row->role_id) == $role->id) selected @elseif(old('role_id') == $role->id) selected @elseif(request()->input('user_type') == strtolower($role->name)) selected @endif>
                                                    {{ ucfirst($role->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label>{{ __('Email Verified?') }}</label>
                                    <select class="form-control" name="is_email_verified">
                                        <option value="">{{ __('No') }}</option>
                                        <option @if (old('is_email_verified', $row->email_verified_at ? 1 : 0)) selected @endif value="1">
                                            {{ __('Yes') }}</option>
                                    </select>
                                </div>
                                {{-- change --}}
                                {{-- <div class="form-group" data-condition="role_id:is(2)">
                                    <label>{{ __('Display Profile') }}</label>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="is_visible"
                                            name="is_visible" value="1"
                                            @if (old('is_visible', $row->is_visible)) checked @endif>
                                        <label class="custom-control-label"
                                            for="is_visible">{{ __('Show Vendor Profile?') }}</label>

                                    </div>
                                </div> --}}

                            @endif
                        </div>
                    </div>

                    {{-- asdf --}}

                    {{-- <div class="panel"data-condition="role_id:is(2)">
                        <div class="panel-title"><strong>{{ __('Vendor') }}</strong></div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label>{{ __('Vendor Commission Type') }}</label>
                                <div class="form-controls">
                                    <select name="vendor_commission_type" id="vendor_commission_type"
                                        class="form-control">
                                        <option value="default"
                                            {{ old('vendor_commission_type', $row->vendor_commission_type ?? '') == 'default' ? 'selected' : '' }}>
                                            {{ __('Default') }}</option>
                                        <option value="percent"
                                            {{ old('vendor_commission_type', $row->vendor_commission_type ?? '') == 'percent' ? 'selected' : '' }}>
                                            {{ __('Percent') }}</option>
                                        <option value="amount"
                                            {{ old('vendor_commission_type', $row->vendor_commission_type ?? '') == 'amount' ? 'selected' : '' }}>
                                            {{ __('Amount') }}</option>
                                        <option value="disable"
                                            {{ old('vendor_commission_type', $row->vendor_commission_type ?? '') == 'disable' ? 'selected' : '' }}>
                                            {{ __('Disable Commission') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>{{ __('Vendor commission value') }}</label>
                                <div class="form-controls">
                                    <input type="text" class="form-control" name="vendor_commission_amount"
                                        value="{{ old('vendor_commission_amount', $row->vendor_commission_amount ?? '') }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>{{ __('Vendor Commission Calculate Way') }}</label>
                                <div class="form-controls">
                                    <select name="vendor_commission_calculate_way" class="form-control">
                                        <option value="default"
                                            {{ old('vendor_commission_calculate_way', $row->vendor_commission_calculate_way ?? '') == 'default' ? 'selected' : '' }}>
                                            {{ __('Default') }}
                                        </option>
                                        <option value="addition"
                                            {{ old('vendor_commission_calculate_way', $row->vendor_commission_calculate_way ?? '') == 'addition' ? 'selected' : '' }}>
                                            {{ __('Addition') }}
                                        </option>
                                        <option value="dedict"
                                            {{ old('vendor_commission_calculate_way', $row->vendor_commission_calculate_way ?? '') == 'dedict' ? 'selected' : '' }}>
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
                                            {{ old('vendor_commission_calculate_time', $row->vendor_commission_calculate_time ?? '') == 'Default' ? 'selected' : '' }}>
                                            {{ __('Default') }}
                                        </option>
                                        <option value="one-time"
                                            {{ old('vendor_commission_calculate_time', $row->vendor_commission_calculate_time ?? '') == 'one-time' ? 'selected' : '' }}>
                                            {{ __('One-Time') }}
                                        </option>
                                        <option value="per-day"
                                            {{ old('vendor_commission_calculate_time', $row->vendor_commission_calculate_time ?? '') == 'per-day' ? 'selected' : '' }}>
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
                                            {{ old('per_person', $row->per_person ?? '') == 'default' ? 'selected' : '' }}>
                                            {{ __('Default') }}
                                        </option>
                                        <option value="1"
                                            {{ old('per_person', $row->per_person ?? '') == '1' ? 'selected' : '' }}>
                                            {{ __('Select') }}
                                        </option>
                                        <option value="0"
                                            {{ old('per_person', $row->per_person ?? '') == '0' ? 'selected' : '' }}>

                                            {{ __('Deselect') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div> --}}

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
            <div class="d-flex justify-content-between">
                <span></span>
                <button class="btn btn-primary" type="submit">{{ __('Save Change') }}</button>
            </div>
        </div>
    </form>
@endsection
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
            defaultOption.textContent = `{{ __('-- ${text} --') }}`;
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
                            option.value = state.state_code;
                            option.textContent = state.name;
                            if (state.state_code === user_info.state) {
                                option.selected = true;
                            }
                            statesSelect.appendChild(option);
                        });

                        if (statesSelect.value) {
                            updateCities(countryValue, statesSelect.value);
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

            fetch(`/cities/${countryCode}/${stateCode}`)
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
            updateCities(country.value, statesSelect.value);
        });

        if (country.value) {
            updateStates(country.value);
        }
    });
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"></script>

<style>
    #phone {
        width: 100%;
        padding-right: 90px;
    }

    .iti {
        width: 100%;
    }

    .iti__selected-flag {
        z-index: 1;
    }

    .iti__flag-container {
        margin-right: 5px;
    }
</style>
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
            storedData = null; // قيمة افتراضية عند الخطأ
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

        input.addEventListener('blur', function() {
            if (iti.isValidNumber()) {
                updateCountryCodeInput();
            } else {
                alert("Please enter a valid phone number.");
            }
        });

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
