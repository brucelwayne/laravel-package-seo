<?php

namespace Brucelwayne\SEO\Models;

use Mallria\Category\Models\TransCategoryModel;
use Mallria\Category\Traits\HasMongoCategory;
use Mallria\Core\Models\BaseMongoModel;
use Mallria\Core\Models\User;
use Mallria\Core\Traits\HasMongodbHashId;

/**
 * Class UserFavoriteSeoPostModel
 *
 * @property string $_id  // 假设这是 MongoDB 的 ID
 * @property string $user_id  // 用户 ID
 * @property string $seo_post_id  // SEO 文章 ID
 * @property User $user  // 与 User 模型的关系
 * @property SeoPostModel $seoPost  // 与 SeoPostModel 模型的关系
 */
class UserFavoriteSeoPostModel extends BaseMongoModel
{
    use HasMongodbHashId;
    use HasMongoCategory;

    const TABLE = 'blw_user_favorite_seo_posts';
    protected $table = self::TABLE;
    protected $hashKey = self::TABLE;

    protected $fillable = [
        'user_id',
        'seo_post_id',
        'category_id',
    ];

    // 定义与 User 模型的关系
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 定义与 SeoPost 模型的关系
    public function seoPost()
    {
        return $this->belongsTo(SeoPostModel::class, 'seo_post_id');
    }

    public function category()
    {
        return $this->hasOne(TransCategoryModel::class, 'id', 'category_id');
    }
}
