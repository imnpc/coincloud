<?php

namespace App\Models;

use App\Traits\dateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Storage;

class Withdraw extends Model
{
    use HasFactory;
    use SoftDeletes;
    use dateTrait;

    //0-审核中 1-已完成 2-已取消
    public const STATUS_WAIT = 0; // 审核中
    public const STATUS_SUCCESS = 1; // 已完成
    public const STATUS_CANCEL = 2; // 已取消

    public static $statusMap = [
        self::STATUS_WAIT => '审核中',
        self::STATUS_SUCCESS => '已完成',
        self::STATUS_CANCEL => '已取消',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'wallet_type_id', 'image', 'wallet_address', 'coin', 'fee', 'real_coin', 'status', 'reason', 'canceled_time',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'image_url', 'status_text', 'wallet_slug',
    ];

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return Storage::disk(config('filesystems.default'))->url($this->image);
        } else {
            return '';
        }
    }

    public function getStatusTextAttribute()
    {
        return self::$statusMap[$this->status];
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

    // 关联 用户
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
