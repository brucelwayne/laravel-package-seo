<?php

namespace Brucelwayne\SEO\Exports;

use Brucelwayne\SEO\Models\SeoFavoriteQuickCategoryModel;
use Brucelwayne\SEO\Models\SeoPostQuickCategoryModel;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FavoritePostsExport implements WithMultipleSheets
{
    use Exportable;

    protected $quick_category;

    public function __construct($quick_category)
    {
        $this->quick_category = $quick_category;
    }

    public function sheets(): array
    {
        $seo_quick_category_models = SeoFavoriteQuickCategoryModel::with(['category'])->get();
        $seo_post_quick_category_models = SeoPostQuickCategoryModel::with(['seoPost', 'category'])->get();


//        $allPosts = UserFavoriteSeoPostModel::with(['seoPost'])->get();
//        $categories = $allPosts->pluck('category')->unique();
//
//        $sheets = [];
//
//        // Handle categorized posts
//        $categorizedPosts = $allPosts->filter(function ($post) {
//            return $post->category;
//        });
//        foreach ($categories->filter() as $category) {
//            if ($category) {
//                $sheets[] = new FavoritePostCategorizedSheet($category, $categorizedPosts);
//            }
//        }
//
//        // Handle posts without a category
//        $uncategorizedPosts = $allPosts->filter(function ($post) {
//            return !$post->category;
//        });
//
//        if (!$uncategorizedPosts->isEmpty()) {
//            $sheets[] = new FavoritePostUnCategorizedSheet($uncategorizedPosts);
//        }
//
//        return $sheets;
    }
}