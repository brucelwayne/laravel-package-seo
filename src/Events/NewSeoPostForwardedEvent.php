<?php

namespace Brucelwayne\SEO\Events;

use Brucelwayne\SEO\Models\SeoPostModel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Mallria\Shop\Models\MallriaPostModel;

class NewSeoPostForwardedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(public SeoPostModel $seo_post, public MallriaPostModel $post)
    {
    }
}
