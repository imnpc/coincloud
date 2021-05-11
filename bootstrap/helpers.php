<?php
/**
 * 辅助函数文件
 *
 */

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

