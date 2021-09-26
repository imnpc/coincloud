<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1, minimum-scale=1, user-scalable=no,uc-fitscreen=yes">
    <title>{{ config('app.name') }}-下载APP</title>
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <style type="text/css">
        * {
            margin: 0;
            padding: 0;
            font-size: 14px;
            color: #fff;
        }
        body {
            background: url({{ asset('img/bg.jpg') }}) no-repeat;
            background-size: 100%;
            background-attachment: fixed;
        }
        .fs16 {
            font-size: 16px;
        }
    </style>
</head>
<body>

<div style="text-align: margin:15px; padding: 15px; border-radius: 10px;">
    <div class="bg" style="margin-bottom: 5px; margin-left:10px; margin-top:130px;">
        <a id="imgId"><img src="{{ asset('img/btn.png') }}" width="100%" height="100%"></a>
    </div>
</div>
<script language="javascript">
    $(function () {

        $("#imgId").click(function () {
            download();
        });
        $('body').css({'background-size': '100% ' + $(window).height() + 'px'});
    });

    function download() {
        var cssText =
            "#weixin-tip{position: fixed; left:0; top:0; background: rgba(0,0,0,0.8); filter:alpha(opacity=80); width: 100%; height:100%; z-index: 100;} #weixin-tip p{text-align: center; margin-top: 10%; padding:0 5%;}";
        var u = navigator.userAgent;
        if (u.indexOf('Android') > -1 || u.indexOf('Linux') > -1) { //安卓手机
            //判断使用环境
            if (is_weixin()) {
                loadHtml();
                loadStyleText(cssText);
            } else {
                window.location.href = "{{ $version['download_url'] }}";
            }
        } else if (u.indexOf('iPhone') > -1) { //苹果手机
            alert("软件包正在审核中！");
            //window.location.href = "https://apps.apple.com/cn/app/id1512360212";
        } else if (u.indexOf('Windows Phone') > -1) {
            //winphone手机
            alert("机型不匹配！");
        }
    }

    function is_weixin() {
        var ua = navigator.userAgent.toLowerCase();
        if (ua.match(/MicroMessenger/i) == "micromessenger") {
            return true;
        } else {
            return false;
        }
    }

    function loadHtml() {
        var div = document.createElement('div');
        div.id = 'weixin-tip';
        div.innerHTML = '<p><img src="{{ asset('img/live_weixin.png') }}" alt="请使用手机浏览器打开" width="80%" height="80%"/></p>';
        document.body.appendChild(div);
    }

    function loadStyleText(cssText) {
        var style = document.createElement('style');
        style.rel = 'stylesheet';
        style.type = 'text/css';
        try {
            style.appendChild(document.createTextNode(cssText));
        } catch (e) {
            style.styleSheet.cssText = cssText; //ie9以下
        }
        var head = document.getElementsByTagName("head")[0]; //head标签之间加上style样式
        head.appendChild(style);
    }
</script>
</body>

</html>
