
<div class="form-checkout" id="form-checkout" >
    <input type="hidden" name="code" value="{{$booking->code}}">
    <div class="form-section">
        <div class="row">

            @if(is_enable_guest_checkout() && is_enable_registration())
                <div class="col-12">
                    <div class="form-group">
                        <label for="confirmRegister">
                            <input type="checkbox" name="confirmRegister" id="confirmRegister" value="1">
                            {{__('Create a new account?')}}
                        </label>
                    </div>
                </div>
            @endif
            @if(is_enable_guest_checkout())
                <div class="col-12 d-none" id="confirmRegisterContent">
                    <div class="row">
                        <div class="col-md-6" >
                            <div class="form-group ">
                                <label class="lh-1 text-16 text-light-1" >{{__("Password")}} <span class="required">*</span></label>
                                <input type="password" class="form-control" name="password" autocomplete="off" >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group ">
                                <label class="lh-1 text-16 text-light-1" >{{__('Password confirmation')}} <span class="required">*</span></label>
                                <input type="password" class="form-control" name="password_confirmation" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <hr>
                </div>
            @endif
            <div class="col-md-6">
                <div class="form-group">
                    <label >{{__("First Name")}} <span class="required">*</span></label>
                    <input type="text" placeholder="{{__("First Name")}}" class="form-control" value="{{$user->first_name ?? ''}}" name="first_name">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label >{{__("Last Name")}} <span class="required">*</span></label>
                    <input type="text" placeholder="{{__("Last Name")}}" class="form-control" value="{{$user->last_name ?? ''}}" name="last_name">
                </div>
            </div>
            <div class="col-md-6 field-email">
                <div class="form-group">
                    <label >{{__("Email")}} <span class="required">*</span></label>
                    <input type="email" placeholder="{{__("email@domain.com")}}" class="form-control" value="{{$user->email ?? ''}}" name="email">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{ __('Phone Number')}}<span class="required">*</span></label>
                    <input type="tel" id="phone" name="phone" class="form-control" value="{{$user->phone ?? ''}}" placeholder="{{ __('Phone')}}" >
                </div>
            </div>
            <input type="hidden" id="country_code" name="country_code" value="{{ old('country_code', $user->country_code ?? '') }}">

            <div class="col-md-6 field-address-line-1">
                <div class="form-group">
                    <label >{{__("Address line 1")}} </label>
                    <input type="text" placeholder="{{__("Address line 1")}}" class="form-control" value="{{$user->address ?? ''}}" name="address_line_1">
                </div>
            </div>
            <div class="col-md-6 field-address-line-2">
                <div class="form-group">
                    <label >{{__("Address line 2")}} </label>
                    <input type="text" placeholder="{{__("Address line 2")}}" class="form-control" value="{{$user->address2 ?? ''}}" name="address_line_2">
                </div>
            </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="">{{ __('Country') }}</label>
                                        <select name="country" class="form-control" id="country-sms-testing" required>
                                            <option value="">{{ __('-- Select --') }}</option>
                                            @foreach (get_country_lists() as $id => $name)
                                                <option @if ($user->country == $id) selected @endif
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
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('City') }}</label>
                                        <select name="city" class="form-control" id="city" >
                                            <option value="">{{ __('-- Select --') }}</option>
                                        </select>
                                    </div>
                                </div>

            <div class="col-md-6 field-zip-code">
                <div class="form-group">
                    <label >{{__("ZIP code/Postal code")}} </label>
                    <input type="text" class="form-control" value="{{$user->zip_code ?? ''}}" name="zip_code" placeholder="{{__("ZIP code/Postal code")}}">
                </div>
            </div>

            <div class="col-md-12">
                <label >{{__("Special Requirements")}} </label>
                <textarea name="customer_notes" cols="30" rows="6" class="form-control" placeholder="{{__('Special Requirements')}}"></textarea>
            </div>
        </div>
    </div>

    @include ('Booking::frontend/booking/checkout-passengers')
    @include ('Booking::frontend/booking/checkout-deposit')
    @include ($service->checkout_form_payment_file ?? 'Booking::frontend/booking/checkout-payment')

    @php
    $term_conditions = setting_item('booking_term_conditions');
    @endphp

    <div class="form-group">
        <label class="term-conditions-checkbox">
            <input type="checkbox" name="term_conditions"> {{__('I have read and accept the')}}  <a target="_blank" href="{{get_page_url($term_conditions)}}">{{__('terms and conditions')}}</a>
        </label>
    </div>
    @if(setting_item("booking_enable_recaptcha"))
        <div class="form-group">
            {{recaptcha_field('booking')}}
        </div>
    @endif
    <div class="html_before_actions"></div>

    <p class="alert-text mt10" v-show=" message.content" v-html="message.content" :class="{'danger':!message.type,'success':message.type}"></p>

    <div class="form-actions">
        <button class="btn btn-danger" @click="doCheckout">{{__('Submit')}}
            <i class="fa fa-spin fa-spinner" v-show="onSubmit"></i>
        </button>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const country = document.getElementById('country-sms-testing');
        const statesSelect = document.getElementById('state');
        const city = document.getElementById('city');

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

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"></script>

<style>
    #phone {
        width: 100%;
        padding-right: 150px;
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
            storedData = null; // قيمة افتراضية عند الخطأ
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
