@extends('layouts.app')
@section('title', $user->name)

@section('content')
  <style>
    section.user_info {
      padding-bottom: 10px;
      margin-top: 20px;
      text-align: center;
    .gravatar {
      float: none;
      max-width: 70px;
    }
    h1 {
      font-size: 1.4em;
      letter-spacing: -1px;
      margin-bottom: 3px;
      margin-top: 15px;
    }

    .gravatar {
      float: left;
      max-width: 50px;
      border-radius: 50%;
    }
  </style>
  <div class="row">
    <div class="offset-md-2 col-md-8">
      <div class="col-md-12">
        <div class="offset-md-2 col-md-8">
          <section class="user_info">

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
              <img src="{{ $config['reg_qrcode'] }}" alt="{{ $user->name }}" class="gravatar"/>
            <h2>点击右上角三个点,选择 在浏览器打开并下载</h2>
          </section>
        </div>
      </div>
    </div>
  </div>
@stop
