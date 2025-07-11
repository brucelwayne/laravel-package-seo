<?php

namespace Brucelwayne\SEO\Traits;

use Artesaos\SEOTools\Facades\{JsonLd, OpenGraph, SEOMeta, TwitterCard};
use Brucelwayne\SEO\Enums\SeoType;
use Mallria\Core\Facades\OptionCacheFacade;
use Mallria\Core\Models\PageModel;
use Mallria\Main\Enums\CacheKey;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

trait HasPageMeta
{
    /**
     * Set SEO metadata for a page
     *
     * @param string $domain
     * @param string $route
     * @param SeoType $type
     * @param string $title
     * @return PageModel|null
     */
    public function setPageMeta(
        string  $domain,
        string  $route,
        SeoType $type = SeoType::WebPage,
        string  $title = '',
        bool    $suffix = true
    ): ?PageModel
    {
        $pageModel = PageModel::byDomainRoute($domain, $route);
        $url = route($route);
        $appName = config('app.name');
        $slogan = config('app.slogan');

        if (!$pageModel) {
            // 是否首页，判断标题和appName是否相等（忽略大小写）
            $isSame = $title && (strtolower($title) === strtolower($appName));

            $seoTitle = !$title || $isSame
                ? $this->prepareTitle($appName, $slogan)
                : $this->prepareTitle($title, $appName);

            SEOMeta::setTitle($seoTitle);
            return null;
        }

        // 优先使用 PageModel 的标题，否则回退到传入的 $title，再到 appName
        $resolvedTitle = $pageModel->title ?: ($title ?: $appName);

        // 判断标题是否以 "Mallria - " 开头（忽略大小写）
        $prefix = $appName . ' - ';
        if (stripos($resolvedTitle, $prefix) === 0) {
            // 以 "Mallria - " 开头，不拼接后缀
            $seoTitle = $resolvedTitle;
        } else {
            // 根据 $suffix 决定是否拼接后缀
            if ($suffix) {
                $seoTitle = $this->prepareTitle($resolvedTitle, $appName);
            } else {
                $seoTitle = $resolvedTitle;
            }
        }

        $description = $pageModel->excerpt ?? '';
        $featuredImage = $pageModel->image->normal ?? null;

        if (!$resolvedTitle || !$description) {
            return $pageModel;
        }

        $this->setSeoMeta($seoTitle, $description, $url);
        $this->setOpenGraph($seoTitle, $description, $url, $featuredImage);
        $this->setTwitterCard($seoTitle, $description, $url, $featuredImage);
        $this->setJsonLd($seoTitle, $description, $url, $type, $pageModel, $featuredImage);

        SEOMeta::setCanonical(LaravelLocalization::getNonLocalizedURL($url));

        return $pageModel;
    }

    /**
     * 简单拼接标题和后缀
     */
    private function prepareTitle(string $title, string $suffix): string
    {
        return "{$title} - {$suffix}";
    }

    private function setSeoMeta(string $title, string $description, string $url): void
    {
        SEOMeta::setTitle($title)
            ->setDescription($description)
            ->addMeta('publisher', config('app.name'));
    }

    private function setOpenGraph(string $title, string $description, string $url, ?string $image): void
    {
        OpenGraph::setTitle($title)
            ->setDescription($description)
            ->setUrl($url)
            ->setSiteName(config('app.name'));

        // Use featured image if available, otherwise use default image from config
        $imagePath = $image ?: asset(config('seo.default_image', '/images/logo/v1/icon.png'));
        OpenGraph::addImage($imagePath);
    }

    private function setTwitterCard(string $title, string $description, string $url, ?string $image): void
    {
        TwitterCard::setType('summary_large_image')
            ->setTitle($title)
            ->setDescription($description)
            ->setUrl($url);

        // Use featured image if available, otherwise use default image from config
        $imagePath = $image ?: asset(config('seo.default_image', '/images/logo/v1/icon.png'));
        TwitterCard::setImage($imagePath);
    }

    private function setJsonLd(string $title, string $description, string $url, SeoType $type, PageModel $pageModel, ?string $image): void
    {
        // Retrieve social media options from cache
        $social_media_options = OptionCacheFacade::get(CacheKey::SocialMedia);

        // Extract social media links for sameAs and filter out empty or null links
        $sameAs = array_filter(array_column($social_media_options, 'link'), function ($link) {
            return !is_null($link) && $link !== '';
        });

        JsonLd::setTitle($title)
            ->setDescription($description)
            ->setUrl($url)
            ->setType($type->value)
            ->addValue('headline', $title)
            ->addValue('datePublished', $pageModel->created_at->toIso8601String())
            ->addValue('dateModified', $pageModel->updated_at->toIso8601String())
            ->addValue('publisher', [
                '@type' => 'Organization',
                'name' => config('app.name'),
                'url' => config('app.url'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('mallria-logo-transparent-white-bg.png')
                ],
                'sameAs' => array_values($sameAs), // Reset array keys to ensure sequential indexing
            ]);

        // Use featured image if available, otherwise use default image from config
        $imagePath = $image ?: asset(config('seo.default_image', '/images/logo/v1/icon.png'));
        JsonLd::addImage($imagePath);
    }
}