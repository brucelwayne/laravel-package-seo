<?php

namespace Brucelwayne\SEO\Models;


use Mallria\Category\Models\TransCategoryModel;
use Mallria\Core\Models\BaseMongoModel;
use Mallria\Core\Traits\HasMongodbHashId;

class SeoFavoriteQuickCategoryModel extends BaseMongoModel
{
    use HasMongodbHashId;

    protected $collection = 'seo_favorite_quick_category';

    protected $appends = [
        'hash',
    ];

    protected $fillable = [
        'scene',
        'category_id',
    ];

    protected $casts = [
        'scene' => 'string',
        'category_id' => 'integer',
    ];

    public function getRouteKeyName()
    {
        return 'hash';
    }

    function category()
    {
        return $this->belongsTo(TransCategoryModel::class, 'category_id', 'id');
    }
}