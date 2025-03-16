@extends('layouts.user')
@section('content')
    <h2 class="title-bar">
        {{ __('Settings') }}
        <a href="{{ route('user.change_password') }}" class="btn-change-password">{{ __('Change Password') }}</a>
    </h2>
    @if (!$errors->any())
        <!-- here, we dont want show error messages because it shown under each field -->
        @include('admin.message')
    @endif
    <form action="{{ route('user.profile.update') }}" method="post" class="input-has-icon needs-validation" novalidate>
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="form-title">
                    <strong>{{ __('Personal Information') }}</strong>
                </div>
                {{-- @if ($is_vendor_access)
                    <div class="form-group">
                        <label>{{__("Business name")}}</label>
                        <input type="text" value="{{old('business_name',$dataUser->business_name)}}" name="business_name" placeholder="{{__("Business name")}}" class="form-control">
                        <i class="fa fa-user input-icon"></i>
                    </div>
                @endif --}}

                <div class="form-group">
                    <label>{{ __('User name') }} <span class="text-danger">*</span></label>
                    <input type="text" name="user_name" value="{{ old('user_name', $dataUser->user_name) }}"
                        placeholder="{{ __('User name') }}" class="form-control @error('user_name') is-invalid @enderror">
                    <i class="fa fa-user input-icon"></i>
                    @error('user_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group">
                    <label>{{ __('E-mail') }} <span class="text-danger">*</span></label>
                    <input type="text" required name="email" value="{{ old('email', $dataUser->email) }}"
                        placeholder="{{ __('E-mail') }}"
                        class="form-control @if ($errors->has('email') || Session::get('email')) is-invalid @endif">
                    <i class="fa fa-envelope input-icon"></i>
                    @if ($errors->has('email') || Session::get('email'))
                        <div class="invalid-feedback">
                            {{ $message ?? Session::get('email') }}
                        </div>
                    @endif
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __('First name') }} <span class="text-danger">*</span></label>
                            <input type="text" required value="{{ old('first_name', $dataUser->first_name) }}"
                                name="first_name" placeholder="{{ __('First name') }}"
                                class="form-control @error('first_name') is-invalid @enderror">
                            <i class="fa fa-user input-icon"></i>
                            @error('first_name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __('Last name') }} <span class="text-danger">*</span></label>
                            <input type="text" required value="{{ old('last_name', $dataUser->last_name) }}"
                                name="last_name" placeholder="{{ __('Last name') }}"
                                class="form-control @error('last_name') is-invalid @enderror">
                            <i class="fa fa-user input-icon"></i>
                            @error('last_name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="phone">{{ __('Phone Number') }} <span class="text-danger">*</span></label>
                    <input type="tel" id="phone" name="phone"
                        class="form-control @if (Session::get('phone')) is-invalid @endif"
                        value="{{ old('phone', $dataUser->phone) }}" placeholder="{{ __('Phone') }}" required>
                    @if ($errors->has('phone') || Session::get('phone'))
                        <div class="invalid-feedback">
                            {{ $message ?? Session::get('phone') }}
                        </div>
                    @endif
                </div>
                {{--
                <div class="form-group">
                    <label for="phone">{{ __('Phone Number') }} <span class="text-danger">*</span></label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $dataUser->phone) }}"
                        placeholder="{{ __('Phone') }}" required
                        class="form-control @error('phone') is-invalid @enderror ">

                    @error('phone')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                --}}

                <input type="hidden" id="country_code" name="country_code"
                    value="{{ old('country_code', $dataUser->country_code ?? '') }}">
                <div class="form-group">
                    <label>{{ __('Birthday') }}</label>
                    <input type="text"
                        value="{{ old('birthday', $dataUser->birthday ? display_date($dataUser->birthday) : '') }}"
                        name="birthday" placeholder="{{ __('Birthday') }}" class="form-control date-picker">
                    <i class="fa fa-birthday-cake input-icon"></i>
                </div>
                <div class="form-group">
                    <label>{{ __('About Yourself') }}</label>
                    <textarea name="bio" rows="5" class="form-control">{{ old('bio', $dataUser->bio) }}</textarea>
                </div>
                <div class="form-group">
                    <label>{{ __('Avatar') }}</label>
                    <div class="upload-btn-wrapper">
                        <div class="input-group">
                            <span class="input-group-btn">
                                <span class="btn btn-default btn-file">
                                    {{ __('Browse') }}… <input type="file">
                                </span>
                            </span>
                            <input type="text" data-error="{{ __('Error upload...') }}"
                                data-loading="{{ __('Loading...') }}" class="form-control text-view" readonly
                                value="{{ basename(get_file_url(old('avatar_id', $dataUser->avatar_id))) ?? ($dataUser->getAvatarUrl() ?? __('No Image')) }}">
                        </div>
                        <input type="hidden" class="form-control" name="avatar_id"
                            value="{{ old('avatar_id', $dataUser->avatar_id) ?? '' }}">
                        <img class="image-demo"
                            src="{{ get_file_url(old('avatar_id', $dataUser->avatar_id)) ?? ($dataUser->getAvatarUrl() ?? '') }}" />
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-title">
                    <strong>{{ __('Location Information') }}</strong>
                </div>
                <div class="form-group">
                    <label>{{ __('Address Line 1') }}</label>
                    <input type="text" value="{{ old('address', $dataUser->address) }}" name="address"
                        placeholder="{{ __('Address') }}" class="form-control">
                    <i class="fa fa-location-arrow input-icon"></i>
                </div>
                <div class="form-group">
                    <label>{{ __('Address Line 2') }}</label>
                    <input type="text" value="{{ old('address2', $dataUser->address2) }}" name="address2"
                        placeholder="{{ __('Address2') }}" class="form-control">
                    <i class="fa fa-location-arrow input-icon"></i>
                </div>
                <div class="form-group">
                    <label class="">{{ __('Country') }}</label>
                    <select required name="country"
                        class="form-control @if ($errors->has('country') || Session::get('country')) is-invalid @endif" id="country-sms-testing">
                        <option value="">{{ __('-- Select --') }}</option>
                        @foreach (get_country_lists() as $id => $name)
                            <option
                                @if (old('country') === $id) selected
                                @elseif(!old('country') && $dataUser->country === $id)
                                    selected @endif
                                value="{{ $id }}"> {{ $name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('country') || Session::get('country'))
                        <div class="invalid-feedback">
                            {{ $message ?? Session::get('country') }}
                        </div>
                    @endif
                </div>
                <div class="form-group">

                    <label>{{ __('State') }}</label>
                    <select name="state" class="form-control @if ($errors->has('state') || Session::get('state')) is-invalid @endif"
                        id="state">
                        <option value="">{{ __('-- Select --') }}</option>
                        <!-- Options will be populated from JavaScript -->
                    </select>
                    @if ($errors->has('state') || Session::get('state'))
                        <div class="invalid-feedback">
                            {{ $message ?? Session::get('state') }}
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <label>{{ __('City') }}</label>
                    <select name="city" class="form-control @if ($errors->has('city') || Session::get('city')) is-invalid @endif"
                        id="city">
                        <option value="">{{ __('-- Select --') }}</option>
                    </select>
                    @if ($errors->has('city') || Session::get('city'))
                        <div class="invalid-feedback">
                            {{ $message ?? Session::get('city') }}
                        </div>
                    @endif
                </div>

                <div class="form-group">
                    <label>{{ __('Zip Code') }}</label>
                    <input type="text" value="{{ old('zip_code', $dataUser->zip_code) }}" name="zip_code"
                        placeholder="{{ __('Zip Code') }}" class="form-control">
                    <i class="fa fa-map-pin input-icon"></i>
                </div>

            </div>
            <div class="col-md-12">
                <hr>
                <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i>
                    {{ __('Save Changes') }}</button>
            </div>
        </div>
    </form>
    {{-- @if (!empty(setting_item('user_enable_permanently_delete')) and !is_admin())
    <hr>
    <div class="row">
        <div class="col-md-12">
            <h4 class="text-danger">
                {{__("Delete account")}}
            </h4>
            <div class="mb-4 mt-2">
                {!! clean(setting_item_with_lang('user_permanently_delete_content','',__('Your account will be permanently deleted. Once you delete your account, there is no going back. Please be certain.'))) !!}
            </div>
            <a data-toggle="modal" data-target="#permanentlyDeleteAccount" class="btn btn-danger" href="">{{__('Delete your account')}}</a>
        </div>

        <!-- Modal -->
        <div class="modal  fade" id="permanentlyDeleteAccount" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content ">
                    <div class="modal-header">
                        <h5 class="modal-title">{{__('Confirm permanently delete account')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="my-3">
                            {!! clean(setting_item_with_lang('user_permanently_delete_content_confirm')) !!}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
                        <a href="{{route('user.permanently.delete')}}" class="btn btn-danger">{{__('Confirm')}}</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
    @endif --}}
@endsection



<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"></script>

<style>
    #phone {
        width: 100%;
        padding-right: 150px;
        padding-left: 60px;
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const country = document.getElementById('country-sms-testing');
        const statesSelect = document.getElementById('state');
        const city = document.getElementById('city');

        var user_info = @json($dataUser);

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
                            if (state.name === "{{ old('state') }}") {
                                option.selected = true;
                            } else if ("{{ !old('state') }}" && state.name === user_info
                                .state) { // مقارنة بناءً على اسم الولاية
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

                            if (cityItem.name === "{{ old('city') }}") {
                                option.selected = true;
                            } else if ("{{ !old('city') }}" && cityItem.name === user_info
                                .city) { // مقارنة بناءً على اسم الولاية
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
