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
        'order_sn', 'user_id', 'wallet_type_id', 'coin', 'pay_type', 'pay_image', 'pay_time', 'confirm_time', 'pay_status',
        'schedule', 'schedule_time', 'used_coin', 'is_return', 'return_coin', 'reason', 'canceled_time',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'pay_image_url', 'pay_status_text',
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

//    // 关联 用户充值封装记录
//    public function rechargeaccountlog()
//    {
//        return $this->hasMany(RechargeAccountLog::class);
//    }
}
