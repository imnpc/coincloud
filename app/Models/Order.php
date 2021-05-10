<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_sn', 'user_id', 'product_id', 'number', 'wallet_type_id', 'pay_money', 'wait_days', 'wait_status', 'valid_days',
        'valid_rate', 'valid_power', 'max_valid_power', 'package_rate', 'package_already', 'package_wait', 'package_status', 'pay_status',
        'pay_image', 'pay_time', 'confirm_time', 'is_output_coin', 'status', 'remark',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'pay_time' => 'datetime',
        'confirm_time' => 'datetime',
    ];

    // 关联 用户
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 关联 产品
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // 关联 钱包类型
    public function wallettype()
    {
        return $this->belongsTo(WalletType::class);
    }

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
