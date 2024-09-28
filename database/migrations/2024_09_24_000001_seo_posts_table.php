<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use MongoDB\Laravel\Schema\Blueprint;

return new class extends Migration {

    protected $connection = 'mongodb';

    public function up()
    {
        Schema::create('blw_seo_posts', function (Blueprint $table) {
            $table->index('platform'); // 为 platform 字段创建索引
            $table->index('seo_user_id');
            $table->index('fk_id'); // 为 fk_id 字段创建索引
            $table->index('created_at'); // 为 created_at 字段创建索引
            $table->index('updated_at');
            $table->index('converted_at');
            $table->index('converted_post_id');
            $table->index(['fk_id', 'platform']); // 为 fk_id 和 platform 创建复合索引
        });
        Schema::create('blw_seo_media', function (Blueprint $table) {
            $table->index('post_id'); // 为 ex_post_id 字段创建索引
            $table->string('tag');//这个是图片还是视频 image, video
            $table->index('src'); // 为 image_id 字段创建索引
            $table->index('created_at'); // 为 created_at 字段创建索引
        });
    }

    public function down()
    {
        Schema::drop('blw_seo_posts');
        Schema::drop('blw_seo_media');
    }
};

