<?php

namespace App\Models;

use App\Traits\dateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Storage;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;
    use dateTrait;

    // 支付方式 0-后台 1-银行转账 2-USDT 3-其他虚拟币'
    public const PAY_ADMIN = 0;
    public const PAY_BANK = 1;
    public const PAY_USDT = 2;
    public const PAY_COIN = 3;
    public static $paymentMap = [
        self::PAY_ADMIN => '后台',
        self::PAY_BANK => '银行转账',
        self::PAY_USDT => 'USDT',
        self::PAY_COIN => '其他虚拟币',
    ];

    // 支付状态 0-已完成 1-未提交 2-审核中
    public const PAID_COMPLETE = 0;
    public const PAID_CREATE = 1;
    public const PAID_CONFIRM = 2;
    public static $paidMap = [
        self::PAID_COMPLETE => '已完成',
        self::PAID_CREATE => '未提交',
        self::PAID_CONFIRM => '审核中',
    ];

    // 等待状态 0-已生效 1-等待中
    public const WAIT_SUCCESS = 0;
    public const WAIT_PENDING = 1;
    public static $waitMap = [
        self::WAIT_SUCCESS => '已生效',
        self::WAIT_PENDING => '等待中',
    ];
    // 封装状态 0-封装完成 1-等待封装 2-封装中
    public const PACKAGE_COMPLETE = 0;
    public const PACKAGE_WAIT = 1;
    public const PACKAGE_PENDING = 2;
    public static $packageMap = [
        self::PACKAGE_COMPLETE => '封装完成',
        self::PACKAGE_WAIT => '等待封装',
        self::PACKAGE_PENDING => '封装中',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_sn', 'user_id', 'product_id', 'number', 'wallet_type_id', 'pay_money', 'wait_days', 'wait_status', 'valid_days',
        'valid_rate', 'valid_power', 'max_valid_power', 'package_rate', 'package_already', 'package_wait', 'package_status',
        'payment', 'payment_type', 'pay_status', 'pay_image', 'pay_time', 'confirm_time', 'is_output_coin', 'status', 'remark',
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
        'pay_time' => 'datetime',
        'confirm_time' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'pay_image_url', 'paid_text',
    ];

    // 返回支付上传图片的 URL
    public function getPayImageUrlAttribute()
    {
        if ($this->pay_image) {
            return Storage::disk(config('filesystems.default'))->url($this->pay_image);
        } else {
            return '';
        }
    }

    // 返回支付状态文本
    public function getPaidTextAttribute()
    {
        return self::$paidMap[$this->pay_status];
    }

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

    // 关联 钱包类型
    public function wallettype()
    {
        return $this->belongsTo(WalletType::class);
    }

    // 已售 T 币
    public static function total()
    {
        return self::where('user_id', '>', 0)
            ->where('status', '=', 0)
            ->where('pay_status', '=', 0)
            ->sum('number');
    }

    // 等待生效的 T 币
    public static function wait()
    {
        return self::where('wait_status', '=', 1)
            ->where('status', '=', 0)
            ->where('pay_status', '=', 0)
            ->sum('number');
    }

    // 已经生效的 T 币
    public static function success()
    {
        return self::where('wait_status', '=', 0)
            ->where('status', '=', 0)
            ->where('pay_status', '=', 0)
            ->sum('number');
    }
}
