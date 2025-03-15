@extends('admin.layouts.app')

@section('content')
    <form action="{{route('hotel.admin.store',['id'=>($row->id) ? $row->id : '-1','lang'=>request()->query('lang')])}}" method="post">
        @csrf
        <div class="container-fluid">
            <div class="d-flex justify-content-between mb20">
                <div class="">
                    <h1 class="title-bar">{{$row->id ? __('Edit: ').$row->title : __('Add new hotel')}}</h1>
                    @if($row->slug)
                        <p class="item-url-demo">{{__("Permalink")}}: {{ url( config('hotel.hotel_route_prefix') ) }}/<a href="#" class="open-edit-input" data-name="slug">{{$row->slug}}</a>
                        </p>
                    @endif
                </div>
                <div class="">
                    @if($row->id)
                        <a class="btn btn-warning btn-xs" href="{{route('hotel.admin.room.index',['hotel_id'=>$row->id])}}" target="_blank"><i class="fa fa-hand-o-right"></i> {{__("Manage Rooms")}}</a>
                    @endif
                    @if($row->slug)
                        <a class="btn btn-primary btn-xs" href="{{$row->getDetailUrl(request()->query('lang'))}}" target="_blank">{{__("View Hotel")}}</a>
                    @endif
                </div>
            </div>
            @include('admin.message')
            @if($row->id)
                @include('Language::admin.navigation')
            @endif
            <div class="lang-content-box">
                <div class="row">
                    <div class="col-md-9">
                        @include('Hotel::admin.hotel.content')

                        @include('Hotel::admin.hotel.pricing')

                        @include('Hotel::admin.hotel.location')

                        @include('Hotel::admin.hotel.surrounding')

                        @include('Core::admin/seo-meta/seo-meta')
                    </div>
                    <div class="col-md-3">
                        <div class="panel">
                            <div class="panel-title"><strong>{{__('Publish')}}</strong></div>
                            <div class="panel-body">
                                @if(is_default_lang())
                                    <div>
                                        <label><input @if($row->status=='publish') checked @endif type="radio" name="status" value="publish"> {{__("Publish")}}
                                        </label></div>
                                    <div>
                                        <label><input @if($row->status=='draft') checked @endif type="radio" name="status" value="draft"> {{__("Draft")}}
                                        </label></div>
                                @endif
                                <div class="text-right">
                                    <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> {{__('Save Changes')}}</button>
                                </div>
                            </div>
                        </div>
                        @if(is_default_lang())
                        <div class="panel">
                            <div class="panel-title"><strong>{{ __("Author Setting") }}</strong></div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label>{{ __('Author Setting') }}</label>
                                    <?php
                                    $vendors = !empty($row->acceptedVendors)
                                        ? $row->acceptedVendors->map(function($vendor) {
                                            return [
                                                'id' => $vendor->business->id,
                                                'text' => $vendor->business->business_name
                                            ];
                                        })->toArray()
                                        : [];
                                        $vendors = array_map('unserialize', array_unique(array_map('serialize', $vendors)));
                                        \App\Helpers\AdminForm::select2('vendors[]', [
                                            'configs' => [
                                                'ajax' => [
                                                    'url' => route('hotel.admin.getVendorsForSelect2'),
                                                    'dataType' => 'json',
                                                    'delay' => 250
                                                ],
                                                'placeholder' => __('-- Select Vendors --'),
                                                'allowClear' => true,
                                            ]
                                        ], $vendors, true);
                                    $user = !empty($row->author_id) ? App\User::find($row->author_id) : false;

                                    ?>
                                </div>
                                @if(!empty($vendor_requests))
                                <div class="mb-3">
                                    @foreach ($vendor_requests as $vendor_request)
                                    <div class="row mb-1">
                                        <div class="col-md-6 pl0">
                                            <span>{{ $vendor_request->business->business_name }}</span>
                                        </div>
                                        <div class="col-md-6 p-0">
                                            <a class="badge badge-success" href="{{ route('hotel.admin.accept_vendor', ['id' => $vendor_request->id, 'status' => 'approved']) }}">{{ __('Accept') }}</a>
                                            <a class="badge badge-danger" href="{{ route('hotel.admin.accept_vendor', ['id' => $vendor_request->id, 'status' => 'delete']) }}">{{ __('Reject') }}</a>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                                <div>
                                    <label id="user-info">
                                        <?php if ($user): ?>
                                            {!! __(":name is :visible", [
                                                'name' => '<strong>' . $user->getDisplayName() . '</strong>',
                                                'visible' => $user->is_visible
                                                    ? '<strong><span style="color: green;">visible</span></strong>'
                                                    : '<strong><span style="color: red;">invisible</span></strong>'
                                            ]) !!}
                                        <?php else: ?>
                                            {{ __("No author selected or user not found.") }}
                                        <?php endif; ?>
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label for="vendor_override_show_name">{{ __("Display Vendor Name for this Hotel") }}</label>
                                    <select name="vendor_override_show_name" id="vendor_override_show_name" class="form-control">
                                        <option value="by_default" {{ empty($row->vendor_override_show_name) || $row->vendor_override_show_name === 'by_default' ? 'selected' : '' }}>
                                            {{ __("Default") }}
                                        </option>
                                        <option value="hidden" {{ !empty($row->vendor_override_show_name) && $row->vendor_override_show_name === 'hidden' ? 'selected' : '' }}>
                                            {{ __("Hidden") }}
                                        </option>
                                        <option value="show" {{ !empty($row->vendor_override_show_name) && $row->vendor_override_show_name === 'show' ? 'selected' : '' }}>
                                            {{ __("Show") }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if(is_default_lang())
                        <div class="panel" >
                            <div class="panel-title"><strong>{{ __('Hotel Commission')}}</strong></div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label>{{__('hotel Commission Type')}}</label>
                                    <div class="form-controls">
                                        <select name="hotel_commission_type" id="hotel_commission_type" class="form-control">
                                            <option value="default" {{old("hotel_commission_type",($row->hotel_commission_type ?? '')) == 'default' ? 'selected' : ''  }}>{{__('Default')}}</option>
                                            <option value="percent" {{old("hotel_commission_type",($row->hotel_commission_type ?? '')) == 'percent' ? 'selected' : ''  }}>{{__('Percent')}}</option>
                                            <option value="amount" {{old("hotel_commission_type",($row->hotel_commission_type ?? '')) == 'amount' ? 'selected' : ''  }}>{{__('Amount')}}</option>
                                            <option value="disable" {{old("hotel_commission_type",($row->hotel_commission_type ?? '')) == 'disable' ? 'selected' : ''  }}>{{__('Disable Commission')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>{{__('hotel commission value')}}</label>
                                    <div class="form-controls">
                                        <input type="text" class="form-control" name="hotel_commission_amount" value="{{old("hotel_commission_amount",($row->hotel_commission_amount ?? '')) }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('hotel Commission Calculate Way') }}</label>
                                    <div class="form-controls">
                                        <select name="hotel_commission_calculate_way" class="form-control">
                                            <option value="default" {{ old("hotel_commission_calculate_way", ($row->hotel_commission_calculate_way ?? '')) == 'default' ? 'selected' : '' }}>
                                                {{ __('Default') }}
                                            </option>
                                            <option value="addition" {{ old("hotel_commission_calculate_way", ($row->hotel_commission_calculate_way ?? '')) == 'addition' ? 'selected' : '' }}>
                                                {{ __('Addition') }}
                                            </option>
                                            <option value="dedict" {{ old("hotel_commission_calculate_way", ($row->hotel_commission_calculate_way ?? '')) == 'dedict' ? 'selected' : '' }}>
                                                {{ __('Dedict') }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('hotel Commission Calculate Time') }}</label>
                                    <div class="form-controls">
                                        <select name="hotel_commission_calculate_time" class="form-control">
                                            <option value="default" {{ old("hotel_commission_calculate_time", ($row->hotel_commission_calculate_time ?? '')) == 'Default' ? 'selected' : '' }}>
                                                {{ __('Default') }}
                                            </option>
                                            <option value="one-time" {{ old("hotel_commission_calculate_time", ($row->hotel_commission_calculate_time ?? '')) == 'one-time' ? 'selected' : '' }}>
                                                {{ __('One-Time') }}
                                            </option>
                                            <option value="per-day" {{ old("hotel_commission_calculate_time", ($row->hotel_commission_calculate_time ?? '')) == 'per-day' ? 'selected' : '' }}>
                                                {{ __('Per-Day') }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group" data-condition="hotel_commission_type:not(percent)">
                                    <label>{{ __('Per Person') }}</label>
                                    <div class="form-controls">
                                        <select name="hotel_per_person" class="form-control">
                                            <option value="default" {{ old("hotel_per_person", ($row->hotel_per_person ?? '')) == 'default' ? 'selected' : '' }}>
                                                {{ __('Default') }}
                                            </option>
                                            <option value="1" {{ old("hotel_per_person", ($row->hotel_per_person ?? '')) == '1' ? 'selected' : '' }}>
                                                {{ __('Select') }}
                                            </option>
                                            <option value="0" {{ old("hotel_per_person", ($row->hotel_per_person ?? '')) == '0' ? 'selected' : '' }}>
                                                {{ __('Deselect') }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(is_default_lang())
                            <div class="panel">
                                <div class="panel-title"><strong>{{__("Availability")}}</strong></div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label>{{__('Hotel Featured')}}</label>
                                        <br>
                                        <label>
                                            <input type="checkbox" name="is_featured" @if($row->is_featured) checked @endif value="1"> {{__("Enable featured")}}
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label>{{__('Hotel Related IDs')}}</label>
                                        <input
                                            type="text"
                                            value="{{$row->related_ids}}"
                                            placeholder="{{__("Eg: 100,200")}}"
                                            name="related_ids"
                                            class="form-control"
                                        >
                                        <p>
                                            <i>{{__("Separated by comma")}}</i>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @include('Hotel::admin.hotel.attributes')

                            <div class="panel">
                                <div class="panel-title"><strong>{{__('Feature Image')}}</strong></div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        {!! \Modules\Media\Helpers\FileHelper::fieldUpload('image_id',$row->image_id) !!}
                                    </div>
                                </div>
                            </div>
                           @include('Hotel::admin.hotel.ical')
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

@push('js')
    {!! App\Helpers\MapEngine::scripts() !!}
    <script>
        jQuery(function ($) {
            new BravoMapEngine('map_content', {
                disableScripts: true,
                fitBounds: true,
                center: [{{$row->map_lat ?? setting_item('map_lat_default',51.505 ) }}, {{$row->map_lng ?? setting_item('map_lng_default',-0.09 ) }}],
                zoom:{{$row->map_zoom ?? "8"}},
                ready: function (engineMap) {
                    @if($row->map_lat && $row->map_lng)
                    engineMap.addMarker([{{$row->map_lat}}, {{$row->map_lng}}], {
                        icon_options: {}
                    });
                    @endif
                    engineMap.on('click', function (dataLatLng) {
                        engineMap.clearMarkers();
                        engineMap.addMarker(dataLatLng, {
                            icon_options: {}
                        });
                        $("input[name=map_lat]").attr("value", dataLatLng[0]);
                        $("input[name=map_lng]").attr("value", dataLatLng[1]);
                    });
                    engineMap.on('zoom_changed', function (zoom) {
                        $("input[name=map_zoom]").attr("value", zoom);
                    });
                    if(bookingCore.map_provider === "gmap"){
                        engineMap.searchBox($('#customPlaceAddress'),function (dataLatLng) {
                            engineMap.clearMarkers();
                            engineMap.addMarker(dataLatLng, {
                                icon_options: {}
                            });
                            $("input[name=map_lat]").attr("value", dataLatLng[0]);
                            $("input[name=map_lng]").attr("value", dataLatLng[1]);
                        });
                    }
                    engineMap.searchBox($('.bravo_searchbox'),function (dataLatLng) {
                        engineMap.clearMarkers();
                        engineMap.addMarker(dataLatLng, {
                            icon_options: {}
                        });
                        $("input[name=map_lat]").attr("value", dataLatLng[0]);
                        $("input[name=map_lng]").attr("value", dataLatLng[1]);
                    });
                }
            });
        })
    </script>
@endpush

