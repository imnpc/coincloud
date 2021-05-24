<?php

namespace App\Models;

use App\Traits\dateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Storage;

class Version extends Model
{
    use HasFactory;
    use SoftDeletes;
    use dateTrait;

    // 平台 1-Android 2-iOS
    public const ANDROID = 1; // Android
    public const IOS = 2; // iOS

    public static $platformMap = [
        self::ANDROID => 'Android',
        self::IOS => 'iOS',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'platform', 'version', 'description', 'app', 'url', 'status',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'download_url',
    ];

    public function getDownloadUrlAttribute()
    {
        if ($this->app) {
            return Storage::disk('oss')->url($this->app);
        } else {
            return $this->url;
        }
    }
}
