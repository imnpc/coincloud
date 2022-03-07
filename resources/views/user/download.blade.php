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
      background-color: #305095;
    }

    .extension{
      width: 350px;
      height: 450px;
      border:2px solid #fff;
      background-color: #305095;
      margin: 0 auto;
      margin-top: 80px;
      border-radius: 15px;
      color: white;
      font-size: 14px;
    }
    .exImg{
      width: 90%;
      height: 70%;
      background-color: #305095;
      margin: auto;
      margin-top: 15px;
      border: 1px solid #ffffff;
      border-radius: 7px;
      text-align: center;
    }
    .exImg img{
            margin-top: 1px;
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
      background-color:#305095;
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
      background-color: #305095;
      border-radius: 0 7px 7px 0;
      color: #fff;
    }

        .notice {
            font-size: 18px;
            color: red;
        }

  </style>
</head>
<body>
<div class="extension">
    <div class="exImg">
        <img src="{{ $config['reg_qrcode'] }}" alt="" width="100%">
    </div>
    <div class="exInput">
        <p>&nbsp;&nbsp;&nbsp;&nbsp;下载链接</p>
        <div>
            <input type="text" id="url" class="sInput" value="{{ $config['download_url'] }}">
            <input type="button" class="btInput" value="复制链接" onClick="url.select();document.execCommand('Copy')">
            <br/><br/>
            <h4 class="notice">请长按识别以上二维码 或者 点击 复制链接，然后 使用浏览器 打开链接</h4>
        </div>

  </div>
</div>
</body>
</html>