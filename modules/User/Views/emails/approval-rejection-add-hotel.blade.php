@extends('Email::layout')
@section('content')
<div class="b-container">
    <div class="b-panel">
        <h1>{!! __("Hello, :first_name :last_name",['first_name'=>$user->first_name, 'last_name'=>$user->last_name])!!}</h1>

        @if($status === 'approved')
        <p>{!! __('Your request to manage :hotel has been accepted and you can now manage the service.', ['hotel'=>$hotel]) !!}</p>
        <p>{{ __('We hope you enjoy your time with us.') }}</p>
        @else
        <p>{!! __("Your request to manage :hotel has been rejected and you can't manage the service.", ['hotel'=>$hotel]) !!}</p>
        @endif


        <br>
        <p>{{__('Regards')}},<br>{{setting_item('site_title')}}</p>
    </div>
</div>
@endsection
