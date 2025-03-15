@extends('Email::layout')
@section('content')
<div class="b-container">
    <div class="b-panel">
        <h1>{!! __("Hello, :first_name :last_name",['first_name'=>$admin->first_name, 'last_name'=>$admin->last_name])!!}</h1>

        <p>{!! __('You have been received request from :business_name to create business profile.',['business_name'=>$business->business_name]) !!}</p>
        <p>{{__('We hope you enjoy your time with us.')}}</p>

        <br>
        <p>{{__('Regards')}},<br>{{setting_item('site_title')}}</p>
    </div>
</div>
@endsection
