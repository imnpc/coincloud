<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="renderer" content="webkit">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ Admin::title() }} @if($header) | {{ $header }}@endif</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    @if(!is_null($favicon = Admin::favicon()))
        <link rel="shortcut icon" href="{{$favicon}}">
    @endif

    {!! Admin::css() !!}

    <script src="{{ Admin::jQuery() }}"></script>
    {!! Admin::headerJs() !!}
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body class="hold-transition {{config('admin.skin')}} {{join(' ', config('admin.layout'))}}">

@if($alert = config('admin.top_alert'))
    <div style="text-align: center;padding: 5px;font-size: 12px;background-color: #ffffd5;color: #ff0000;">
        {!! $alert !!}
    </div>
@endif

<div class="wrapper">

    @include('admin::partials.header')

    @include('admin::partials.sidebar')

    <div class="content-wrapper" id="pjax-container">
        {!! Admin::style() !!}
        <div id="app">
            @yield('content')
        </div>
        {!! Admin::script() !!}
        {!! Admin::html() !!}
    </div>

    @include('admin::partials.footer')

</div>

<button id="totop" title="Go to top" style="display: none;"><i class="fa fa-chevron-up"></i></button>

<script>
    function LA() {
    }

    LA.token = "{{ csrf_token() }}";
    LA.user = @json($_user_);
</script>

<!-- REQUIRED JS SCRIPTS -->
{!! Admin::js() !!}

{{-- 音频通知 --}}
<audio style="display:none; height: 0" id="bg-music" preload="auto" src="/mp3/order.mp3" loop="loop"></audio>

<script>
    function LA() {
    }

    LA.token = "{{ csrf_token() }}";

    var getting = {
        url: '/admin/api/sendNotice',
        dataType: 'json',
        success: function (res) {
            console.log(res);
            if (res.code == 200) {

                toastr.options.onclick = function () {
                    location.href = '/admin/orders';  // 点击跳转页面
                };
                toastr.options.timeOut = 120000; // 保存2分钟
                toastr.warning(res.msg); // 提示文字

                var audio = document.getElementById('bg-music');  // 启用音频通知
                audio.play();
                setTimeout(function () {
                    audio.load(); // 20 秒后关闭音频通知
                }, 30000);
            }
        },
        error: function (res) {
            console.log(res);
        }
    };

    //关键在这里，Ajax定时访问服务端，不断获取数据 ，这里是 60 秒请求一次。
    window.setInterval(function () {
        $.ajax(getting)
    }, 60000);

</script>
</body>
</html>
