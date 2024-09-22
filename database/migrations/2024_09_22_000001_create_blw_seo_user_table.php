<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    protected $connection = 'mysql';

    public function up(): void
    {
        Schema::create('blw_seo_user', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index(); // 本平台用户ID
            $table->string('platform')->index(); // 外部平台名称
            $table->string('ex_user_id')->index(); // 外部平台的用户ID
            $table->string('ex_user_name')->nullable(); // 外部平台的用户ID
            $table->text('ex_user_avatar')->nullable(); // 外部平台的用户ID
            $table->text('ex_user_url')->nullable(); // 外部平台的用户URL

            $table->timestamp('collected_at')->index();//上一次采集的时间

            $table->timestamps();

            // 添加一个唯一约束，确保每个用户在每个平台只能绑定一个外部用户
            $table->unique(['user_id', 'platform'], 'user_platform_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blw_seo_user');
    }
};
