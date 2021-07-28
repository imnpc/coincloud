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

    // 关联 系统钱包
    public function systemwallet()
    {
        return $this->belongsTo(SystemWallet::class);
    }
}
