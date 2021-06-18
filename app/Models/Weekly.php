<?php

namespace App\Models;

use App\Traits\dateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Weekly extends Model
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
        'product_id', 'wallet_type_id', 'year', 'week', 'begin', 'end', 'begin_time', 'end_time', 'freed', 'freed75', 'reward',
        'total',
    ];

    // 关联 每周数据详细列表
    public function weeklylog()
    {
        return $this->hasMany(WeeklyLog::class);
    }
}
