<tr>
    <td class="booking-history-type">
        @if($service = $booking->service)
            <i class="{{$service->getServiceIconFeatured()}}"></i>
        @endif
        <small>{{$booking->object_model}}</small>
        <div>
            {{ "#".$booking->id }}
        </div>
    </td>
    <td>
        @if($service = $booking->service)
            @php
                $translation = $service->translate();
            @endphp
            <a target="_blank" href="{{$service->getDetailUrl()}}">
                {{$translation->title}}
            </a>
        @else
            {{__("[Deleted]")}}
        @endif
    </td>
    <td class="a-hidden">{{display_date($booking->created_at)}}</td>
    <td class="a-hidden">
        {{__("Check in")}} : {{display_date($booking->start_date)}} <br>
        {{__("Check out")}} : {{display_date($booking->end_date)}} <br>
        {{__("Duration")}} :

        @if($booking->duration_nights <= 1)
            {{__(':count night',['count'=>$booking->duration_nights])}}
        @else
            {{__(':count nights',['count'=>$booking->duration_nights])}}
        @endif
    </td>
    <td>{{format_money_main($booking->total)}}</td>
    <td>{{format_money($booking->paid)}}</td>
    <td>{{format_money($booking->total - $booking->paid)}}</td>
    <td class="{{$booking->status}} a-hidden">{{$booking->statusName}}</td>
    <td width="2%">
        @if($service = $booking->service)
            <a class="btn btn-xs btn-primary btn-info-booking" data-ajax="{{route('booking.modal',['booking'=>$booking])}}" data-toggle="modal" data-id="{{$booking->id}}" data-target="#modal_booking_detail">
                <i class="fa fa-info-circle"></i>{{__("Details")}}
            </a>
        @endif
        <a href="{{route('user.booking.invoice',['code'=>$booking->code])}}" class="btn btn-xs btn-primary btn-info-booking open-new-window mt-1" onclick="window.open(this.href); return false;">
            <i class="fa fa-print"></i>{{__("Invoice")}}
        </a>
        @if($booking->status == 'unpaid')
            <a href="{{route('booking.checkout',['code'=>$booking->code])}}" class="btn btn-xs btn-primary btn-info-booking open-new-window mt-1">
                {{__("Pay now")}}
            </a>
        @endif

        @if(!empty(setting_item("hotel_allow_customer_can_change_their_booking_status")))
        <div class="dropdown" style="display: inline-block; position: relative;">
            <a class="btn btn-xs btn-info btn-make-as @if($booking->status !== 'cancelled') dropdown-toggle @endif"
               @if($booking->status !== 'cancelled') data-toggle="dropdown" @endif
               aria-haspopup="true" aria-expanded="false">
                <i class="icofont-ui-settings"></i>
                {{__("Action")}}
            </a>
            @if($booking->status !== 'cancelled'&& $booking->status != 'rejected')
                <div class="dropdown-menu" style="min-width: 80px; width: auto; text-align: center; position: absolute; top: 100%; left: 0;">
                    @if(!empty($statues))
                        @foreach($statues as $status)
                            @if($status === 'cancelled')
                                <a href="{{ route("hotel.customer.booking_history.bulk_edit", ['id' => $booking->id, 'status' => $status]) }}" class="dropdown-item">
                                    {{__('Cancel')}}
                                </a>
                            @endif
                        @endforeach
                    @endif
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        {{__('Update')}}
                    </a>
                </div>
            @endif
        </div>
    @endif


    </td>
</tr>
