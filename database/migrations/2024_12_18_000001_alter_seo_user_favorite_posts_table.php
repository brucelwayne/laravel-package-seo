<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use MongoDB\Laravel\Schema\Blueprint;

return new class extends Migration {

    protected $connection = 'mongodb';

    public function up()
    {
        Schema::table('blw_user_favorite_seo_posts', function (Blueprint $table) {
            $table->id()->first();
        });
    }

    public function down()
    {
        Schema::table('blw_user_favorite_seo_posts', function (Blueprint $table) {
            $table->dropColumn('id');
        });
    }
};

