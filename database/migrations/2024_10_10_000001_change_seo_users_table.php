<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    protected $connection = 'mysql';

    public function up(): void
    {
        Schema::table('blw_seo_user', function (\Illuminate\Database\Schema\Blueprint $table) {
            // 修改字段名称并添加索引
            $table->renameColumn('ex_user_id', 'fk_user_id');
            $table->renameColumn('ex_user_name', 'fk_user_name');
            $table->renameColumn('ex_user_avatar', 'fk_user_avatar');

            // 确保字段有索引
            $table->index('fk_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('blw_seo_user', function (\Illuminate\Database\Schema\Blueprint $table) {
            // 回滚时将字段名还原
            $table->renameColumn('fk_user_id', 'ex_user_id');
            $table->renameColumn('fk_user_name', 'ex_user_name');
            $table->renameColumn('fk_user_avatar', 'ex_user_avatar');

            // 删除新增的索引
            $table->dropIndex(['fk_user_id']);
        });
    }
};
