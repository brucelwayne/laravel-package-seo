<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seo_indexed', function (Blueprint $table) {
            $table->id();
            $table->morphs('model');
            $table->string('locale')->nullable()->index();
            $table->text('url')->nullable();
            $table->timestamp('google_indexed_at')->nullable();
            $table->longText('response')->nullable();
            $table->longText('payload')->nullable();
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
        Schema::dropIfExists('seo_indexed');
    }
};
