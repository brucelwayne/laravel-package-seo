<?php

namespace Brucelwayne\SEO\Traits;

use Brucelwayne\SEO\Events\SearchEngineIndexEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Mallria\Core\Http\Responses\SuccessJsonResponse;
use Mallria\Core\Models\PageModel;
use Mallria\Shop\Models\TransPostModel;
use Mallria\Shop\Models\TransProductModel;

trait HasSEOIndexRequest
{
    function seoIndex(Request $request)
    {
        /**
         * @var PageModel|TransPostModel|TransProductModel|null $model
         */
        $model = null;
        $url = null;
        if (!empty($request->post('page'))) {
            $model = PageModel::byHashOrFail($request->post('page'));
            $url = route($model->route);
        } elseif (!empty($request->post('post'))) {
            $model = TransPostModel::byHashOrFail($request->post('post'));
            $url = route('sparkle.single', $model);
        } elseif (!empty($request->post('product'))) {
            $model = TransProductModel::byHashOrFail($request->post('product'));
            $url = route('product.single', $model);
        }

        if ($url) {
            event(new SearchEngineIndexEvent($model, App::getLocale(), $url, 'google'));
        }

        return new SuccessJsonResponse();
    }
}