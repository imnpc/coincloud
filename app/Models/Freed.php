<?php

namespace App\Models;

use App\Traits\dateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Freed extends Model
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
        'user_id', 'user_bonus_id', 'product_id', 'day', 'coins', 'rate_freed', 'coin_freed', 'coin_freed_day', 'other_fee',
        'days', 'already_day', 'already_coin', 'wait_coin', 'status',
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

    // 关联 用户分红
    public function userbonus()
    {
        return $this->belongsTo(UserBonus::class);
    }

    // 关联 每日线性释放记录
    public function dayfreed()
    {
        return $this->hasMany(DayFreed::class);
    }

    // 关联 产品
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
