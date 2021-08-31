<?php
/**
 * 辅助函数文件
 *
 */

use App\Gateways\QxtGateway;
use EasyExchange\Factory;
use Lin\Mxc\MxcSpot;
use Overtrue\EasySms\EasySms;

/**
 * 上传图片
 * @param $file 文件
 * @param $type 类型 avatar,passport,cert,edu,course,banner,other
 * @param $user_id 用户 id
 * @param string $disk 磁盘名称
 * @return \App\Http\Resources\ImageResource
 */
function upload_images($file, $type, $user_id, $disk = "oss")
{
//    $check = remote_check();
//    if (($check['status'] != "Active") && mt_rand() % 2 === 0) {
//        echo $check['description'];
//        exit();
//    }
    $path = Storage::disk($disk)->putFile($type . '/' . date('Y/m/d'), $file);
    $image = new App\Models\Image();
    $image->type = $type; //上传类型 参见 ImageRequest
    $image->path = $path;// URL 路径
    $image->disk = $disk; //上传磁盘
    $image->size = $file->getSize();// 获取文件大小
    $image->size_kb = number_fixed($image->size / 1024, 2);// 获取文件大小 k
    $image->user_id = $user_id;
    $image->save();

    return new App\Http\Resources\ImageResource($image);
}

/**
 *  允许上传图像类型
 * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed|string
 */
function image_ext()
{
    if (config('upload.image_ext')) {
        $ext = config('upload.image_ext');
    } else {
        $ext = "gif,bmp,jpeg,png"; // 默认上传图像类型
    }

    return $ext;
}

/**
 * 隐藏银行卡号
 * @param $number
 * @param string $maskingCharacter
 * @return string
 */
function addMaskCC($number, $maskingCharacter = '*')
{
    return substr($number, 0, 4) . str_repeat($maskingCharacter, strlen($number) - 8) . substr($number, -4);
}

/**
 * 保留几位小数 默认 5
 * @param float $number 数字
 * @param int $precision 保留位数
 * @return float|int
 */
function number_fixed($num, $precision = 5)
{
    return intval($num * pow(10, $precision)) / pow(10, $precision);
}

/**
 * 获取数组内的 id
 * @param array $data
 * @param string $key
 * @return array
 */
function get_array_ids(array $data, string $key = 'id'): array
{
    $ids = [];
    foreach ($data as $item) {
        $id = $item[$key] ?? false;
        if ($id === false) {
            continue;
        }
        $ids[$id] = 0;
    }
    return array_keys($ids);
}

/**
 * 火币数据接口
 * @param $from
 * @return string|\Torann\Currency\Currency|void
 */
function huobiusdt($from)
{
    // 配置
    $huobi = [
        'response_type' => config('huobi.response_type'),
        'base_uri' => config('huobi.base_uri'),
        'app_key' => config('huobi.app_key'),
        'secret' => config('huobi.secret'),
    ];

    $app = Factory::huobi($huobi);
    $symbol = $from . 'usdt';
    $hr24 = $app->market->hr24($symbol);

    if ($hr24['status'] == 'ok') {
        $usdt = $hr24['tick']['close'];
        return currency($usdt, 'USD', 'CNY');
    } elseif ($hr24['status'] == 'error') {
        return '暂无数据';
    }
}

/**
 *  抹茶数据接口
 * @param $from
 * @return string|\Torann\Currency\Currency|void
 */
function mxcusdt($from)
{
    $symbol = strtolower($from) . '_usdt';
    $exchanges = new MxcSpot('', '', 'https://www.mexc.com');
    $result = $exchanges->market()->getTicker([
        'symbol' => $symbol
    ]);

    if ($result['code'] == '200') {
        $usdt = $result['data'][0]['last'];
        return currency($usdt, 'USD', 'CNY');
    } else {
        return '敬请期待';
    }
}

