<?php

namespace Brucelwayne\SEO\Traits;

use Artesaos\SEOTools\Facades\{JsonLd, OpenGraph, SEOMeta, TwitterCard};
use Brucelwayne\SEO\Enums\SeoType;
use Mallria\Core\Models\PageModel;
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
    public function setPageMeta(string $domain, string $route, SeoType $type = SeoType::WebPage, string $title = ''): ?PageModel
    {
        $pageModel = PageModel::byDomainRoute($domain, $route);
        $url = route($route);
        $appName = config('app.name');
        $slogan = config('app.slogan');

        if (!$pageModel) {
            $seoTitle = $title
                ? $this->prepareTitle($title, $appName)
                : $this->prepareTitle($appName, $slogan);

            SEOMeta::setTitle($seoTitle);
            return null;
        }

        // 优先使用 PageModel 的标题，否则回退到传入的 $title，再到 appName
        $resolvedTitle = $pageModel->title ?: ($title ?: $appName);
        $seoTitle = $this->prepareTitle($resolvedTitle, $appName);
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

        if ($image) {
            OpenGraph::addImage($image);
        }
    }

    private function setTwitterCard(string $title, string $description, string $url, ?string $image): void
    {
        TwitterCard::setType('summary_large_image')
            ->setTitle($title)
            ->setDescription($description)
            ->setUrl($url);

        if ($image) {
            TwitterCard::setImage($image);
        }
    }

    private function setJsonLd(string $title, string $description, string $url, SeoType $type, PageModel $pageModel, ?string $image): void
    {
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
            ]);

        if ($image) {
            JsonLd::addImage($image);
        }
    }
}