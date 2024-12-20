<?php

namespace Brucelwayne\SEO\Models;

use Mallria\Category\Models\TransCategoryModel;
use Mallria\Core\Models\BaseMongoModel;

class SeoPostQuickCategoryModel extends BaseMongoModel
{
    protected $collection = 'seo_post_quick_category';

    protected $fillable = [
        'action',
        'seo_post_id',
        'quick_category_id',
        'category_id',
    ];

    /**
     * 关联到 SeoFavoriteQuickCategoryModel
     */
    public function quick_category()
    {
        return $this->belongsTo(SeoFavoriteQuickCategoryModel::class, 'quick_category_id', '_id');
    }

    /**
     * 关联到其他表，例如 SeoPost 模型（根据项目具体需求）
     */
    public function seoPost()
    {
        // 假设 SeoPostModel 存在
        return $this->belongsTo(SeoPostModel::class, 'seo_post_id', '_id');
    }

    function category()
    {
        return $this->belongsTo(TransCategoryModel::class, 'category_id', 'id');
    }
}
