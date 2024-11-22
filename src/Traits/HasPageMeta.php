<?php

namespace Brucelwayne\SEO\Traits;

use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Brucelwayne\SEO\Enums\SeoType;
use Mallria\Core\Models\PageModel;

trait HasPageMeta
{
    function setPageMeta($domain, $route, SeoType $type = SeoType::WebPage)
    {
        $page_model = PageModel::byDomainRoute($domain, $route);
        $url = route($route);
        if (!empty($page_model)) {

            $title = $page_model->title;
            $description = $page_model->excerpt;
            $featured_image = empty($page_model->image->normal) ? null : empty($page_model->image->normal);

            if (empty($title) || empty($description)) {
                return;
            }

            SEOMeta::setTitle($title);
            SEOMeta::setDescription($description);
            SEOMeta::addMeta('publisher', config('app.name'));

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
            TwitterCard::setUrl($url);

            JsonLd::setTitle($title);
            JsonLd::addValue('headline', $title);
            JsonLd::setDescription($description);
            JsonLd::setUrl($url);
            JsonLd::setType($type->value);

            JsonLd::addValue('datePublished', $page_model->created_at->toIso8601String());
            JsonLd::addValue('dateModified', $page_model->updated_at->toIso8601String());

            JsonLd::addValue('publisher', [
                '@type' => 'Organization',
                'name' => config('app.name'),
                'url' => config('app.url'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('mallria-logo-transparent-white-bg.png')  // 替换为你的 logo URL
                ],
            ]);
        }
        return $page_model;
    }
}
