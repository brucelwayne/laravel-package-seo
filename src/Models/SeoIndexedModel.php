<?php

namespace Brucelwayne\SEO\Models;


use Carbon\Carbon;
use Mallria\Core\Models\BaseMysqlModel;

/**
 * @property string $url
 * @property Carbon $google_indexed_at
 * @property string response
 * @property array payload
 */
class SeoIndexedModel extends BaseMysqlModel
{
    const TABLE = 'seo_indexed';

    protected $table = self::TABLE;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'model_type',
        'model_id',
        'url',
        'google_indexed_at',
        'response',
        'payload',
    ];


    protected $casts = [
        'payload' => 'array',
    ];

    protected $dates = [
        'google_indexed_at',
    ];

    /**
     * Get the parent model associated with this SEO record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function model()
    {
        return $this->morphTo();
    }
}
