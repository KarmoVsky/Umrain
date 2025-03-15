@extends('Email::layout')
@section('content')
<div class="b-container">
    <div class="b-panel">
        <h1>{!! __("Hello, :first_name :last_name",['first_name'=>$admin->first_name, 'last_name'=>$admin->last_name])!!}</h1>

        <p>{!! __('New request to manage :services has been received from :business_name waiting you reply.',['services'=>implode(', ', array_column($services, 'title')),'business_name'=>$business->business_name]) !!}</p>

        <br>
        <p>{{__('Regards')}},<br>{{setting_item('site_title')}}</p>
    </div>
</div>
@endsection
