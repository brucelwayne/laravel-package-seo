<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use MongoDB\Laravel\Schema\Blueprint;

return new class extends Migration {

    protected $connection = 'mongodb';

    public function up()
    {
        Schema::create('seo_favorite_quick_category', function (Blueprint $table) {
            $table->id();
            $table->string('scene')->index();
            $table->unsignedBigInteger('category_id')->index();
            $table->timestamps();
        });
        Schema::create('seo_post_quick_category', function (Blueprint $table) {
            $table->id();
            $table->string('action')->nullable()->index();
            $table->string('seo_post_id')->index();
            $table->string('quick_category_id')->index();
            $table->unsignedBigInteger('category_id')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seo_post_quick_category');
        Schema::dropIfExists('seo_favorite_quick_category');
    }
};
