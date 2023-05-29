<?php
/**
 * 辅助函数文件
 *
 */

/**
 * 授权检测
 * @param string $license_key 授权码
 * @param string $local_key 本地 KEY 信息
 * @return array|mixed|void
 */
function shy_check_license($license_key, $local_key = '')
{
    $server_url = 'https://license.shanhaiyun.com/'; // 授权服务器

    // 程序密钥 TODO:每套系统需要配置一个程序密钥
    if (strpos($license_key, 'Multi') !== false) {
        $licensing_secret_key = '5927b0ae59e11ce8245a7af98fed70d3'; // 多币系统密钥
    }
    if (strpos($license_key, 'Single') !== false) {
        $licensing_secret_key = '3c79308da67d47445d8d13dc05f7a8fe'; // 单币系统密钥
    }
    if (strpos($license_key, 'School') !== false) {
        $licensing_secret_key = 'fa526d1ad929abd67b0b0045ee4731bb'; // 校服系统密钥
    }

    $local_key_days = 30; // 本地 key 有效期
    $allow_check_fail_days = 5; // 本地 key 宽限天数
    $check_token = time() . md5(mt_rand(100000000, mt_getrandmax()) . $license_key); // 授权验证检测 token
    $check_date = date("Ymd"); // 当前检测日期
    $domain = $_SERVER['SERVER_NAME']; // 域名
    $user_ip = get_ip(); // 所在服务器 IP
    if (!$user_ip) {
        $user_ip = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR'];
    }
    $dir_path = dirname(dirname(__FILE__)); // 程序安装目录
    $verify_file_path = 'api/v1/verify'; // 授权检测接口
    $local_key_valid = false; // 本地 KEY 信息是否验证,标记为 否
    // 验证本地 KEY 信息
    if ($local_key) {
        $local_key = str_replace("\n", '', $local_key); // 删除换行符
        $local_data_license = substr($local_key, 0, strlen($local_key) - 32); // 提取许可证数据
        $md5_hash_license = substr($local_key, strlen($local_key) - 32); // 提取 MD5 Hash
        if ($md5_hash_license == md5($local_data_license . $licensing_secret_key)) {
            $local_data = strrev($local_data_license); // 反转字符串
            $md5_hash = substr($local_data, 0, 32); // 提取 MD5 Hash
            $local_data = substr($local_data, 32); // 提取许可证数据
            $local_data = base64_decode($local_data); // 解密许可证数据
            $local_key_results = json_decode($local_data, true); // 许可证详情
            $original_check_date = $local_key_results['check_date']; // 授权文件检测日期
            if ($md5_hash == md5($original_check_date . $licensing_secret_key)) {
                //  本地 KEY 有效期 = 当前本地日期减去 30 天
                $local_expiry = date("Ymd", mktime(0, 0, 0, date("m"), date("d") - $local_key_days, date("Y")));
                // 如果授权文件检测日期 > 本地 KEY 有效期
                if ($original_check_date > $local_expiry) {
                    $local_key_valid = true; // 本地 KEY 信息标记为有效
                    $results = $local_key_results;
                    // 验证授权域名
                    if ($results['allowdomain'] == 0) {
                        $valid_domains = explode(',', $results['validdomain']); // 检测授权域名
                        if (!in_array($_SERVER['SERVER_NAME'], $valid_domains)) {
                            $local_key_valid = false; // 验证失败,标记授权无效
                            $local_key_results['status'] = "Invalid";
                        }
                    }
                    // 验证授权 IP
                    if ($results['allowip'] == 0) {
                        $valid_ips = explode(',', $results['validip']); // 检测授权 IP
                        if (!in_array($user_ip, $valid_ips)) {
                            $local_key_valid = false; // 验证失败,标记授权无效
                            $local_key_results['status'] = "Invalid";
                        }
                    }
                    // 验证安装目录
                    if ($results['allowdirectory'] == 0) {
                        $valid_dirs = explode(',', $results['validdirectory']); // 检测安装目录
                        if (!in_array($dir_path, $valid_dirs)) {
                            $local_key_valid = false; // 验证失败,标记授权无效
                            $local_key_results['status'] = "Invalid";
                        }
                    }
                }
            }
        }
    }
    // 本地 KEY 信息不存在，在线获取授权信息并且保存到本地
    if (!$local_key_valid) {
        $responseCode = 0;
        $post_fields = array(
            'licensekey' => $license_key, // 授权码
            'domain' => $domain, // 使用的域名
            'ip' => $user_ip, // 服务器IP
            'dir' => $dir_path, // 安装目录
        ); // 授权表单数据
        if ($check_token) $post_fields['check_token'] = $check_token;
        $query_string = '';
        foreach ($post_fields as $k => $v) {
            $query_string .= $k . '=' . urlencode($v) . '&';
        }
        // 提交授权检测信息
        if (function_exists('curl_exec')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $server_url . $verify_file_path);
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
            $fp = @fsockopen($server_url, 80, $errno, $errstr, 5);
            if ($fp) {
                $newlinefeed = "\r\n";
                $header = "POST " . $server_url . $verify_file_path . " HTTP/1.0" . $newlinefeed;
                $header .= "Host: " . $server_url . $newlinefeed;
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
        // 处理返回的结果
        if ($responseCode != 200) {
            $local_expiry = date("Ymd", mktime(0, 0, 0, date("m"), date("d") - ($local_key_days + $allow_check_fail_days), date("Y")));
            $original_check_date = $local_key_results['check_date'];
            if ($original_check_date > $local_expiry) {
                $results = $local_key_results;
            } else {
                $results = array();
                $results['status'] = "Invalid";
                $results['description'] = "远程检测失败";
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
        // 验证 MD5
        if (isset($results['md5hash'])) {
            if ($results['md5hash'] != md5($licensing_secret_key . $check_token)) {
                $results['status'] = "Invalid";
                $results['description'] = "MD5 效验失败";
                return $results;
            }
        }
        // 返回授权信息
        if ($results['status'] == "Active") {
            $results['check_date'] = $check_date;
            $data_encoded = json_encode($results);
            $data_encoded = base64_encode($data_encoded);
            $data_encoded = md5($check_date . $licensing_secret_key) . $data_encoded;
            $data_encoded = strrev($data_encoded);
            $data_encoded = $data_encoded . md5($data_encoded . $licensing_secret_key);
            $data_encoded = wordwrap($data_encoded, 80, "\n", true);
            $results['local_key'] = $data_encoded;
        }
        $results['remote_check'] = true;
    }

    return $results;
}

/**
 * 执行授权检测
 * @return array|mixed
 */
function remote_check()
{
    $cache_name = "license_check_status"; // 缓存名称

    // 随机删除缓存 TODO

    // 从缓存中读取授权信息
//    if (Cache::has($cache_name)) {
//        $mydate_check_status = Cache::get($cache_name);
//        if ($mydate_check_status['status'] == 'Active') {
//            return $mydate_check_status;
//        }
//    }

    $license_key = config('app.license_key'); // 授权码

    if (empty($license_key)) {
        $results['description'] = "请在.env文件中配置授权码";
        echo $results['description'];
        exit();
    }

    $exists = Storage::disk('local')->exists('local_key.txt'); // 是否存在本地 KEY
    if (!$exists) {
        $results = shy_check_license($license_key); // 远程验证获取授权信息
    } else {
        $local_key = Storage::disk('local')->get('local_key.txt');
        $results = shy_check_license($license_key, $local_key); // 本地 KEY 直接本地验证
    }

    // 处理返回的授权信息
    switch ($results['status']) {
        case "Active":
            if (isset($results['local_key'])) {
                $local_key_data = $results['local_key']; // 授权信息
                Storage::disk('local')->put('local_key.txt', $local_key_data); // 存储授权信息到本地
            }
            break;
        case "Expired":
        case "Suspended":
        case "Invalid":
        default:
            $results['description'] = "检测数据无效";
            break;
    }

    if (isset($results['message'])) {
        $results['description'] = $results['message'];
    }

    if (empty($results['description'])) {
        $results['description'] = "检测数据无效";
    }

    if ($results['status'] == "Active") {
        Cache::put($cache_name, $results, 60 * 60 * 24); // 授权有效的 缓存授权信息 24小时
    }

    if ($results['status'] != "Active") {
        echo $results['description'];
        exit();
    }
   // return $results;
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
