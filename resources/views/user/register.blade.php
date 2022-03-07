<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}-邀请码注册</title>
    <style>
        html {
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #305095;
        }

        .DW {
        }

        .logon {
            width: 350px;
            height: 450px;
            border: 2px solid #fff;
            background-color: #305095;
            margin: 0 auto;
            margin-top: 80px;
            border-radius: 15px;
            color: white;
            font-size: 14px;

        }

        .logon p {
            height: 20px;
            margin: 5px 0 0 0;
            padding: 0;
        }

        .logon img {
            position: relative;
            top: 15px;
            left: 5px;
        }

        .bInput {
            background-color: #fff;
            height: 37px;
            width: 90%;
            border: 1px solid floralwhite;
            border-radius: 7px;
            margin-left: 15px;
            margin-top: 5px;
        }

        .sInput {
            background-color: #fff;
            height: 37px;
            width: 55%;
            border: 1px solid floralwhite;
            border-radius: 7px;
            margin-left: 15px;
            margin-top: 5px;
        }

        .btInput {
            width: 110px;
            height: 40px;
            background-color: white;
            border-radius: 7px;
            margin-left: 5px;
            cursor: pointer;
            font-size: 15px;

        }

        .logonB {
            width: 350px;
            height: 80px;
            text-align: center;
            margin: 0 auto;
        }

        .LBut {
            width: 160px;
            height: 40px;
            background-color: #305095;
            color: floralwhite;
            border: 1px solid floralwhite;
            border-radius: 7px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="DW">
    <form method="POST" action="{{ route('userregister') }}">
        @csrf
    <div class="logon">
        <div>
            <p>&nbsp;&nbsp;&nbsp;&nbsp;手机号</p>
            <input type="text" class="bInput" id="phone" name="phone" value="{{ old('phone') }}"
                   required placeholder="请输入手机号码"/>
        </div>
        <div>
            <p>&nbsp;&nbsp;&nbsp;&nbsp;图形验证码</p>
            <input id="captcha" class="sInput"
                   name="captcha" placeholder="请输入图形验证码">
            <img src="{{ captcha_src('math') }}"
                 onclick="this.src='/captcha/math?'+Math.random()" title="点击图片重新获取验证码">
        </div>
        <div>
            <p>&nbsp;&nbsp;&nbsp;&nbsp;短信验证码</p>

            <input type="text" class="sInput" id="verify_code" name="verify_code"
                   value="{{ old('verify_code') }}"
                   placeholder="请输入短信验证码">
            <input type="button" class="btInput" id="getVerifyCode" value="发送验证码">
        </div>
        <div>
            <p>&nbsp;&nbsp;&nbsp;&nbsp;密码</p>
            <input id="password" type="password" class="bInput" name="password" required autocomplete="new-password">
        </div>
        <div>
            <p>&nbsp;&nbsp;&nbsp;&nbsp;再次输入密码</p>
            <input id="password-confirm" type="password" class="bInput" name="password_confirmation"
                   required autocomplete="new-password">
        </div>
        <div>
            <p>&nbsp;&nbsp;&nbsp;&nbsp;邀请码</p>

            <input id="parent_id" type="text" class="bInput" name="parent_id"
                   value="{{ $parent_id ?? '' }}" required placeholder="请填写邀请码,没有邀请码无法注册">
        </div>
    </div>
    <div class="logonB">
        <button class="LBut">注册</button>
    </div>
    </form>
</div>
</body>
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script>
    var timeCount = 60;

    function setTime(obj){
        if(timeCount === 0){
            obj.attr('disabled',false);
            obj.val('发送验证码');
            timeCount = 60;
            return;
        }else{
            obj.attr('disabled',true);
            obj.val(`重新发送(${timeCount}s)`);
            timeCount --;
        }
        setTimeout(function(){
            setTime(obj);
        }, 1000);
    }

    $('#getVerifyCode').click(function(e){
        e.preventDefault();
        var mobile = parseInt($('input[name="phone"]').val());
        var captcha = parseInt($('input[name="captcha"]').val());
        if(!mobile){
            alert('请输入手机号码.');
            return;
        }
        if(!captcha){
            alert('请先输入图形验证码.');
            return;
        }
        var url = "{{ route('sendcode') }}";
        getVerifyCode(mobile, captcha, url);
        setTime($(this));
    });

    function getVerifyCode(mobile, captcha, url) {
        $.ajax({
            url: url,
            method: 'post',
            data: {phone: mobile,captcha:captcha},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data){
                //alert('短信发送成功');
                console.log(data);
                alert(data.message);
            },

            error: function(xhr, status, error) {
                //console.log(xhr);
                var json=JSON.parse(xhr.responseText);
                alert(json.message);
            }
        })
    }
</script>
</html>
