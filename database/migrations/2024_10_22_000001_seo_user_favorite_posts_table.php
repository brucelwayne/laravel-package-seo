<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use MongoDB\Laravel\Schema\Blueprint;

return new class extends Migration {

    protected $connection = 'mongodb';

    public function up()
    {
        Schema::create('blw_user_favorite_seo_posts', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('seo_post_id');
            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    public function down()
    {
        Schema::drop('blw_user_favorite_seo_posts');
    }
};

