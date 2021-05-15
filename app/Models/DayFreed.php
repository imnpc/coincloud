<?php

namespace App\Models;

use App\Traits\dateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DayFreed extends Model
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
        'user_id', 'freed_id', 'product_id', 'day', 'coin', 'today',
    ];

    // 关联 用户
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 关联 用户分红
    public function freed()
    {
        return $this->belongsTo(Freed::class);
    }

    // 关联 产品
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