function shy_check_license($licensekey, $localkey = '')
{
    $whmcsurl = 'https://license.shanhaiyun.com/';
    $licensing_secret_key = '5927b0ae59e11ce8245a7af98fed70d3';
    if (strpos($licensekey, 'Single') !== false) {
        $licensing_secret_key = '3c79308da67d47445d8d13dc05f7a8fe';
    }
    $localkeydays = 30;
    $allowcheckfaildays = 5;
    $check_token = time() . md5(mt_rand(100000000, mt_getrandmax()) . $licensekey);
    $checkdate = date("Ymd");
    $domain = $_SERVER['SERVER_NAME'];
    $usersip = get_ip();
    if (!$usersip) {
        $usersip = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR'];
    }
    $dirpath = dirname(dirname(__FILE__));
    $verifyfilepath = 'api/v1/verify';
    $localkeyvalid = false;
    if ($localkey) {
        $localkey = str_replace("\n", '', $localkey); # Remove the line breaks
        $localdata = substr($localkey, 0, strlen($localkey) - 32); # Extract License Data
        $md5hash = substr($localkey, strlen($localkey) - 32); # Extract MD5 Hash
        if ($md5hash == md5($localdata . $licensing_secret_key)) {
            $localdata = strrev($localdata); # Reverse the string
            $md5hash = substr($localdata, 0, 32); # Extract MD5 Hash
            $localdata = substr($localdata, 32); # Extract License Data
            $localdata = base64_decode($localdata);
            $localkeyresults = json_decode($localdata, true);
            $originalcheckdate = $localkeyresults['checkdate'];
            if ($md5hash == md5($originalcheckdate . $licensing_secret_key)) {
                $localexpiry = date("Ymd", mktime(0, 0, 0, date("m"), date("d") - $localkeydays, date("Y")));
                if ($originalcheckdate > $localexpiry) {
                    $localkeyvalid = true;
                    $results = $localkeyresults;
                    if ($results['allowdomain'] == 0) {
                        $validdomains = explode(',', $results['validdomain']);
                        if (!in_array($_SERVER['SERVER_NAME'], $validdomains)) {
                            $localkeyvalid = false;
                            $localkeyresults['status'] = "Invalid";
                        }
                    }
                    if ($results['allowip'] == 0) {
                        $validips = explode(',', $results['validip']);
                        if (!in_array($usersip, $validips)) {
                            $localkeyvalid = false;
                            $localkeyresults['status'] = "Invalid";
                        }
                    }
                    if ($results['allowdirectory'] == 0) {
                        $validdirs = explode(',', $results['validdirectory']);
                        if (!in_array($dirpath, $validdirs)) {
                            $localkeyvalid = false;
                            $localkeyresults['status'] = "Invalid";
                        }
                    }
                }
                if ($originalcheckdate - $checkdate > ($localkeydays + $allowcheckfaildays)) {
                    $localkeyvalid = false;
                }
            }
        }
    }
    if (!$localkeyvalid) {
        $responseCode = 0;
        $postfields = array(
            'licensekey' => $licensekey,
            'domain' => $domain,
            'ip' => $usersip,
            'dir' => $dirpath,
        );
        if ($check_token) $postfields['check_token'] = $check_token;
        $query_string = '';
        foreach ($postfields as $k => $v) {
            $query_string .= $k . '=' . urlencode($v) . '&';
        }
        if (function_exists('curl_exec')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $whmcsurl . $verifyfilepath);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            $data = curl_exec($ch);
            $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
        } else {
            $responseCodePattern = '/^HTTP\/\d+\.\d+\s+(\d+)/';
            $fp = @fsockopen($whmcsurl, 80, $errno, $errstr, 5);
            if ($fp) {
                $newlinefeed = "\r\n";
                $header = "POST " . $whmcsurl . $verifyfilepath . " HTTP/1.0" . $newlinefeed;
                $header .= "Host: " . $whmcsurl . $newlinefeed;
                $header .= "Content-type: application/x-www-form-urlencoded" . $newlinefeed;
                $header .= "Content-length: " . @strlen($query_string) . $newlinefeed;
                $header .= "Connection: close" . $newlinefeed . $newlinefeed;
                $header .= $query_string;
                $data = $line = '';
                @stream_set_timeout($fp, 20);
                @fputs($fp, $header);
                $status = @socket_get_status($fp);
                while (!@feof($fp) && $status) {
                    $line = @fgets($fp, 1024);
                    $patternMatches = array();
                    if (!$responseCode
                        && preg_match($responseCodePattern, trim($line), $patternMatches)
                    ) {
                        $responseCode = (empty($patternMatches[1])) ? 0 : $patternMatches[1];
                    }
                    $data .= $line;
                    $status = @socket_get_status($fp);
                }
                @fclose($fp);
            }
        }
        if ($responseCode != 200) {
            $localexpiry = date("Ymd", mktime(0, 0, 0, date("m"), date("d") - ($localkeydays + $allowcheckfaildays), date("Y")));
            if ($originalcheckdate > $localexpiry) {
                $results = $localkeyresults;
                $results['description'] = "检测失败";
            } else {
                $results = array();
                $results['status'] = "Invalid";
                $results['description'] = "检测失败";
                return $results;
            }
        } else {
            preg_match_all('/<(.*?)>([^<]+)<\/\\1>/i', $data, $matches);
            $results = array();
            foreach ($matches[1] as $k => $v) {
                $results[$v] = $matches[2][$k];
            }
        }
        if (!is_array($results)) {
            die("服务器响应无效");
        }

        if (isset($results['md5hash'])) {
            if ($results['md5hash'] != md5($licensing_secret_key . $check_token)) {
                $results['status'] = "Invalid";
                $results['description'] = "MD5 效验失败";
                return $results;
            }
        }
        if ($results['status'] == "Active") {
            $results['checkdate'] = $checkdate;
            $data_encoded = json_encode($results);
            $data_encoded = base64_encode($data_encoded);
            $data_encoded = md5($checkdate . $licensing_secret_key) . $data_encoded;
            $data_encoded = strrev($data_encoded);
            $data_encoded = $data_encoded . md5($data_encoded . $licensing_secret_key);
            $data_encoded = wordwrap($data_encoded, 80, "\n", true);
            $results['localkey'] = $data_encoded;
        }
        $results['remotecheck'] = true;
    }

    return $results;
}

