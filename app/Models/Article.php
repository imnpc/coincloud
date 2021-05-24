<?php

namespace App\Models;

use App\Traits\dateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Storage;

class Article extends Model
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
        'article_category_id', 'title', 'content', 'is_recommand', 'status', 'thumb',
    ];

    /* @array $appends */
    protected $appends = [
        'thumb_url',
    ];

    public function getThumbUrlAttribute()
    {
        if ($this->thumb) {
            return Storage::disk('oss')->url($this->thumb);
        } else {
            return '';
        }
    }

    // 关联 文章分类
    public function article_category()
    {
        return $this->belongsTo(ArticleCategory::class);
    }

}
