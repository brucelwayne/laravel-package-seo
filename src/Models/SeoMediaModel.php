<?php

namespace Brucelwayne\SEO\Models;

use Mallria\Core\Models\BaseMongoModel;
use Mallria\Core\Traits\HasMongodbHashId;

class SeoMediaModel extends BaseMongoModel
{
    use HasMongodbHashId;

    const TABLE = 'blw_seo_media';
    protected $table = self::TABLE;
    protected $hashKey = self::TABLE;

    protected $appends = [
        'hash',
    ];

    protected $fillable = [
        'post_id',
        'tag',
        'src',
    ];
}