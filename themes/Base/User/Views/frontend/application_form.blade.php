@extends('layouts.app')

@section('content')
<div class="container py-5 px-4">
    <div class="bravo-vendor-form-register">
    <h1>{{ __('Become a vendor') }}</h1>
    <p>{{ __('Join our community to unlock your greatest asset and welccome paying guests into your home') }}</p>
        <form id="form" action="{{ route('user.application.submit') }}" method="POST" class="needs-validation" novalidate>
            @csrf
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="business_name">{{ __('Business name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="business_name" id="business_name" class="form-control @error('business_name') is-invalid @enderror" value="{{old('business_name',$user->business_name)}}" placeholder="{{ __('Business name') }}" required>
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
                        <input type="text" name="business_licience_id" id="business_licience_id" class="form-control" value="{{old('business_name_id',$user->business_name_id)}}" placeholder="{{ __('Business licience id') }}">
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="phone" style="display: block">{{ __('Business Phone Number') }} <span class="text-danger">*</span></label>
                        <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{old('phone')}}" placeholder="{{ __('Phone number') }}" required
                        style="text-align: left; direction: ltr">
                        @error('phone')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <input type="hidden" id="country_code" name="country_code" value="{{ old('country_code', $user->country_code ?? '') }}">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="email">{{ __('E-mail') }} <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{old('email')}}" placeholder="{{ __('E-mail') }}" required>
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
                        <input type="text" name="address" placeholder="{{__("Address")}}" class="form-control" value="{{old('address',$user->address)}}">
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="address2">{{ __('Address Line 2') }}</label>
                        <input type="text" name="address2" placeholder="{{__("Address 2")}}" class="form-control" value="{{old('address2',$user->address2)}}">
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="">{{ __('Country') }} <span class="text-danger">*</span></label>
                        <select  name="country" class="form-control @error('country') is-invalid @enderror" id="country-sms-testing" required>
                            <option value="">{{ __('-- Select --') }}</option>
                            @foreach (get_country_lists() as $id => $name)
                                <option
                                 @if ($user->country == $id) selected @endif
                                    value="{{ $id }}">{{ $name }}</option>
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
                        <select name="state" class="form-control @error('state') is-invalid @enderror" id="state" required>
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
                        <select name="city" class="form-control @error('city') is-invalid @enderror" id="city" required>
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
                        <label>{{__("Zip Code")}}</label>
                        <input type="text" value="{{old('zip_code',$user->zip_code)}}" name="zip_code" placeholder="{{__("Zip Code")}}" class="form-control">
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label>{{ __('Select the service you are authorized to sell') }}</label>
                        <div class="row">
                            @php
                                $activeServices = array_keys(get_bookable_services());
                            @endphp

                            @foreach ($activeServices as $name)
                                @php
                                    $index = array_search($name, $services);
                                    $selectedServices = old('services', $services ?? []);
                                @endphp
                                <div class="col-2">
                                    <input type="checkbox" name="services[]"
                                    id="service_{{ $name }}" value="{{ $index }}" @if(in_array($index, $selectedServices)) checked @endif>
                                    <label for="service_{{ $name }}">{{ __(ucfirst($name)) }}</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="text-danger" id="services-error" style="display: none;">{{ __('You must select at least one service.') }}</div>
                        @error('services')
                            <div class="invalid-feedback" style="display: block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label for="term">
                            <input id="term" type="checkbox" name="term" class="mr-2 @error('term') is-invalid @enderror" @if(old('term')) checked @endif>
                            {!! __("I have read and accept the <a href=':link' target='_blank'>Terms and Privacy Policy</a>",['link'=>get_page_url(setting_item('vendor_term_conditions'))]) !!}
                        </label>
                        <div class="text-danger" id="term-error" style="display: none;">{{ __('You must accept the terms and conditions.') }}</div>
                        @error('term')
                            <div class="invalid-feedback" style="display: block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="col-12 col-sm-4 col-md-3">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary form-submit w-100">
                            {{ __('Sign Up') }}
                            <span class="spinner-grow spinner-grow-sm icon-loading" role="status" aria-hidden="true" style="display: none"></span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"></script>


<script>
    document.addEventListener("DOMContentLoaded", function () {
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
            geoIpLookup: function (callback) {
                fetch('https://ipinfo.io?token=2dff839b1cadf7', { cache: 'no-cache' })
                    .then(response => response.json())
                    .then(data => callback(data.country))
                    .catch(() => callback('us'));
            },
            dropdownContainer: document.body,
            preferredCountries: ['us', 'gb', 'sy'],
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
        });

        input.addEventListener('countrychange', function () {
            updateCountryCodeInput();
        });

        input.addEventListener('blur', function () {
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
        var oldState = "{{ old('state') }}" || null;
        var oldCity = "{{ old('city') }}" || null;

        var user_info = @json($user);

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
                    if (!response.ok) throw new Error('Network response was not ok: ' + response.statusText);
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
                            option.dataset.stateCode = state.state_code; // حفظ state_code في dataset
                            option.textContent = state.name;
                            if (state.name === user_info.state) { // مقارنة بناءً على اسم الولاية
                                option.selected = true;
                            }
                            if(state.name == oldState) {
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
                    if (!response.ok) throw new Error('Network response was not ok: ' + response.statusText);
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
                            if(cityItem.name == oldCity) {
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


<script>//This script returns the phone field to 100% width after the country code library reduced the width
document.addEventListener("DOMContentLoaded", function () {
    const phoneFieldWrapper = document.querySelector('.iti.iti--allow-dropdown.iti--separate-dial-code');

    if (phoneFieldWrapper) {
        phoneFieldWrapper.style.width = "100%";
    }
});

</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('form');
        const termCheckbox = document.getElementById('term');
        const termError = document.getElementById('term-error');
        const serviceCheckboxes = document.querySelectorAll('input[name="services[]"]');
        const servicesError = document.getElementById('services-error');

        form.addEventListener('submit', function (e) {
            let hasServiceChecked = false;

            // تحقق من تحديد الشروط
            if (!termCheckbox.checked) {
                e.preventDefault();
                termError.style.display = 'block';
            } else {
                termError.style.display = 'none';
            }

            // تحقق من اختيار خدمة واحدة على الأقل
            serviceCheckboxes.forEach(function (checkbox) {
                if (checkbox.checked) {
                    hasServiceChecked = true;
                }
            });

            if (!hasServiceChecked) {
                e.preventDefault();
                servicesError.style.display = 'block';
            } else {
                servicesError.style.display = 'none';
            }
        });
    });
</script>
