<?php

namespace App\Models;

use App\Traits\dateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WeeklyLog extends Model
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
        'product_id', 'wallet_type_id', 'user_id', 'weekly_id', 'year', 'week', 'begin', 'end', 'begin_time', 'end_time',
        'freed', 'freed75', 'reward', 'total',
    ];

    // 关联 用户
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 关联 每周统计数据
    public function weekly()
    {
        return $this->belongsTo(Weekly::class);
    }
}
