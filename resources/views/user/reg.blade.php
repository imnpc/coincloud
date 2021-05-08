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

                <form method="POST" action="{{ route('regstore') }}">
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

                  <div class="form-group row">
                    <label for="safe_code" class="col-md-4 col-form-label text-md-right">安全码</label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" id="safe_code" name="safe_code" value="{{ old('safe_code') }}"
                             required placeholder="请输入安全码"/>
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

          </div>
        </div>
      </div>
    </div>
  </div>

@endsection
