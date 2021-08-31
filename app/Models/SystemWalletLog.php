<?php

namespace App\Models;

use App\Traits\dateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemWalletLog extends Model
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
        'system_wallet_id', 'product_id', 'wallet_type_id', 'day', 'old_team_a', 'old_team_b', 'old_team_c', 'old_risk',
        'old_commission_balance', 'old_service_fee', 'team_a_add', 'team_b_add', 'team_c_add', 'risk_add', 'commission_balance_add',
        'service_fee_add', 'team_a', 'team_b', 'team_c', 'risk', 'commission_balance', 'service_fee', 'from_user_id', 'order_id',
        'remark',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'wallet_slug',
    ];


    // 获取钱包类型的名称
    public function getWalletSlugAttribute()
    {
        if ($this->wallet_type_id > 0) {
            return WalletType::find($this->wallet_type_id)->slug;
        } else {
            return '';
        }
    }

    // 关联 系统钱包
    public function systemwallet()
    {
        return $this->belongsTo(SystemWallet::class);
    }

    // 关联 产品
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
