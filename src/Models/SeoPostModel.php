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
 * @property string $converted_at 什么时候转为帖子的
 * @property integer $converted_post_id 转为帖子的id
 * @property array download_medias 已经下载的文件
 *
 * @property SeoUserModel $seo_user
 *
 * @method static static create($data)
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
        'converted_at',
        'converted_post_id',
        'payload',
        'created_at',
        'updated_at',
        'download_medias',
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


    public function seo_user()
    {
        return $this->belongsTo(SeoUserModel::class, 'seo_user_id', 'id');
    }

    public function toSearchableArray()
    {
        $array = $this->toArray();
        return ensureArrayUtf8Encoding($array);
    }

}