<?php

namespace Brucelwayne\SEO;

use Illuminate\Support\Arr;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class SEOMeta extends \Artesaos\SEOTools\SEOMeta
{
    function generate($minify = false)
    {
        $this->loadWebMasterTags();

        $title = $this->getTitle();
        $app_name = __(config('app.name'));
        $slogan = __(config('app.slogan'));

        if (empty($title)) {
            $title = $app_name . ' - ' . $slogan;
        }

        $description = $this->getDescription();
        $keywords = $this->getKeywords();
        $metatags = $this->getMetatags();
        $canonical = $this->getCanonical();
        $amphtml = $this->getAmpHtml();
        $prev = $this->getPrev();
        $next = $this->getNext();
        $languages = $this->getAlternateLanguages();
        $robots = $this->getRobots();

        $html = [];

        // Add title tag
        if ($title) {
            $html[] = Arr::get($this->config, 'add_notranslate_class', false) ? "<title class=\"notranslate\" inertia>$title</title>" : "<title inertia>$title</title>";
        }

        // Add description meta tag
        if ($description) {
            $html[] = "<meta name=\"description\" content=\"{$description}\">";
        }

        // Add keywords meta tag
        if (!empty($keywords)) {
            if ($keywords instanceof \Illuminate\Support\Collection) {
                $keywords = $keywords->toArray();
            }
            $keywords = implode(', ', $keywords);
            $html[] = "<meta name=\"keywords\" content=\"{$keywords}\">";
        }

        // Add other meta tags
        foreach ($metatags as $key => $value) {
            $name = $value[0];
            $content = $value[1];

            if (empty($content)) {
                continue;
            }

            $html[] = "<meta {$name}=\"{$key}\" content=\"{$content}\">";
        }

        // Add canonical link
        if ($canonical) {
            $html[] = "<link rel=\"canonical\" href=\"{$canonical}\">";
        }

        // Add AMP link
        if ($amphtml) {
            $html[] = "<link rel=\"amphtml\" href=\"{$amphtml}\">";
        }

        // Add prev and next links
        if ($prev) {
            $html[] = "<link rel=\"prev\" href=\"{$prev}\">";
        }
        if ($next) {
            $html[] = "<link rel=\"next\" href=\"{$next}\">";
        }

        // Dynamically add alternate languages
        $supportedLocales = LaravelLocalization::getSupportedLocales();
        $currentUrl = url()->current();

        foreach ($supportedLocales as $localeCode => $properties) {
            $localizedUrl = LaravelLocalization::getLocalizedURL($localeCode, $currentUrl);
            $this->addAlternateLanguage($localeCode, $localizedUrl);
            $html[] = "<link rel=\"alternate\" hreflang=\"{$localeCode}\" href=\"{$localizedUrl}\">";
        }

        // Add robots meta tag
        if ($robots) {
            $html[] = "<meta name=\"robots\" content=\"{$robots}\">";
        }

        // Return minified or formatted HTML
        return ($minify) ? implode('', $html) : implode(PHP_EOL, $html);
    }
}
