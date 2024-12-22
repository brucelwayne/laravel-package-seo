<?php

namespace Brucelwayne\SEO\Exports;

use Brucelwayne\SEO\Models\SeoFavoriteQuickCategoryModel;
use Brucelwayne\SEO\Models\SeoPostModel;
use Brucelwayne\SEO\Models\SeoPostQuickCategoryModel;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FavoritePostsExportV3 implements WithMultipleSheets
{
    use Exportable;

    protected $quick_category;

    public function __construct($quick_category)
    {
        $this->quick_category = $quick_category;
    }

    public function sheets(): array
    {
        $sheets = [];
        if (!empty($this->quick_category)) {
            $posts = $this->getCategorizedPosts($this->quick_category->category);
            $sheets[] = new FavoritePostCategorizedSheet($this->quick_category->category, $posts);
        } else {
            //代表获取所有的excel
            $posts = $this->getUncategorizedPosts();
            $sheets[] = new FavoritePostCategorizedSheet(null, $posts);

            $seo_quick_category_models = SeoFavoriteQuickCategoryModel::with(['category'])->get();
            foreach ($seo_quick_category_models as $seo_quick_category_model) {
                $posts = $this->getCategorizedPosts($seo_quick_category_model->category);
                $sheets[] = new FavoritePostCategorizedSheet($seo_quick_category_model->category, $posts);
            }
        }
        return $sheets;
    }

    protected function getCategorizedPosts($category)
    {
        $seo_post_quick_category_models = SeoPostQuickCategoryModel::with(['seoPost'])
            ->where('category_id', $category->getKey())
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