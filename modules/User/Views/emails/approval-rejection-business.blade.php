@extends('Email::layout')
@section('content')
<div class="b-container">
    <div class="b-panel">
        <h1>{!! __("Hello, :first_name :last_name",['first_name'=>$user->first_name, 'last_name'=>$user->last_name])!!}</h1>

        @if($business->status === 'approved')
        <p>{!! __('Your request to create :business_name has been accepted and you can now manage the services.', ['business_name'=>$business->business_name]) !!}</p>
        @else
        <p>{!! __("Your request to create :business_name has been rejected and you can't manage the services.", ['business_name'=>$business->business_name]) !!}</p>
        @endif

        <p>{{ __('We hope you enjoy your time with us.') }}</p>

        <br>
        <p>{{__('Regards')}},<br>{{setting_item('site_title')}}</p>
    </div>
</div>
@endsection
