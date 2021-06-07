@extends('layouts.app')
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

              <img src="{{ $config['reg_qrcode'] }}" class="gravatar"/>

            <h2><a href="{{ $config['download_url'] }}">APP下载</a></h2>
              <h2>点击右上角三个点,选择 在浏览器打开并下载</h2>
          </section>
        </div>
      </div>
    </div>
  </div>
@stop
