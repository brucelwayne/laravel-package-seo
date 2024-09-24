<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    protected $connection = 'mysql';

    public function up(): void
    {
        Schema::create('blw_seo_user', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index()->comment('关联的本平台id'); // 本平台用户ID
            $table->boolean('available')->default(false)->index()->comment('该账号是否启用'); // 是否可用
            $table->string('platform')->index()->comment('来自哪个平台'); // 外部平台名称
            $table->string('ex_user_id')->nullable()->index()->comment('外部的id');
            $table->string('ex_user_name')->nullable()->comment('名字');
            $table->text('ex_user_avatar')->nullable()->comment('头像');
            $table->text('scrap_user_url')->nullable()->comment('采集的地址');
            $table->timestamp('scrap_at')->index()->comment('上次采集的时间');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blw_seo_user');
    }
};
