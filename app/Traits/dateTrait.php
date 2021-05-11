<?php
/**
 * Laravel 7+ 处理日期格式
 */
namespace App\Traits;

use DateTimeInterface;

trait dateTrait
{
    /**
     * 为数组 / JSON 序列化准备日期。
     *
     * @param \DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }
}
