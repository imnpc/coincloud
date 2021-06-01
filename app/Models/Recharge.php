<?php

namespace App\Models;

use App\Traits\dateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Storage;

class Recharge extends Model
{
    use HasFactory;
    use SoftDeletes;
    use dateTrait;

    // 支付类型 1-充值 2-账户转入
    public const PAY_RECHARGE = 1;
    public const PAY_TRANSFER = 2;
    public const PAY_COMPANY = 3;
    public static $paymentMap = [
        self::PAY_RECHARGE => '充值',
        self::PAY_TRANSFER => '账户转入',
        self::PAY_COMPANY => '公司充值',
    ];

    // 支付状态 0-未提交 1-审核中 2-已完成
    public const STATUS_CANCEL = -1; // 已取消
    public const STATUS_WAIT = 0; // 未提交
    public const STATUS_PENDING = 1; // 审核中
    public const STATUS_SUCCESS = 2; // 已完成

    public static $statusMap = [
        self::STATUS_CANCEL => '已取消',
        self::STATUS_WAIT => '未提交',
        self::STATUS_PENDING => '审核中',
        self::STATUS_SUCCESS => '已完成',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_sn', 'user_id', 'wallet_type_id', 'coin', 'used_coin', 'pledge', 'gas_fee', 'pay_type', 'pay_image', 'pay_time',
        'confirm_time', 'pay_status', 'schedule', 'schedule_time', 'finished_time', 'is_return', 'return_coin', 'reason',
        'remark', 'canceled_time',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'pay_image_url', 'pay_status_text', 'wallet_slug',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'pay_time',
        'confirm_time',
        'schedule_time',
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

    public function getPayImageUrlAttribute()
    {
        if ($this->pay_image) {
            return Storage::disk('oss')->url($this->pay_image);
        } else {
            return '';
        }
    }

    public function getPayStatusTextAttribute()
    {
        return self::$statusMap[$this->pay_status];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 关联 用户充值
    public function recharge()
    {
        return $this->hasMany(Recharge::class);
    }

    // 关联 用户充值封装记录
    public function rechargeaccountlog()
    {
        return $this->hasMany(RechargeAccountLog::class);
    }
}
