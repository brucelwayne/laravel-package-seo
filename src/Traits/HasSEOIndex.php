<?php

namespace Brucelwayne\SEO\Traits;

use Brucelwayne\SEO\Models\SeoIndexedModel;

/**
 * @property SeoIndexedModel $seoIndex
 */
trait HasSEOIndex
{
    public function seoIndex()
    {
        return $this->morphOne(SeoIndexedModel::class, 'model');
    }
}
