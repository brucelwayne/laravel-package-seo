<?php

namespace Brucelwayne\SEO\Traits;

use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Mallria\Core\Models\PageModel;

trait HasSEOPageMeta
{
    function setPageMeta($domain, $route)
    {
        $page_model = PageModel::byDomainRoute($domain, $route);
        $url = route($route);
        if (!empty($page_model)) {

            $title = $page_model->title;
            $description = $page_model->description;
            $featured_image = empty($page_model->image->normal) ? null : empty($page_model->image->normal);

            if (empty($title) || empty($description)) {
                return;
            }

            SEOMeta::setTitle($title);
            SEOMeta::setDescription($description);
            SEOMeta::addMeta('author', config('app.name'));

            OpenGraph::setTitle($title);
            OpenGraph::setDescription($description);
            OpenGraph::setUrl($url);
            if (!empty($featured_image)) {
                OpenGraph::addImage($featured_image); // 添加图片链接
            }
            OpenGraph::setSiteName(config('app.name'));

            TwitterCard::setType('summary_large_image'); // 设定为大图卡片
            TwitterCard::setTitle($title);
            TwitterCard::setDescription($description);
            if (!empty($featured_image)) {
                TwitterCard::setImage($featured_image); // 添加图片链接
            }
            TwitterCard::setSite(config('app.name'));

            JsonLd::setTitle($title);
            JsonLd::setDescription($description);
            JsonLd::setUrl($url);
            JsonLd::setType('BlogPosting');
            JsonLd::setSite(config('app.name'));

            JsonLd::addValue('datePublished', $page_model->created_at->toIso8601String());
            JsonLd::addValue('dateModified', $page_model->updated_at->toIso8601String());
            JsonLd::addValue('author', [
                '@type' => 'Organization',
                'name' => config('app.name'),
                'url' => config('app.url'),
            ]);

            JsonLd::addValue('publisher', [
                '@type' => 'Organization',
                'name' => config('app.name'),
            ]);
        }
    }
}