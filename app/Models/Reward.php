<?php

namespace App\Models;

use App\Traits\dateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reward extends Model
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
        'user_id', 'order_id', 'product_id', 'wallet_type_id', 'power', 'reward_base', 'coin_freed', 'coin_freed_day', 'days',
        'already_day', 'already_coin', 'wait_coin', 'status',
    ];

    // 关联 用户
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 关联 订单
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // 关联 奖励币每日线性释放记录
    public function dayreward()
    {
        return $this->hasMany(DayReward::class);
    }

    // 关联 钱包类型
    public function wallettype()
    {
        return $this->belongsTo(WalletType::class);
    }
}