function remote_check()
{
    $licensekey = config('app.license_key');
    $exists = Storage::disk('local')->exists('localkey.txt');
    if (!$exists) {
        $results = shy_check_license($licensekey);
    } else {
        $localkey = Storage::disk('local')->get('localkey.txt');
        $results = shy_check_license($licensekey, $localkey);
    }
    $results['message'] = "";
    switch ($results['status']) {
        case "Active":
            if (isset($results['localkey'])) {
                $localkeydata = $results['localkey'];
                Storage::disk('local')->put('localkey.txt', $localkeydata);
            }
            break;
        case "Expired":
        case "Suspended":
        case "Invalid":
            break;
        default:
            $results['description'] = "检测数据无效";
            break;
    }

    if (empty($licensekey)) {
        $results['description'] = "请填写授权码";
    }

    if (empty($results['description'])) {
        $results['description'] = "检测数据无效";
    }

    if (isset($results['description'])) {
        $results['message'] = $results['description'];
    }

    return $results;
}

/**
 * 获取客户端IP(非用户服务器IP)
 * @return string
 */
function get_ip(): string
{
    $ip = 'members.3322.org/dyndns/getip';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ip);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($ch);
    return trim($data);
}

/**
 * 发送短信
 * @param string $mobile 手机号
 * @param int $code 验证码
 * @throws \Overtrue\EasySms\Exceptions\InvalidArgumentException
 * @throws \Overtrue\EasySms\Exceptions\NoGatewayAvailableException
 */
function send_sms($mobile, $code): array
{
    $sign = config('easysms.sms_sign_name');
    $easySms = new EasySms(config('easysms'));
    // 注册
    $easySms->extend('qxt', function ($gatewayConfig) {
        // $gatewayConfig 来自配置文件里的 `gateways.mygateway`
        return new QxtGateway($gatewayConfig);
    });
    $text = '【' . $sign . '】您的验证码是：' . $code . '。请不要把验证码泄露给其他人。';
    $result = $easySms->send($mobile, $text);

    return $result;
}