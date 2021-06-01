<?php

namespace App\Models;

use App\Traits\dateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserWalletLog extends Model
{
    use HasFactory;
    use SoftDeletes;
    use dateTrait;

    // 来源
    public const FROM_ADMIN = -1; // 后台
    public const FROM_NORMAL = 0; // 默认 正常
    public const FROM_COMMISSION = 1; // 推荐
    public const FROM_DIVIDENDS = 2; // 股东分红
    public const FROM_IN = 3; // 转入
    public const FROM_OUT = 4; // 转出
    public const FROM_FREED = 5; // 线性释放
    public const FROM_DAY_BONUS = 6; // 每日分红
    public const FROM_REWARD = 7; // 奖励币
    public const FROM_WITHDRAW = 8; // 提币
    public const FROM_WITHDRAW_CASH = 9; // 提现
    public const FROM_BORROW = 10; // 借币
    public const FROM_FREED75 = 11; // 75% 线性释放
    public const FROM_PLEDGE = 12; // 质押币
    public const FROM_RECHARGE = 13; // 充值
    public const FROM_VALID_POWER = 14; // 有效算力
    public const FROM_RECHARGE_COMPANY = 15; // 公司充值
    public const FROM_LEND = 16; // 出借
    public const FROM_LEND_DAY = 17; // 出借利息
    public const FROM_LEND_RETURN = 18; // 归还本金
    public const FROM_TEAM_DIVIDENDS = 19; // 分红池分红
    public const FROM_REWARD_FREED = 20; // 奖励算力线性释放
    public const FROM_REWARD_FREED75 = 21; // 奖励算力 75% 线性释放
    public const FROM_REWARD_DAY = 22; // 奖励产币
    public const FROM_CANCEL_WITHDRAW = 23; // 取消提币
    public const FROM_CANCEL_RECHARGE = 24; // 取消充值
    public const FROM_RECHARGE_RETURN = 25; // 返还剩余充值余额

    public static $fromMap = [
        self::FROM_ADMIN => '后台',
        self::FROM_NORMAL => '正常',
        self::FROM_COMMISSION => '推荐',
        self::FROM_DIVIDENDS => '股东分红',
        self::FROM_IN => '转入',
        self::FROM_OUT => '转出',
        self::FROM_FREED => '25% 一次性释放',
        self::FROM_DAY_BONUS => '每日分红',
        self::FROM_REWARD => '奖励币',
        self::FROM_WITHDRAW => '提币',
        self::FROM_WITHDRAW_CASH => '提现',
        self::FROM_BORROW => '借币',
        self::FROM_FREED75 => '75% 线性释放',
        self::FROM_PLEDGE => '质押币',
        self::FROM_RECHARGE => '充值',
        self::FROM_VALID_POWER => '有效算力',
        self::FROM_RECHARGE_COMPANY => '公司充值',
        self::FROM_LEND => '出借',
        self::FROM_LEND_DAY => '出借利息',
        self::FROM_LEND_RETURN => '归还本金',
        self::FROM_TEAM_DIVIDENDS => '分红池分红',
        self::FROM_REWARD_FREED => '奖励算力 25% 一次性释放',
        self::FROM_REWARD_FREED75 => '奖励算力 75% 线性释放',
        self::FROM_REWARD_DAY => '奖励产币',
        self::FROM_CANCEL_RECHARGE => '取消充值',
        self::FROM_RECHARGE_RETURN => '返还剩余充值余额',
        self::FROM_CANCEL_WITHDRAW => '取消提币',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'wallet_type_id', 'from_user_id', 'day', 'old', 'add', 'new', 'from', 'remark',
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

    // 关联 用户
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 关联 钱包类型
    public function wallettype()
    {
        return $this->belongsTo(WalletType::class);
    }

}
