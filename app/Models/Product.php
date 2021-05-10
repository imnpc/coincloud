<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Storage;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'tag', 'price', 'price_usdt', 'price_coin', 'coin_wallet_address', 'coin_wallet_qrcode', 'wallet_type_id',
        'wait_days', 'valid_days', 'valid_days_text', 'choose_reason', 'choose_reason_text', 'service_rate', 'day_customer_rate',
        'day_rate', 'freed_rate', 'parent1', 'parent2', 'invite_rate', 'bonus_team_a', 'bonus_team_b', 'bonus_team_c', 'upgrade_team_a',
        'upgrade_team_b', 'upgrade_team_c', 'gas_fee', 'pledge_fee', 'pledge_days', 'valid_rate', 'package_rate', 'thumb',
        'desc', 'content', 'status',
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
    ];

    /* @array $appends */
    protected $appends = [
        'thumb_url', 'wallet_slug',
    ];

    // 获取缩略图地址
    public function getThumbUrlAttribute()
    {
        if ($this->thumb) {
            return Storage::disk('oss')->url($this->thumb);
        } else {
            return '';
        }
    }

    // 获取钱包类型的名称
    public function getWalletSlugAttribute()
    {
        if ($this->wallet_type_id > 0) {
            return WalletType::find($this->wallet_type_id)->slug;
        } else {
            return '';
        }
    }

    // 关联 钱包类型
    public function wallettype()
    {
        return $this->belongsTo(WalletType::class);
    }

    // 关联 订单
    public function order()
    {
        return $this->hasMany(Order::class);
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
