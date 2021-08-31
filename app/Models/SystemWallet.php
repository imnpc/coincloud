<?php

namespace App\Models;

use App\Traits\dateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemWallet extends Model
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
        'product_id', 'wallet_type_id', 'team_a', 'team_b', 'team_c', 'risk', 'commission_balance', 'service_fee',
    ];

    // 关联 产品
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // 关联 系统钱包日志
    public function systemwalletlog()
    {
        return $this->hasMany(SystemWalletLog::class);
    }
}
