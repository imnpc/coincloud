<?php
/**
 * 辅助函数文件
 *
 */

use App\Gateways\QxtGateway;
use EasyExchange\Factory;
use Illuminate\Support\Facades\Http;
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
function upload_images($file, $type, $user_id, $disk = "public")
{
//    $check = remote_check();
//    if (($check['status'] != "Active") && mt_rand() % 2 === 0) {
//        echo $check['description'];
//        exit();
//    }
    if (config('filesystems.default') != 'public') {
        $disk = config('filesystems.default');
    }

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
 * @param bool $format
 * @return string|\Torann\Currency\Currency|void
 */
function huobiusdt($from, $format = true)
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
        if ($format) {
            return currency($usdt, 'USD', 'CNY');
        } else {
            return currency($usdt, 'USD', 'CNY', false);
        }
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

/**
 * ZTB 数据接口
 * https://www.ztb.im（海外域名）
 * https://www.ztbzh.net（大陆域名）
 * @param $from
 * @return string|\Torann\Currency\Currency
 */
function ztbusdt($from)
{
    $symbol = strtoupper($from).'_USDT';
    $base_url = 'https://www.ztbzh.net';
    $path = '/api/v1/tickers';
    if (Cache::has('ztb')) {
        $response = Cache::get('ztb'); // get from cahche 有缓存从缓存读取数据
    } else {
        $response = Http::get($base_url.$path);
        Cache::put('ztb', $response->json(), 3600); // 默认缓存 1 小时
    }
    $data = $response['ticker'];

    $key = array_search($symbol, array_column($data, 'symbol'));
    if ($key) {
        $coin = $data[$key];
        $usdt = $coin['last'];
        return currency($usdt, 'USD', 'CNY');
    } else {
        return '敬请期待';
    }
}

///**
// * 授权检测
// * @param string $license_key 授权码
// * @param string $local_key 本地 KEY 信息
// * @return array|mixed|void
// */
//function shy_check_license($license_key, $local_key = '')
//{
//    $server_url = 'https://license.shanhaiyun.com/'; // 授权服务器
//    $licensing_secret_key = '5927b0ae59e11ce8245a7af98fed70d3'; // 多币系统密钥
//    if (strpos($license_key, 'Single') !== false) {
//        $licensing_secret_key = '3c79308da67d47445d8d13dc05f7a8fe'; // 单币系统密钥
//    }
//    if (strpos($license_key, 'School') !== false) {
//        $licensing_secret_key = 'fa526d1ad929abd67b0b0045ee4731bb'; // 校服商城系统 系统密钥 TODO
//    }
//    $local_key_days = 30; // 本地 key 有效期
//    $allow_check_fail_days = 5; // 本地 key 宽限天数
//    $check_token = time() . md5(mt_rand(100000000, mt_getrandmax()) . $license_key); // 授权验证检测 token
//    $check_date = date("Ymd"); // 当前检测日期
//    $domain = $_SERVER['SERVER_NAME']; // 域名
//    $user_ip = get_ip(); // 所在服务器 IP
//    if (!$user_ip) {
//        $user_ip = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR'];
//    }
//    $dir_path = dirname(dirname(__FILE__)); // 程序安装目录
//    $verify_file_path = 'api/v1/verify'; // 授权检测接口
//    $local_key_valid = false; // 本地 KEY 信息是否验证,标记为 否
//    // 验证本地 KEY 信息
//    if ($local_key) {
//        $local_key = str_replace("\n", '', $local_key); // 删除换行符
//        $local_data_license = substr($local_key, 0, strlen($local_key) - 32); // 提取许可证数据
//        $md5_hash_license = substr($local_key, strlen($local_key) - 32); // 提取 MD5 Hash
//        if ($md5_hash_license == md5($local_data_license . $licensing_secret_key)) {
//            $local_data = strrev($local_data_license); // 反转字符串
//            $md5_hash = substr($local_data, 0, 32); // 提取 MD5 Hash
//            $local_data = substr($local_data, 32); // 提取许可证数据
//            $local_data = base64_decode($local_data); // 解密许可证数据
//            $local_key_results = json_decode($local_data, true); // 许可证详情
//            $original_check_date = $local_key_results['check_date']; // 授权文件检测日期
//            if ($md5_hash == md5($original_check_date . $licensing_secret_key)) {
//                //  本地 KEY 有效期 = 当前本地日期减去 30 天
//                $local_expiry = date("Ymd", mktime(0, 0, 0, date("m"), date("d") - $local_key_days, date("Y")));
//                // 如果授权文件检测日期 > 本地 KEY 有效期
//                if ($original_check_date > $local_expiry) {
//                    $local_key_valid = true; // 本地 KEY 信息标记为有效
//                    $results = $local_key_results;
//                    // 验证授权域名
//                    if ($results['allowdomain'] == 0) {
//                        $valid_domains = explode(',', $results['validdomain']); // 检测授权域名
//                        if (!in_array($_SERVER['SERVER_NAME'], $valid_domains)) {
//                            $local_key_valid = false; // 验证失败,标记授权无效
//                            $local_key_results['status'] = "Invalid";
//                        }
//                    }
//                    // 验证授权 IP
//                    if ($results['allowip'] == 0) {
//                        $valid_ips = explode(',', $results['validip']); // 检测授权 IP
//                        if (!in_array($user_ip, $valid_ips)) {
//                            $local_key_valid = false; // 验证失败,标记授权无效
//                            $local_key_results['status'] = "Invalid";
//                        }
//                    }
//                    // 验证安装目录
//                    if ($results['allowdirectory'] == 0) {
//                        $valid_dirs = explode(',', $results['validdirectory']); // 检测安装目录
//                        if (!in_array($dir_path, $valid_dirs)) {
//                            $local_key_valid = false; // 验证失败,标记授权无效
//                            $local_key_results['status'] = "Invalid";
//                        }
//                    }
//                }
//            }
//        }
//    }
//    // 本地 KEY 信息不存在，在线获取授权信息并且保存到本地
//    if (!$local_key_valid) {
//        $responseCode = 0;
//        $post_fields = array(
//            'licensekey' => $license_key, // 授权码
//            'domain' => $domain, // 使用的域名
//            'ip' => $user_ip, // 服务器IP
//            'dir' => $dir_path, // 安装目录
//        ); // 授权表单数据
//        if ($check_token) $post_fields['check_token'] = $check_token;
//        $query_string = '';
//        foreach ($post_fields as $k => $v) {
//            $query_string .= $k . '=' . urlencode($v) . '&';
//        }
//        // 提交授权检测信息
//        if (function_exists('curl_exec')) {
//            $ch = curl_init();
//            curl_setopt($ch, CURLOPT_URL, $server_url . $verify_file_path);
//            curl_setopt($ch, CURLOPT_POST, 1);
//            curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
//            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
//            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
//            $data = curl_exec($ch);
//            $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//            curl_close($ch);
//        } else {
//            $responseCodePattern = '/^HTTP\/\d+\.\d+\s+(\d+)/';
//            $fp = @fsockopen($server_url, 80, $errno, $errstr, 5);
//            if ($fp) {
//                $newlinefeed = "\r\n";
//                $header = "POST " . $server_url . $verify_file_path . " HTTP/1.0" . $newlinefeed;
//                $header .= "Host: " . $server_url . $newlinefeed;
//                $header .= "Content-type: application/x-www-form-urlencoded" . $newlinefeed;
//                $header .= "Content-length: " . @strlen($query_string) . $newlinefeed;
//                $header .= "Connection: close" . $newlinefeed . $newlinefeed;
//                $header .= $query_string;
//                $data = $line = '';
//                @stream_set_timeout($fp, 20);
//                @fputs($fp, $header);
//                $status = @socket_get_status($fp);
//                while (!@feof($fp) && $status) {
//                    $line = @fgets($fp, 1024);
//                    $patternMatches = array();
//                    if (!$responseCode
//                        && preg_match($responseCodePattern, trim($line), $patternMatches)
//                    ) {
//                        $responseCode = (empty($patternMatches[1])) ? 0 : $patternMatches[1];
//                    }
//                    $data .= $line;
//                    $status = @socket_get_status($fp);
//                }
//                @fclose($fp);
//            }
//        }
//        // 处理返回的结果
//        if ($responseCode != 200) {
//            $local_expiry = date("Ymd", mktime(0, 0, 0, date("m"), date("d") - ($local_key_days + $allow_check_fail_days), date("Y")));
//            $original_check_date = $local_key_results['check_date'];
//            if ($original_check_date > $local_expiry) {
//                $results = $local_key_results;
//            } else {
//                $results = array();
//                $results['status'] = "Invalid";
//                $results['description'] = "远程检测失败";
//                return $results;
//            }
//        } else {
//            preg_match_all('/<(.*?)>([^<]+)<\/\\1>/i', $data, $matches);
//            $results = array();
//            foreach ($matches[1] as $k => $v) {
//                $results[$v] = $matches[2][$k];
//            }
//        }
//        if (!is_array($results)) {
//            die("服务器响应无效");
//        }
//        // 验证 MD5
//        if (isset($results['md5hash'])) {
//            if ($results['md5hash'] != md5($licensing_secret_key . $check_token)) {
//                $results['status'] = "Invalid";
//                $results['description'] = "MD5 效验失败";
//                return $results;
//            }
//        }
//        // 返回授权信息
//        if ($results['status'] == "Active") {
//            $results['check_date'] = $check_date;
//            $data_encoded = json_encode($results);
//            $data_encoded = base64_encode($data_encoded);
//            $data_encoded = md5($check_date . $licensing_secret_key) . $data_encoded;
//            $data_encoded = strrev($data_encoded);
//            $data_encoded = $data_encoded . md5($data_encoded . $licensing_secret_key);
//            $data_encoded = wordwrap($data_encoded, 80, "\n", true);
//            $results['local_key'] = $data_encoded;
//        }
//        $results['remote_check'] = true;
//    }
//
//    return $results;
//}
//
///**
// * 执行授权检测
// * 授权码配置
// * 1 .ENV 文件增加一行 ： LICENSE_KEY=
// * 2. config/app.php 添加一行：  'license_key' => env('LICENSE_KEY'),
// * @return array|mixed
// */
//function remote_check()
//{
//    $cache_name = "license_check_status"; // 缓存名称
//
//    // 从缓存中读取授权信息
//    if (Cache::has($cache_name)) {
//        $mydate_check_status = Cache::get($cache_name);
//        if ($mydate_check_status['status'] == 'Active') {
//            return $mydate_check_status;
//        }
//    }
//
//    $license_key = config('app.license_key'); // 授权码
//    $exists = Storage::disk('local')->exists('local_key.txt'); // 是否存在本地 KEY
//    if (!$exists) {
//        $results = shy_check_license($license_key); // 远程验证获取授权信息
//    } else {
//        $local_key = Storage::disk('local')->get('local_key.txt');
//        $results = shy_check_license($license_key, $local_key); // 本地 KEY 直接本地验证
//    }
//
//    // 处理返回的授权信息
//    switch ($results['status']) {
//        case "Active":
//            if (isset($results['local_key'])) {
//                $local_key_data = $results['local_key']; // 授权信息
//                Storage::disk('local')->put('local_key.txt', $local_key_data); // 存储授权信息到本地
//            }
//            break;
//        case "Expired":
//        case "Suspended":
//        case "Invalid":
//        default:
//            $results['description'] = "检测数据无效";
//            break;
//    }
//
//    if (empty($license_key)) {
//        $results['description'] = "请填写授权码";
//    }
//
//    if (isset($results['message'])) {
//        $results['description'] = $results['message'];
//    }
//
//    if (empty($results['description'])) {
//        $results['description'] = "检测数据无效";
//    }
//
//    if ($results['status'] == "Active") {
//        Cache::put($cache_name, $results, 60 * 60 * 24); // 授权有效的 缓存授权信息 24小时
//    }
//
//    return $results;
//}
//
///**
// * 获取客户端IP(非用户服务器IP)
// * @return string
// */
//function get_ip(): string
//{
//    $ip = 'members.3322.org/dyndns/getip';
//    $ch = curl_init();
//    curl_setopt($ch, CURLOPT_URL, $ip);
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//    $data = curl_exec($ch);
//    return trim($data);
//}

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

/**
 * @param $num         科学计数法字符串  如 2.1E-5
 * @param  int  $double  小数点保留位数 默认5位
 * @return string
 */
function sctonum($num, $double = 5)
{
    if (false !== stripos($num, "e")) {
        $a = explode("e", strtolower($num));
        return bcmul($a[0], bcpow(10, $a[1], $double), $double);
    }

    return $num;
}

/**
 * 生成唯一订单号
 * @param  string  $model  模型名称,首字母大写
 * @param  string  $field  订单号查询字段
 * @return bool|string
 */
function createNO($model, $field)
{
    // 订单流水号前缀
    $prefix = date('YmdHis');
    for ($i = 0; $i < 10; $i++) {
        // 随机生成 6 位的数字
        $sn = $prefix.str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        // 查询该模型是否已经存在对应订单号
        $modelName = '\\App\\Models\\'.$model;
        $MODEL = new $modelName;
        if (!$MODEL::query()->where($field, $sn)->exists()) {
            return $sn;
        }
    }
    \Log::warning('生成单号失败-'.$modelName);

    return false;
}

/**
 * 判断是否都是中文
 * @param $str
 * @return int
 */
function isAllChinese($str)
{
    $len = preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $str);
    if ($len) {
        return true;
    }
    return false;
}

