<?php

namespace App\Models;

use App\Traits\dateTrait;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Storage;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    use dateTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'tag', 'price', 'price_usdt', 'price_coin', 'coin_wallet_address', 'coin_wallet_qrcode', 'wallet_type_id',
        'wait_days', 'valid_days', 'valid_days_text', 'choose_reason', 'choose_reason_text', 'service_rate', 'day_customer_rate',
        'day_rate', 'freed_rate', 'freed_days', 'parent1', 'parent2', 'invite_rate', 'bonus_team_a', 'bonus_team_b', 'bonus_team_c',
        'upgrade_team_a', 'upgrade_team_b', 'upgrade_team_c', 'gas_fee', 'pledge_fee', 'pledge_days', 'valid_rate', 'package_rate',
        'thumb', 'desc', 'content', 'status', 'is_show_text',
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

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
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

    // 关联 每日分红
    public function daybonus()
    {
        return $this->hasMany(DayBonus::class);
    }

    // 关联 默认每日分红
    public function defaultdaybonus()
    {
        return $this->hasOne(DefaultDayBonus::class);
    }

    // 关联 用户分红
    public function userbonus()
    {
        return $this->hasMany(UserBonus::class);
    }

    // 关联 线性释放
    public function freed()
    {
        return $this->hasMany(Freed::class);
    }

    // 关联 每日线性释放记录
    public function dayfreed()
    {
        return $this->hasMany(DayFreed::class);
    }

    // 关联 每周统计报表
    public function weekly()
    {
        return $this->hasMany(Weekly::class);
    }

    // 关联 每周统计详细
    public function weeklylog()
    {
        return $this->hasMany(WeeklyLog::class);
    }

}
