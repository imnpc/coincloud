<?php

namespace App\Models;

use App\Traits\dateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DayReward extends Model
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
        'user_id', 'reward_id', 'product_id', 'wallet_type_id', 'day', 'coin', 'today',
    ];

    // 关联 用户
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 关联 奖励币
    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }

    // 关联 钱包类型
    public function wallettype()
    {
        return $this->belongsTo(WalletType::class);
    }
}
