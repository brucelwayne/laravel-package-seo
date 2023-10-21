<?php

namespace Brucelwayne\SEO\Traits;

use Brucelwayne\SEO\Models\SeoModel;
use Brucelwayne\SEO\SeoType;
use Illuminate\Database\Eloquent\Model;

trait HasSeo
{

    protected SeoModel $seo_model;

    protected bool $observed = false;

    function seo()
    {
        $model_name = get_class($this);
        if ($this->exists) {
            //model exists
            $this->seo_model = SeoModel::where([
                'model' => $model_name,
                'model_id' => $this->getKey(),
            ])->first();
        }
        if (empty($this->seo_model)) {
            $this->seo_model = new SeoModel();
        }

        if (!$this->observed) {
            $callback = function ($model) {
                /**
                 * @var Model $model
                 */
                $this->seo_model->model_id = $model->getKey();
                $this->seo_model->save();
            };
            /**
             * @var Model $model_name
             */
            $model_name::created($callback);
            $model_name::updated($callback);
            $model_name::saved($callback);
            $this->observed = true;
        }
        return $this->seo_model;
    }


}