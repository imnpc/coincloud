<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ config('app.name') }}-下载 APP</title>
  <style>
    html{
      margin: 0;
      padding: 0;
    }
    body{
      background-color: black;
    }

    .extension{
      width: 350px;
      height: 450px;
      border:2px solid #444444;
      background-color: #191919;
      margin: 0 auto;
      margin-top: 80px;
      border-radius: 15px;
      color: white;
      font-size: 14px;
    }
    .exImg{
      width: 90%;
      height: 70%;
      background-color: gray;
      margin: auto;
      margin-top: 15px;
      border: 1px solid #ffffff;
      border-radius: 7px;
      text-align: center;
    }
    .exImg img{
      margin-top: 18px;
    }
    .exInput{
      margin-top: 30px;

    }
    .exInput p{
      height: 20px;
      margin: 5px 0 0 0;
      padding: 0;
    }
    .sInput{
      background-color:gray;
      height: 37px;
      width: 63%;
      border:1px solid floralwhite;
      border-radius: 7px 0 0 7px;
      margin-left: 15px;
      margin-top: 5px;
      color: white;
    }
    .btInput{
      width: 90px;
      height: 41px;
      cursor:pointer;
      font-size: 15px;
      margin: 0;
      padding: 0;
      position: relative;
      right: 5px;
      top: 1px;
      border: 1px solid floralwhite;
      background-color: #EF334D;
      border-radius: 0 7px 7px 0;
    }


  </style>
</head>
<body>
<div class="extension">
  <div class="exImg">
    <img src="{{ $config['reg_qrcode'] }}" alt="">
  </div>
  <div class="exInput">
    <p>&nbsp;&nbsp;&nbsp;&nbsp;下载链接</p>
    <div>
      <input type="text" id="url" class="sInput" value="{{ $config['download_url'] }}">
      <input type="button"  class="btInput" value="复制链接" onClick="url.select();document.execCommand('Copy')">
      <br/><br/>
    </div>

  </div>
</div>
</body>
</html>