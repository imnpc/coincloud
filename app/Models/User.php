<?php

namespace App\Models;

use App\Services\LogService;
use App\Traits\dateTrait;
use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Interfaces\WalletFloat;
use Bavix\Wallet\Traits\HasWallet;
use Bavix\Wallet\Traits\HasWalletFloat;
use Bavix\Wallet\Traits\HasWallets;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Mrlaozhou\Extend\Unlimitedable;
use Storage;
use Laravel\Passport\HasApiTokens;
use Overtrue\EasySms\PhoneNumber;

class User extends Authenticatable implements Wallet, WalletFloat
{
    use HasFactory, Notifiable;
    use HasApiTokens;
    use SoftDeletes;
    use HasWallet, HasWallets;
    use HasWalletFloat;
    use dateTrait;
    use Unlimitedable;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * 缓存key
     * @return string
     */
    protected static function unlimitedCacheKey()
    {
        return 'users.parent';
    }

    // 钱包列表
    public const WALLETSLIST = [
        ['name' => '余额', 'slug' => 'money', 'decimal_places' => '2',],
        ['name' => '积分', 'slug' => 'credit', 'decimal_places' => '2',],
        ['name' => 'Tether 钱包', 'slug' => 'USDT', 'decimal_places' => '5',],
        ['name' => 'Filecoin 钱包', 'slug' => 'FIL', 'decimal_places' => '5',],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'mobile', 'nickname', 'parent_id', 'status', 'last_login_at', 'last_login_ip', 'avatar',
        'real_name', 'id_number', 'id_front', 'id_back', 'is_verify', 'money_password', 'level_id', 'team_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'avatar_url', 'invite_code', 'id_front_url', 'id_back_url',
    ];

    // 返回头像链接
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return Storage::disk('oss')->url($this->avatar);
        } else {
            return '';
        }
    }

    public function getInviteCodeAttribute()
    {
        return \Hashids::encode($this->attributes['id']);
    }

    public function getIdFrontUrlAttribute()
    {
        if ($this->id_front) {
            return Storage::disk('oss')->url($this->id_front);
        } else {
            return '';
        }
    }

    public function getIdBackUrlAttribute()
    {
        if ($this->id_back) {
            return Storage::disk('oss')->url($this->id_back);
        } else {
            return '';
        }
    }

    // 关联 订单
    public function order()
    {
        return $this->hasMany(Order::class);
    }

    // 关联 用户分红
    public function userbonus()
    {
        return $this->hasMany(UserBonus::class);
    }

    // 关联 线性释放
    public function freed()
    {
        return $this->hasMany(Freed::class);
    }

    // 关联 每日线性释放记录
    public function dayfreed()
    {
        return $this->hasMany(DayFreed::class);
    }

    // 关联 问题反馈
    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    // 关联 用户钱包日志
    public function userwalletlog()
    {
        return $this->hasMany(UserWalletLog::class);
    }

    // 关联 用户充值封装记录
    public function rechargeaccountlog()
    {
        return $this->hasMany(RechargeAccountLog::class);
    }

    // 关联每周统计详细数据
    public function weeklylog()
    {
        return $this->hasMany(WeeklyLog::class);
    }

    // 关联 等级
    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    // 用户总数
    public static function count()
    {
        return self::all()->count();
    }

    // 已购T数
    public static function buypower()
    {
        return 0;
    }

    // 用户推荐下级列表
    public function sons()
    {
        return $this->hasMany(User::class, 'parent_id', 'id');
    }

    /**
     * 获取登录用户手机号码
     * @param $notification
     * @return PhoneNumber
     */
    public function routeNotificationForEasySms($notification)
    {
        return new PhoneNumber($this->mobile);
    }

    /**
     * Passport 登录支持 邮箱 和 手机号码
     * @param $username
     * @return mixed
     */
    public function findForPassport($username)
    {
        filter_var($username, FILTER_VALIDATE_EMAIL) ?
            $credentials['email'] = $username :
            $credentials['mobile'] = $username;

        return self::where($credentials)->first();
    }

    /**
     * @param int $wallet_type_id 钱包类型ID
     * @param int $user_id 用户 id
     * @param float $user_coin 单用户每日产币总量
     * @param int $level_id 上一轮等级
     * @param int $last_rate 最后分润比例
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function JiCha(int $wallet_type_id, int $user_id, $user_coin, $level_id, $last_rate = 0)
    {
        $logService = app()->make(LogService::class);
        $day = Carbon::now()->toDateTimeString();
        $user = User::find($user_id);
        $fix_rate = 0; // 修正的实际比例
        // 判断当前用户team_id 是否大于0
        if ($user->team_id > 0) {
            // 团队长用户
            $top_user = User::find($user->team_id);
            // 团队长级别
            $level = Level::find($top_user->level_id);
            $reward_rate = $level->reward_rate;
            $rate = $reward_rate - $last_rate;
            if ($rate > 0) {
                $fix_rate = $rate;
                $team_bonus = number_fixed($user_coin * $rate / 100);
                // 给团队长发提成
                $remark = "团队分红-" . $fix_rate;
                $logService->userLog($top_user->id, $wallet_type_id, $team_bonus, 0, $day, UserWalletLog::FROM_TEAM_BONUS, $remark);
            }

            // 最高等级的平级奖
//            if ($level_id == 7 && $top_user->level_id == 7) {
//                $peer = 10;
//                $team_bonus = number_fixed($user_coin * $peer / 100);
//                // 给团队长发提成
//                $remark = "平级奖-" . $team_bonus;
//                $logService->userLog($top_user->id, $wallet_type_id, $team_bonus, 0, $day, UserWalletLog::FROM_TEAM_BONUS, $remark);
//            }
            $last_rate = $last_rate + $fix_rate;
            // 递归
            if ($top_user->team_id > 0) {
                self::JiCha($wallet_type_id, $top_user->id, $user_coin, $top_user->level_id, $last_rate);
            }
        }
    }

}
