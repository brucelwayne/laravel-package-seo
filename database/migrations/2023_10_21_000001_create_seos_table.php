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

            $table->integer('type')->index();//article webpage book profile etc...
            $table->text('url');
            $table->text('image_url');
            $table->text('canonical');
            $table->text('title');
            $table->text('keywords');
            $table->text('description');
            $table->text('coordinate');
            $table->text('payload');

            $table->timestamps();

            $table->unique(['model', 'model_id'], 'model_unique');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seos');
    }
};
