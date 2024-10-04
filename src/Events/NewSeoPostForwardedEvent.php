<?php

namespace Brucelwayne\SEO\Events;

use Brucelwayne\SEO\Jobs\TranslatePostJob;
use Brucelwayne\SEO\Models\SeoPostModel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Mallria\Shop\Models\TranslatablePostModel;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class NewSeoPostForwardedEvent
{
    use Dispatchable, SerializesModels;

    /**
     * @param SeoPostModel $seo_post
     * @param TranslatablePostModel $post
     */
    public function __construct(public $seo_post, public $post)
    {
        $defaultLocale = $this->post->getDefaultLocale() ?? App::getLocale();
        $supported_locales = LaravelLocalization::getSupportedLocales();
        foreach ($supported_locales as $locale => $supported_locale) {
            if ($locale === $defaultLocale) {
                continue;
            }
            TranslatePostJob::dispatch(post_id: $this->post->getKey(), locale: $locale);
        }
    }
}
