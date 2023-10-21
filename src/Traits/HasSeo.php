<?php

namespace Brucelwayne\SEO\Traits;

use Brucelwayne\SEO\Models\SeoModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property  SeoModel $seo
 */
trait HasSeo
{

    protected ?SeoModel $observed_seo_model;

    protected bool $seo_observed = false;

    function seo()
    {
        $model_name = get_class($this);
        if (empty($this->observed_seo_model)){
            if (!empty($this->seo)) {
                $this->observed_seo_model = $this->seo;
            }
            if (empty($this->observed_seo_model)) {
                $this->observed_seo_model = new SeoModel();
                $this->observed_seo_model->model = $model_name;
            }
        }

        if (!$this->seo_observed) {
            $callback = function ($model) {
                /**
                 * @var Model $model
                 */
                $this->observed_seo_model->model_id = $model->getKey();
                $this->observed_seo_model->save();
            };
            /**
             * @var Model $model_name
             */
            $model_name::created($callback);
            $model_name::updated($callback);
            $model_name::saved($callback);
            $this->seo_observed = true;
        }
        return $this->observed_seo_model;
    }

    function getSeoAttribute(){
        if (empty($this->getKey())){
            return null;
        }
        return SeoModel::where([
            'model' => get_class($this),
            'model_id'=>$this->getKey(),
        ])->first();
    }

    function getSeoTitle($default=''){
        if (!empty($this->seo->title)){
            return $this->seo->title;
        }
        return $default;
    }

    function getSeoDescription($default=''){
        if (!empty($this->seo->description)){
            return $this->seo->description;
        }
        return $default;
    }

}