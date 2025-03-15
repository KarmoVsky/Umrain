@extends('Email::layout')
@section('content')
<div class="b-container">
    <div class="b-panel">
        <h1>{!! __("Hello, :first_name :last_name",['first_name'=>$user->first_name, 'last_name'=>$user->last_name])!!}</h1>

        @if($status === 'add')
        <p>{!! __('You have been added to :business_name workspace and you can manage them services.', ['business_name'=>$business->business_name]) !!}</p>
        @elseif($status === 'remove')
        <p>{!! __("You have been removed from :business_name workspace and you don't have access any more.", ['business_name'=>$business->business_name]) !!}</p>
        @endif
        <p>{{__('We hope you enjoy your time with us.')}}</p>

        <br>
        <p>{{__('Regards')}},<br>{{setting_item('site_title')}}</p>
    </div>
</div>
@endsection