/**
 * 格式化数字
 */
function float_number($number)
{
    $length = strlen($number);  //数字长度
    if ($length > 8) { //亿单位
        $str = substr_replace(floor($number * 0.0000001), '.', -1, 0)."亿";
    } elseif ($length > 4) { //万单位
        //截取前俩为
        $str = floor($number * 0.001) * 0.1 ."万";
    } else {
        return $number;
    }
    return $str;
}

/**
 * 二维数组根据某个字段排序
 * @param array $array 要排序的数组
 * @param string $keys 要排序的键字段
 * @param string $sort 排序类型  SORT_ASC     SORT_DESC
 * @return array 排序后的数组
 */
function arraySort($array, $keys, $sort = SORT_DESC)
{
    $keysValue = [];
    foreach ($array as $k => $v) {
        $keysValue[$k] = $v[$keys];
    }
    array_multisort($keysValue, $sort, $array);
    return $array;
}

/**
 * 二分查找法
 * @param $num 数量
 * @param $filter 对应集合
 * @return array
 */
function priceSearch($num, $filter)
{
    if (count($filter) == 1) {
        return $filter;
    }
    $half = floor(count($filter) / 2); // 取出中间数

    // 判断数量在哪个区间
    if ($num < $filter[$half]['number']) {
        $filter = array_slice($filter, 0, $half);
    } else {
        $filter = array_slice($filter, $half, count($filter));
    }
    //print_r($filter);
    // 继续递归直到只剩一个元素
    if (count($filter) > 1) {
        $filter = priceSearch($num, $filter);
    }

    return $filter;
}

// 随机返回所需范围数字
function randNumber(): int
{
    return rand(1, 10);
}
