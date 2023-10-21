<?php

use Brucelwayne\Blog\Enums\BlogStatus;
use Brucelwayne\Blog\Enums\BlogType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    protected $connection = 'mysql';

    public function up(): void
    {
        Schema::create('seos', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->string('model')->index();
            $table->string('model_id')->index();

            $table->string('type')->index();//article webpage book profile etc...
            $table->text('url')->nullable();
            $table->text('image_url')->nullable();
            $table->text('canonical')->nullable();
            $table->text('title')->nullable();
            $table->text('keywords')->nullable();
            $table->text('description')->nullable();
            $table->text('coordinate')->nullable();
            $table->text('payload')->nullable();

            $table->timestamps();

            $table->unique(['model', 'model_id'], 'model_unique');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seos');
    }
};
