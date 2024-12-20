<?php

namespace Brucelwayne\SEO\Exports;

use Brucelwayne\SEO\Models\UserFavoriteSeoPostModel;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FavoritePostsExport implements WithMultipleSheets
{
    public function __construct()
    {
    }

    public function sheets(): array
    {
        $allPosts = UserFavoriteSeoPostModel::with(['seoPost'])->get();
        $categories = $allPosts->pluck('category')->unique();

        $sheets = [];

        // Handle categorized posts
        $categorizedPosts = $allPosts->filter(function ($post) {
            return $post->category;
        });
        foreach ($categories->filter() as $category) {
            if ($category) {
                $sheets[] = new FavoritePostCategorizedSheet($category, $categorizedPosts);
            }
        }

        // Handle posts without a category
        $uncategorizedPosts = $allPosts->filter(function ($post) {
            return !$post->category;
        });

        if (!$uncategorizedPosts->isEmpty()) {
            $sheets[] = new FavoritePostUnCategorizedSheet($uncategorizedPosts);
        }

        return $sheets;
    }
}