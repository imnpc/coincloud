@extends('layouts.app')
@section('title', '错误')

@section('content')
  <div class="card">
    <div class="card-header">错误</div>
    <div class="card-body text-center">
      <h1>{{ $msg }}</h1>
      <a class="btn btn-primary" href="{{ url()->previous() }}">返回上一页</a>
    </div>
  </div>
@endsection
