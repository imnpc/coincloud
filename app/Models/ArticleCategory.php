<?php

namespace App\Models;

use App\Traits\dateTrait;
use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ArticleCategory extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ModelTree;
    use AdminBuilder;
    use dateTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id', 'order', 'title', 'icon', 'status',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'icon_url',
    ];

    public function getIconUrlAttribute()
    {
        if ($this->icon) {
            return Storage::disk('oss')->url($this->icon);
        } else {
            return '';
        }
    }
}
