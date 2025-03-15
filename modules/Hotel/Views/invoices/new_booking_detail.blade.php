<?php
$translation = $service->translate();
$lang_local = app()->getLocale();
$dir = ($lang_local == 'ar')? 'right' : 'left';
?>
<div class="b-panel-title">{{ __('Hotel information') }}</div>

<div class="text-center mt20">
    <a href="{{ route('user.booking_history') }}" target="_blank"
        class="btn btn-primary manage-booking-btn">{{ __('Manage Bookings') }}</a>
</div>
<div class=" mb-4">
    <div class="row d-flex justify-content-between">
        <div class="col-md-5 pt-md-2">
            <div class="row">
                <div class="col col-md-6"><strong>{{ __('Booking Number') }}</strong>:</div>
                <div class="col col-md-6" align="{{ $dir }}"> #{{ $booking->id }}</div>
            </div>
        </div>
        <div class="col-md-5 pt-md-2">
            <div class="row">
                <div class="col col-md-6"><strong>{{ __('Booking Status') }}</strong>:</div>
                <div  class="col col-md-6" align="{{ $dir }}">{{ $booking->statusName }}</div>
            </div>
        </div>
        {{-- @if ($booking->gatewayObj)
            <div class="col-md-5 pt-md-2">
                <div class="row">
                    <div class="col col-md-6"><strong>{{ __('Payment method') }}</strong>: </div>
                    <div class="col col-md-6">{{ __($booking->gatewayObj->getOption('name')) }}</div>
                </div>
            </div>
        @endif --}}
        {{-- @if ($booking->gatewayObj and ($note = $booking->gatewayObj->getOption('payment_note')))
            <div class="col-md-5 pt-md-2">
                <div class="row">
                    <div class="col col-md-8"><strong>{{ __('Payment Note') }}</strong>:</div>
                    <div class="col col-md-4">{!! clean($note) !!}</div>
                </div>
            </div>
        @endif --}}
        <div class="col-md-5 pt-md-2">
            <div class="row">
                <div class="col col-md-6"><strong>{{ __('Hotel name') }}</strong>:</div>
                <div class="col col-md-6" align="{{ $dir }}"><a href="{{ $service->getDetailUrl() }}">{!! clean($translation->title) !!}</a></div>
            </div>
        </div>
        @if ($translation->address)
            <div class="col-md-5 pt-md-2">
                <div class="row">
                    <div class="col col-sm-6 col-md-6"><strong>{{ __('Address') }}</strong>:</div>
                    <div class="col col-sm-6 col-md-6" align="{{ $dir }}">{{ __($translation->address) }}</div>
                </div>
            </div>
        @endif
        @if ($booking->start_date && $booking->end_date)
            <div class="col-md-12 pt-md-2">
                <div class="row d-flex justify-content-between">
                    <div class="col-md-5 pt-md-2">
                        <div class="row">
                            <div class="col col-md-6">
                                <strong>{{ __('Check in') }}</strong>:
                            </div>
                            <div class="col col-md-6" align="{{ $dir }}">
                                {{ display_date($booking->start_date) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 pt-md-2">
                        <div class="row">
                            <div class="col col-md-6">
                                <strong>{{ __('Check out') }}</strong>:
                            </div>
                            <div class="col col-md-6" align="{{ $dir }}">
                                {{ display_date($booking->end_date) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="col-md-12 pt-md-2">
            <div class="row d-flex justify-content-between">
                <div class="col-md-5 pt-md-2">
                    <div class="row">
                        <div class="col col-md-6">
                            <strong>{{ __('Guest name ref') }}</strong>
                        </div>
                        <div class="col col-md-6" align="{{ $dir }}">

                        </div>
                    </div>
                </div>
                <div class="col-md-5 pt-md-2">
                    <div class="row">
                        <div class="col col-md-6">
                            <strong>{{ __('Vendor ref') }}</strong>
                        </div>
                        <div class="col col-md-6" align="{{ $dir }}">

                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- @if ($meta = $booking->getMeta('adults'))
        <div class="col-md-6">
            <strong>{{__('Adults')}}</strong>: <strong>{{$meta}}</strong>
        </div>
        @endif
        @if ($meta = $booking->getMeta('children'))
        <div class="col-md-6">
            <strong>{{__('Children')}}</strong>: <strong>{{$meta}}</strong>
        </div>
        @endif --}}
    </div>
</div>

<table class="table-bordered" width="100%" cellspacing="0" cellpadding="5" style="border-collapse: collapse;">
    <thead>
        <tr style="background-color: #f0f0f0;">
            <th>{{ __('Room Type') }}</th>
            <th style="text-align: center">{{ __('Qty') }}</th>
            <th style="text-align: center">{{ __('Nights') }}</th>
            <th style="text-align: center">{{ __('Amount') }}</th>
            <th style="text-align: center">{{ __('Total') }}</th>
        </tr>
    </thead>
    <tbody>
        @php $rooms = \Modules\Hotel\Models\HotelRoomBooking::getByBookingId($booking->id) @endphp
        @foreach ($rooms as $room)
            <!-- Room Details Row -->
            <tr>
                <td style="">
                    <strong>{{ $room->room->translate()->title }} ({{ $room->room->size }} {{ __('m') }}<sup>2</sup>)</strong><br>
                    <ul style="padding-left: 20px; padding-right: 20px">
                        <li>{{ __('Number of beds') }}: {{ $room->room->beds }}, {{ __('size wide') }} {{ $room->room->bed_width_from }} - {{ $room->room->bed_width_to }} {{ __('cm') }}</li>
                        <li>{{ __('Max') }}: <strong>{{ $room->room->adults }}</strong>
                            {{ __('Adults') }}
                            ,{{ __('Max') }}: <strong>{{ $room->room->children }}</strong>
                            {{ __('Children') }}</li>
                    </ul>
                    {{-- Attributes Here --}}
                    <div style="max-width: 100%">
                        @php $roomAttributes = \Modules\Hotel\Models\HotelRoomTerm::getRoomTerms($room->room->id, app()->getLocale()) @endphp
                        <br>
                        <div class="room-features">
                            @foreach($roomAttributes as $attribute)
                                    {{ implode(', ', $attribute['terms']->pluck('name')->toArray()) }}
                                    @if (!$loop->last)
                                        ,
                                    @endif
                            @endforeach
                        </div>
                    </div>
                </td>
                <td style="text-align: center" align="center">{{ $room->number }}</td>
                <td style="text-align: center" align="center">{{ $booking->duration_nights }}</td>
                <td style="text-align: center" align="center">{{ ($room->price) }}</td>
                <td style="text-align: center" align="center">{{ ($room->price * $room->number) }}</td>
            </tr>
        @endforeach
        <!-- Extra Services Row -->
        @foreach ($booking->getJsonMeta('extra_price') as $extra)
            <tr>
                <td style="border-top: none; border-bottom: none">
                @if ($lang_local == 'ar')
                    {{ $extra['name_ar'] }}
                @else
                    {{ $extra['name'] }}
                @endif</td>
                @if(isset($extra['per_person']) && $extra['per_person'] == 'on')
                    <td style="text-align: center; border-top: none; border-bottom: none" align="center">
                        {{ $booking->getMeta('guests') }}</td>
                @else
                    <td style="text-align: center; border-top: none; border-bottom: none" align="center">1</td>
                @endif
                @if($extra['type'] == 'per_day')
                    <td style="text-align: center; border-top: none; border-bottom: none" align="center">{{ $booking->duration_nights }}</td>
                @else
                <td style="text-align: center; border-top: none; border-bottom: none" align="center">1</td>
                @endif
                <td style="text-align: center; border-top: none; border-bottom: none" align="center">{{ ($extra['price']) }}
                </td>
                <td style="text-align: center; border-top: none; border-bottom: none" align="center">{{ ($extra['total']) }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Total Calculation -->
<table width="100%" cellspacing="0" cellpadding="5" style="margin-top: 20px;">
    @php
    $dir = 'right';
        if(app()->getLocale() == 'ar') {
            $dir = 'left';
        }
    @endphp
    <tr>
        <td align="left" style="text-align: {{ $dir }}"><strong>{{ __('Total before VAT') }}:</strong></td>
        <td align="left" style="text-align: {{ $dir }}">{{ format_money($booking->total_before_fees) }}</td>
    </tr>
    <tr>
        <td align="left" style="text-align: {{ $dir }}"><strong>{{ __('VAT') }}:</strong></td>
        <td align="left" style="text-align: {{ $dir }}">{{ format_money($booking->vat) }}</td>
    </tr>
    @php
        $list_all_fee = [];
        if (!empty($booking->buyer_fees)) {
            $buyer_fees = json_decode($booking->buyer_fees, true);
            $list_all_fee = $buyer_fees;
        }
        if (!empty(($vendor_service_fee = $booking->vendor_service_fee))) {
            $list_all_fee = array_merge($list_all_fee, $vendor_service_fee);
        }
    @endphp
    @if (!empty($list_all_fee))
        @foreach ($list_all_fee as $item)
            @php
                $fee_price = $item['price'];
                if (!empty($item['unit']) and $item['unit'] == 'percent') {
                    $fee_price = ($booking->total_before_fees / 100) * $item['price'];
                }
            @endphp
            <tr>
                <td align="right" class="label" style="text-align: {{ $dir }}">
                    <strong>{{ __('Service fee') }}</strong>
                    <i class="icofont-info-circle" data-toggle="tooltip" data-placement="top"
                        title="{{ $item['desc_' . $lang_local] ?? $item['desc'] }}"></i>
                    @if (!empty($item['per_person']) and $item['per_person'] == 'on')
                        : {{ $booking->total_guests }} * {{ format_money($fee_price) }}
                    @endif
                </td>
                <td class="val">
                    @if (!empty($item['per_person']) and $item['per_person'] == 'on')
                        {{ format_money($fee_price * $booking->total_guests) }}
                    @else
                        {{ format_money($fee_price) }}
                    @endif
                </td>
            </tr>
        @endforeach
    @endif
    @if (!empty($booking->coupon_amount) and $booking->coupon_amount > 0)
        <tr>
            <td class="label">
                {{ __('Coupon') }}
            </td>
            <td class="val">
                -{{ format_money($booking->coupon_amount) }}
            </td>
        </tr>
    @endif

    <tr style="background-color: #f0f0f0;">
        <td align="right" style="text-align: {{ $dir }}"><strong>{{ __('Total Amount') }}:</strong></td>
        <td align="right" style="text-align: {{ $dir }}"><strong style="color: #FA5636">{{ format_money($booking->total) }}</strong></td>
    </tr>
</table>

<hr>

<!-- Hotel Policies -->
<div class="hotel-policies">
    <p style="text-align: {{ (app()->getLocale() == 'ar') ? 'right':'left' }}">{{ __('Check In') }}: {{ $service->check_in_time }} - {{ __('Check Out') }}: {{ $service->check_out_time }}</p>
    @php
        $policies = $translation->policy;
    @endphp
    @if ($policies && is_array($policies))
        <table>
            @foreach ($policies as $policy)
                <tr>
                    <th><strong style="font-size: 15px">{{ $policy['title'] }}</strong></th>
                </tr>
                <tr>
                    <td>
                        <ul style="list-style-type: none">
                            @foreach (explode("\r\n", $policy['content']) as $description)
                                <li>{{ $description }}</li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
            @endforeach
        </table>
    @endif
</div>
