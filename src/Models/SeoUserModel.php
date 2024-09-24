<?php

namespace Brucelwayne\SEO\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Mallria\Core\Models\BaseMysqlModel;
use Mallria\Core\Models\User;
use Mallria\Shop\Models\ExternalPostModel;
use Veelasky\LaravelHashId\Eloquent\HashableId;

/**
 * Class SeoUserModel
 *
 * @property int $id 主键ID
 * @property int $user_id 关联的本平台用户ID
 * @property bool $available 该账号是否启用
 * @property string $platform 外部平台名称
 * @property string|null $ex_user_id 外部平台用户ID
 * @property string|null $ex_user_name 外部平台用户名
 * @property string|null $ex_user_avatar 外部平台用户头像
 * @property string|null $scrap_user_url 采集的用户地址
 * @property Carbon|null $scrap_at 上次采集的时间
 * @property Carbon|null $created_at 创建时间
 * @property Carbon|null $updated_at 更新时间
 * @property-read User $user 关联的本平台用户
 * @property-read Collection|ExternalPostModel[] $externalPosts 关联的外部帖子
 */
class SeoUserModel extends BaseMysqlModel
{
    const TABLE = 'blw_seo_user';

    use HashableId;

    protected $table = self::TABLE;
    protected $hashKey = self::TABLE;

    protected $appends = ['hash'];

    protected $fillable = [
        'user_id',
        'available',
        'platform',
        'ex_user_id',
        'ex_user_name',
        'ex_user_avatar',
        'scrap_user_url',
        'scrap_at',
    ];

    protected $casts = [
        'available' => 'boolean',
        'scrap_at' => 'datetime',
    ];

    public function getRouteKeyName()
    {
        return 'hash';
    }

    /**
     * 获取关联的用户信息
     *
     * 这是一个多对一的关系：一个 BlwSeoUser 记录属于一个 User
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 获取关联的外部帖子
     *
     * 这是一个一对多的关系：一个 SeoUserModel 记录可以有多个 ExternalPostModel
     *
     * @return HasMany
     */
    public function externalPosts(): HasMany
    {
        return $this->hasMany(ExternalPostModel::class, 'seo_user_id', 'id');
    }

    public function toSearchableArray()
    {
        return $this->toArray();
    }
}
