<?php

namespace App\Models;

use App\Traits\dateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use SolutionForest\Translatable\HasTranslations;
use Storage;

class Article extends Model
{
    use HasFactory;
    use SoftDeletes;
    use dateTrait;
    use HasTranslations;

    public $translatable = ['title', 'content', 'desc'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'article_category_id', 'title', 'content', 'is_recommand', 'status', 'thumb',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'thumb_url',
    ];

    public function getThumbUrlAttribute()
    {
        if ($this->thumb) {
            return Storage::disk(config('filesystems.default'))->url($this->thumb);
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
