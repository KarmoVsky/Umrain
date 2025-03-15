<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ $html_class ?? '' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php event(new \Modules\Layout\Events\LayoutBeginHead()); @endphp
    <style>
        :root {
            --style_other_color: {{ setting_item('style_other_color', '#c4cdd5') }};
            --style_main_color: {{ setting_item('style_main_color', '#5191fa') }};
            --style_btn_primary_color: {{ setting_item('style_btn_primary_color', '#0d6efd') }};
            --style_btn_secondary_color: {{ setting_item('style_btn_secondary_color', '#6c757d') }};
            --style_btn_danger_color: {{ setting_item('style_btn_danger_color', '#dc3545') }};
            --style_btn_info_color: {{ setting_item('style_btn_info_color', '#0dcaf0') }};
            --style_btn_warning_color: {{ setting_item('style_btn_warning_color', '#ffc107') }};
            --style_btn_success_color: {{ setting_item('style_btn_success_color', '#198754') }};
        }
    </style>
    @include('Layout::parts.favicon')

    @include('Layout::parts.seo-meta')
    <link href="{{ asset('libs/bootstrap/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/ionicons/css/ionicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/icofont/icofont.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('dist/frontend/css/notification.css') }}" rel="newest stylesheet">
    <link href="{{ asset('dist/frontend/css/app.css?_ver=' . config('app.asset_version')) }}" rel="stylesheet">
    <link href="{{ asset('dist/frontend/css/load.css?_ver=' . time()) }}" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <link rel="stylesheet" type="text/css" href="{{ asset('libs/daterange/daterangepicker.css') }}">
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel='stylesheet' id='google-font-css-css'
        href='https://fonts.googleapis.com/css?family=Poppins%3A300%2C400%2C500%2C600&display=swap' type='text/css'
        media='all' />

    @if (setting_item('cookie_agreement_type') == 'cookie_consent')
        <link rel="stylesheet" href="{{ asset('libs/cookie-consent/cookieconsent.css') }}" media="print"
            onload="this.media='all'">
    @endif

    {!! \App\Helpers\Assets::css() !!}
    {!! \App\Helpers\Assets::js() !!}
    @include('Layout::parts.global-script')
    <!-- Styles -->
    @stack('css')
    {{-- Custom Style --}}
    <link href="{{ route('core.style.customCss') }}" rel="stylesheet">
    <link href="{{ asset('libs/carousel-2/owl.carousel.css') }}" rel="stylesheet">
    <link href="{{ asset('custom/css/anisth_custom.css') }}" rel="stylesheet">
    @if (setting_item_with_lang('enable_rtl'))
        <link href="{{ asset('dist/frontend/css/rtl.css') }}" rel="stylesheet">
    @endif
    @if (!is_demo_mode())
        {!! setting_item('head_scripts') !!}
        {!! setting_item_with_lang_raw('head_scripts') !!}
    @endif

</head>

<body @if (!is_demo_mode()) {!! setting_item('body_scripts') !!}
        {!! setting_item_with_lang_raw('body_scripts') !!} @endif
    class="frontend-page {{ !empty($row->header_style) ? 'header-' . $row->header_style : 'header-normal' }} {{ $body_class ?? '' }} @if (setting_item_with_lang('enable_rtl')) is-rtl @endif @if (is_api()) is_api @endif">
    @if (!is_demo_mode())
        {!! setting_item('body_scripts') !!}
        {!! setting_item_with_lang_raw('body_scripts') !!}
    @endif

    <div class="bravo_wrap">
        @if (!is_api())
            @include('Layout::parts.topbar')
            @include('Layout::parts.header')
        @endif

        @yield('content')

        @include('Layout::parts.footer')
    </div>
    @if (!is_demo_mode())
        {!! setting_item('footer_scripts') !!}
        {!! setting_item_with_lang_raw('footer_scripts') !!}
    @endif
    @include('demo_script')
</body>

</html>
