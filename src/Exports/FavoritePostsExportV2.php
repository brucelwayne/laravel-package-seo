<?php

namespace Brucelwayne\SEO\Exports;

use Brucelwayne\SEO\Models\SeoPostModel;
use Brucelwayne\SEO\Models\SeoPostQuickCategoryModel;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Mallria\Category\Models\TransCategoryModel;

class FavoritePostsExportV2 implements WithMultipleSheets
{
    use Exportable;

    protected ?TransCategoryModel $category;
    protected $posts;
    protected $needs_image;

    public function __construct($category = null, $needs_image = false)
    {
        $this->category = $category;
        $this->needs_image = $needs_image;
    }

    public function sheets(): array
    {
        $sheets = [];

        if (empty($this->category)) {
            $this->posts = $this->getUncategorizedPosts();
            $sheets[] = new FavoritePostUnCategorizedSheet($this->posts, $this->needs_image);
        } else {
            $this->posts = $this->getCategorizedPosts();
            $sheets[] = new FavoritePostCategorizedSheet($this->category, $this->posts, $this->needs_image);
        }
        return $sheets;
    }

    protected function getCategorizedPosts()
    {
        $seo_post_quick_category_models = SeoPostQuickCategoryModel::with(['seoPost'])
            ->where('category_id', $this->category->getKey())
            ->get();
        return $seo_post_quick_category_models->pluck('seoPost');
    }

    protected function getUncategorizedPosts()
    {
        return SeoPostModel::raw(function ($collection) {
            return $collection->aggregate([
                // 1. 使用 $lookup 关联 categories 表
                [
                    '$lookup' => [
                        'from' => 'seo_post_quick_category', // 关联的集合名
                        'localField' => '_id',  // posts 表的字段
                        'foreignField' => 'seo_post_id', // categories 表的字段
                        'as' => 'category_info', // 关联结果保存的字段名
                    ]
                ],
                // 2. 筛选没有分类记录的 posts
                [
                    '$match' => [
                        'category_info' => ['$size' => 0] // 只保留关联结果为空的记录
                    ]
                ]
            ]);
        });
    }
}