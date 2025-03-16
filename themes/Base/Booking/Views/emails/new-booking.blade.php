@extends('Email::layout')
@section('content')

    <div class="b-container">
        <div class="b-panel">
            @switch($to)
                @case ('admin')
                    <h3 class="email-headline"><strong>{{__('Hello Administrator')}}</strong></h3>
                    <p>{{__('New booking has been made')}}</p>
                @break
                @case ('vendor')
                    <h3 class="email-headline"><strong>{{__('Hello :name',['name'=>$booking->vendor->nameOrEmail ?? ''])}}</strong></h3>
                    <p>{{__('Your service has new booking')}}</p>
                @break

                @case ('customer')
                    <h3 class="email-headline"><strong>{{__('Hello :name',['name'=>$booking->first_name ?? ''])}}</strong></h3>
                    <p>{{__('Thank you for booking with us. Here are your booking information:')}}</p>
                @break

            @endswitch

            @include($service->email_new_booking_file ?? '')
            <p>{{ __('Your invoice can be viewed at the following link:') }}</p>
            <div class="text-center">
                <a class="btn btn-primary" href="{{ route('user.booking.invoice', ['code' => $booking->code]) }}" target="_blank">{{ __('View Invoice') }}</a>
            </div>
        </div>
        @include('Booking::emails.parts.panel-customer')
        @include('Booking::emails.parts.panel-passengers')
    </div>
@endsection
