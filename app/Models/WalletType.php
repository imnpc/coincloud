<?php

namespace App\Models;

use App\Traits\dateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Storage;

class WalletType extends Model implements Sortable
{
    use HasFactory;
    use SoftDeletes;
    use dateTrait;
    use SortableTrait;

    public $sortable = [
        'order_column_name' => 'sort',
        'sort_when_creating' => true,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'slug', 'description', 'decimal_places', 'is_enblened', 'sort',
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
        'icon_url',
    ];

    public function getIconUrlAttribute()
    {
        if ($this->icon) {
            return Storage::disk(config('filesystems.default'))->url($this->icon);
        } else {
            return '';
        }
    }

    // 关联 产品
    public function product()
    {
        return $this->hasMany(Product::class);
    }
}
