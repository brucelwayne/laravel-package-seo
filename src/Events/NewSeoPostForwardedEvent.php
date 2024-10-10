<?php

namespace Brucelwayne\SEO\Events;

use Brucelwayne\SEO\Jobs\TranslatePostJob;
use Brucelwayne\SEO\Models\SeoPostModel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Mallria\Shop\Models\TranslatablePostModel;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class NewSeoPostForwardedEvent
{
    use Dispatchable, SerializesModels;

    /**
     * @param SeoPostModel $seo_post
     * @param TranslatablePostModel $post
     */
    public function __construct(public $seo_post, public $post, public $locale = 'zh')
    {
        $supported_locales = LaravelLocalization::getSupportedLocales();
        foreach ($supported_locales as $_locale => $supported_locale) {
            if ($_locale === $locale) {
                continue;
            }
            TranslatePostJob::dispatch(post_id: $this->post->getKey(), locale: $_locale);
        }
    }
}
