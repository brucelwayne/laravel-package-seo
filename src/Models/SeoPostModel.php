<?php

namespace Brucelwayne\SEO\Models;

use Laravel\Scout\Searchable;
use Mallria\Core\Models\BaseMongoModel;
use Mallria\Core\Traits\HasMongodbHashId;
use Mallria\Shop\Enums\ExternalPostPlatform;

/**
 * ExternalPostModel
 *
 * @property string $platform 平台标识符
 * @property string $fk_id 外部系统的关联ID
 * @property string $title 帖子的标题
 * @property string $content 帖子的内容
 * @property string $seo_user_id SEO用户ID
 * @property-read string $hash 模型的哈希值
 *
 */
class SeoPostModel extends BaseMongoModel
{
    use HasMongodbHashId;
    use Searchable;

    const TABLE = 'blw_seo_posts';
    protected $table = self::TABLE;
    protected $hashKey = self::TABLE;

    protected $appends = [
        'hash',
    ];

    protected $fillable = [
        'platform',
        'seo_user_id',
        'fk_id',
        'title',
        'content',
        'payload',
    ];

    protected $casts = [
        'platform' => ExternalPostPlatform::class,
    ];

    protected $with = [
        'images',
        'videos',
    ];

    public function images()
    {
        return $this->hasMany(SeoMediaModel::class, 'post_id', '_id')->where('tag', 'img');
    }

    public function videos()
    {
        return $this->hasMany(SeoMediaModel::class, 'post_id', '_id')->where('tag', 'video');
    }

    public function toSearchableArray()
    {
        return $this->toArray();
    }

    public function seoUser()
    {
        return $this->belongsTo(SeoUserModel::class, 'seo_user_id');
    }

}