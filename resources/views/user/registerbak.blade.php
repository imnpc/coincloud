@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header">{{ __('Register') }}-邀请码注册</div>

          <div class="card-body">

            @if (count($errors) > 0)
              <div class="alert alert-danger">
                <ul>
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif
            @foreach (['danger', 'warning', 'success', 'info'] as $msg)
              @if(session()->has($msg))
                <div class="flash-message">
                  <p class="alert alert-{{ $msg }}">
                    {{ session()->get($msg) }}
                  </p>
                </div>
              @endif
            @endforeach

              @if (empty($parent_id))
                <div class="alert alert-danger">
                  <ul>
                    <li>邀请码不正确,没有邀请码无法注册</li>
                  </ul>
                </div>
              @else

                <form method="POST" action="{{ route('userregister') }}">
                  @csrf

                  <div class="form-group row">
                    <label for="mobile" class="col-md-4 col-form-label text-md-right">手机号码</label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}"
                             required placeholder="请输入手机号码"/>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label for="captcha" class="col-md-4 col-form-label text-md-right">图形验证码</label>

                    <div class="col-md-6">
                      <input id="captcha" class="form-control{{ $errors->has('captcha') ? ' is-invalid' : '' }}"
                             name="captcha" placeholder="请输入图形验证码">
                      <img class="thumbnail mt-3 mb-2" src="{{ captcha_src('math') }}"
                           onclick="this.src='/captcha/math?'+Math.random()" title="点击图片重新获取验证码">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label for="verify_code" class="col-md-4 col-form-label text-md-right">短信验证码</label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" id="verify_code" name="verify_code"
                             value="{{ old('verify_code') }}"
                             placeholder="请输入短信验证码">
                      <input type="button" class="btn btn-outline-primary btn-sm mt-2" id="getVerifyCode"
                             value="点击获取短信验证码">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label for="password" class="col-md-4 col-form-label text-md-right">密码</label>

                    <div class="col-md-6">
                      <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                             name="password" required autocomplete="new-password">

                      @error('password')
                      <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                      @enderror
                    </div>
                  </div>

                  <div class="form-group row">
                    <label for="password-confirm" class="col-md-4 col-form-label text-md-right">重复密码</label>

                    <div class="col-md-6">
                      <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
                             required autocomplete="new-password">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label for="parent_id" class="col-md-4 col-form-label text-md-right">邀请码</label>

                    <div class="col-md-6">
                      <input id="parent_id" type="text" class="form-control" name="parent_id"
                             value="{{ $parent_id ?? '' }}" required placeholder="请填写邀请码,没有邀请码无法注册">
                    </div>
                  </div>

                  <div class="form-group row mb-0">
                    <div class="col-md-6 offset-md-4">
                      <button type="submit" class="btn btn-primary">
                        {{ __('注册') }}
                      </button>
                    </div>
                  </div>
                </form>
              @endif

          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset('js/jquery.min.js') }}"></script>
  <script>
    var timeCount = 60;

    function setTime(obj){
      if(timeCount === 0){
        obj.attr('disabled',false);
        obj.val('点击获取短信验证码');
        timeCount = 60;
        return;
      }else{
        obj.attr('disabled',true);
        obj.val(`重新获取短信验证码(${timeCount}s)`);
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

@endsection
