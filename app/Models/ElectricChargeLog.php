<?php

namespace App\Models;

use App\Traits\dateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Storage;

class ElectricChargeLog extends Model
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
        'product_id', 'wallet_type_id', 'user_id', 'electric_charge_id', 'year', 'month', 'electric_charge', 'number',
        'total_fee', 'pay_image', 'pay_time', 'confirm_time', 'pay_status',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'pay_image_url', 'wallet_slug', 'paid_text',
    ];

    // 返回支付上传图片的 URL
    public function getPayImageUrlAttribute()
    {
        if ($this->pay_image) {
            return Storage::disk('public')->url($this->pay_image);
        } else {
            return '';
        }
    }

    // 获取钱包类型的名称
    public function getWalletSlugAttribute()
    {
        if ($this->wallet_type_id > 0) {
            return WalletType::find($this->wallet_type_id)->slug;
        } else {
            return '';
        }
    }

    // 返回支付状态文本
    public function getPaidTextAttribute()
    {
        return Order::$paidMap[$this->pay_status];
    }

    // 关联 用户
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 关联 电费
    public function electriccharge()
    {
        return $this->belongsTo(ElectricCharge::class);
    }

    // 关联 产品
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
