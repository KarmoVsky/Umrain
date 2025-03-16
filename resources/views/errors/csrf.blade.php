<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', __('Session Expired'))</title>

        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 36px;
                padding: 20px;
                color: #ff5733;
            }

            p {
                font-size: 18px;
            }


        </style>
    </head>
    <body>
        <div class="flex-center position-ref">
            <div class="content">
                <img src="{{asset('/images/icons/svg/Hourglass.svg')}}" alt="Session Expired">
{{--                <i class="bi bi-hourglass-bottom"></i>--}}
                <div class="title">
                    @yield('message', __('Still around?'))
                </div>
                <p>@yield('detail', __('Refresh the page to see the latest update.')) </p>

                <button class="btn btn-danger btn-block btn-md mb-3" onclick="location.reload()">
                    <strong>{{__("Refresh")}}</strong>
                </button>
            </div>
        </div>
    </body>
</html>
