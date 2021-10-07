<?php

namespace App\Models;

use App\Traits\dateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserBonus extends Model
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
        'day', 'day_bonus_id', 'user_id', 'product_id', 'bonus_coin_add', 'valid_power', 'each_add', 'coins', 'pay_user_rate',
        'coin_for_user', 'now_rate', 'coin_now', 'freed_rate', 'coin_freed', 'coin_freed_day', 'coin_freed_other', 'coin_day',
        'balance', 'parent1_uid', 'parent1_rate', 'coin_parent1', 'parent2_uid', 'parent2_rate', 'coin_parent2', 'bonus_rate',
        'coin_bonus', 'risk_rate', 'coin_risk', 'status', 'order_id',
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

    // 关联 每日分红
    public function daybonus()
    {
        return $this->belongsTo(DayBonus::class);
    }

    // 关联 线性释放
    public function freed()
    {
        return $this->hasMany(Freed::class);
    }
}
