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
        'user_id', 'day', 'yesterday_power', 'power_add', 'power', 'coin_add', 'coin_add_day', 'coin', 'coin_user', 'rate_day',
        'rate_freed', 'coin_rate_day', 'coin_freed', 'coin_freed_day', 'coin_freed_other', 'coin_day', 'pay_customer_rate',
        'balance', 'parent1_balance', 'parent1_uid', 'parent1', 'parent2_balance', 'parent2_uid', 'parent2', 'bonus_rate',
        'bonus_pool', 'fee', 'status', 'max_valid_power', 'borrowed_filecoin_coin_day', 'borrowed_filecoin_other', 'bonus_id',
        'type', 'product_id', 'returns_ratio', 'system_coin',
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
}
