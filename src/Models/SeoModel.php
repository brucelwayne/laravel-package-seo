<?php

namespace Brucelwayne\SEO\Models;

use Brucelwayne\SEO\Enums\SeoType;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * @property string $model
 * @property string $model_id
 * @property string $type
 * @property string $url
 * @property string $image_url
 * @property string $canonical
 * @property string $title
 * @property string $keywords
 * @property string $description
 * @property string $coordinate
 * @property string $payload
 * @property string $created_at
 * @property string $updated_at
 *
 *
 */
class SeoModel extends Model
{
    use HasTranslations;

    protected $table = 'blw_seos';

    public $translatable = [
        'url',
        'image_url',
        'canonical',
        'title',
        'keywords',
        'description',
    ];

    protected $fillable = [
        'model',
        'model_id',
        'type',
        'url',
        'image_url',
        'canonical',
        'title',
        'keywords',
        'description',
        'coordinate',
        'payload',
    ];

    protected $casts = [
        'type' => SeoType::class,
    ];


    function setType(SeoType $type): static
    {
        $this->type = $type;
        return $this;
    }

    function setUrl($url): static
    {
        $this->url = $url;
        return $this;
    }

    function setImageUrl($image_url): static
    {
        $this->image_url = $image_url;
        return $this;
    }

    function setCanonical($canonical, $locale = ''): static
    {
        if (empty($locale)) {
            $locale = config('app.locale');
        }
        $this->setTranslation('canonical', $locale, $canonical);
        return $this;
    }

    function setTitle($title, $locale = ''): static
    {
        if (empty($locale)) {
            $locale = config('app.locale');
        }
        $this->setTranslation('title', $locale, $title);
        return $this;
    }

    function setKeywords($keywords, $locale = ''): static
    {
        if (empty($locale)) {
            $locale = config('app.locale');
        }
        $this->setTranslation('keywords', $locale, $keywords);
        return $this;
    }

    function setDescription($description, $locale = ''): static
    {
        if (empty($locale)) {
            $locale = config('app.locale');
        }
        $this->setTranslation('description', $locale, $description);
        return $this;
    }

    function setCoordinate($coordinate): static
    {
       $this->coordinate = $coordinate;
       return $this;
    }

    function setPayload($payload): static
    {
        $this->payload = $payload;
        return $this;
    }
}