@extends('layouts.user')
@section('content')
    <div class="d-flex justify-content-between align-items-center" style="">
        <h2 class="title-bar no-border-bottom">
            {{ __('Edit: ') . $user->first_name . ' ' . $user->last_name }}
        </h2>
    </div>
    @include('admin.message')
    <div class="panel mt-3">
        <div class="panel-title">
            <strong>{{ __('User Info') }}</strong>
        </div>
        <div class="panel-body">
            <form action="{{ route('user.vendor.update') }}" method="POST" class="bravo-form-item">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id }}" id="">
                <h3>{{ $user->first_name . ' ' . $user->last_name }}</h3>
                <div class="form-group">
                    <label for="roles"><strong>{{ __('Select a role') }}</strong></label>
                    <select name="role" id="role" class="form-control mb-2 w-50" required>
                        <option value="">{{ __('-- Select a role --') }}</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" class="" @if($role->selected) selected @endif>{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="services"><strong>{{ __('Select a service(s)') }}</strong></label>
                    @foreach ($services as $service)
                        <div class="row mt-1" style="">
                            <div class="col-2" style="text-align: end; padding: 5px;">
                                <label for="">{{ ucfirst($service) }}</label>
                            </div>
                            <div class="col-5 pl-0">
                                <select name="" id="{{ $service }}_locations"
                                    class="form-control mb-2 d-inline-block select2 select2_locations" multiple="multiple">
                                    <option value="">{{ __('-- Select location(s) --') }}</option>
                                    @if (isset($locations[$service]))
                                        @foreach ($locations[$service] as $item)
                                            <option value="{{ $item['location_id'] }}">
                                                {{ ucfirst($item['location_name']) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-5 pl-0">
                                <select name="{{ $service }}_services[]" id="{{ $service }}_services"
                                    class="form-control mb-2 select2 select2_services" multiple="multiple">
                                    <option value="">{{ __('-- Select service(s) --') }}</option>
                                </select>
                            </div>
                        </div>
                    @endforeach
                    <div id="error-services-message" style="color: red; display: none; margin-top: 5px;"></div>
                </div>
                <div class="">
                    <button class="btn btn-primary" type="submit">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>


{{-- الكود الاخير الصحيح --}}
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

        // دالة لتعبئة الخدمات بناءً على المواقع المختارة
        function updateServices(selectElement) {
            var selectedLocations = selectElement.val() || [];
            var serviceType = selectElement.attr('id').split('_')[0];
            var servicesDropdown = $("#" + serviceType + "_services");

            // استخدم مجموعة (Set) لتجميع جميع الخدمات بدون تكرار
            var newServices = new Set();

            selectedLocations.forEach(function(locationId) {
                locationsData[serviceType].forEach(function(location) {
                    if (location.location_id == locationId) {
                        location.services.forEach(function(serviceItem) {
                            newServices.add(JSON.stringify(serviceItem));
                        });
                    }
                });
            });

            // تحويل الخدمات من JSON إلى كائنات فعلية
            var servicesArray = Array.from(newServices).map(function(item) {
                return JSON.parse(item);
            });

            // الاحتفاظ بالخدمات المختارة مسبقًا فقط أثناء التفاعل مع التغيير
            var selectedServices = servicesDropdown.val() || [];

            // تحديث قائمة الخدمات في الـ select
            servicesDropdown.empty().append('<option value="">{{ __("-- Select service(s) --") }}</option>');
            servicesArray.forEach(function(serviceItem) {
                servicesDropdown.append('<option value="' + serviceItem.id + '">' + serviceItem.name + '</option>');
            });

            // إزالة الخدمات التي لم تعد مرتبطة بالمواقع المحددة
            selectedServices = selectedServices.filter(serviceId => {
                return servicesArray.some(serviceItem => serviceItem.id == serviceId);
            });

            servicesDropdown.val(selectedServices).trigger('change');
        }

        // عند تغيير حقل المواقع، يتم تحديث الخدمات بناءً على المواقع المختارة
        $('.select2_locations').on('change', function() {
            updateServices($(this));
        });

        // دالة لملء الحقول تلقائيًا بالبيانات عند تحميل الصفحة
        function preFillData() {
            $.each(locationsData, function(serviceType, locations) {
                var locationSelect = $("#" + serviceType + "_locations");

                var selectedLocationIds = locations.filter(function(location) {
                    return location.selected;
                }).map(function(location) {
                    return location.location_id;
                });

                locationSelect.val(selectedLocationIds).trigger('change');

                // تعبئة الخدمات المختارة تلقائيًا عند تحميل الصفحة
                var servicesDropdown = $("#" + serviceType + "_services");
                var preSelectedServices = [];

                locations.forEach(function(location) {
                    if (location.selected) {
                        location.services.forEach(function(serviceItem) {
                            if (serviceItem.selected) {
                                preSelectedServices.push(serviceItem.id);
                            }
                        });
                    }
                });

                servicesDropdown.val(preSelectedServices).trigger('change');
            });
        }
        preFillData();

        // حدث الإرسال للتحقق من اختيار خدمات
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
                    errorMessageElement.style.display = 'none';
                }, 3000);
            }
        });
    });
</script>


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
