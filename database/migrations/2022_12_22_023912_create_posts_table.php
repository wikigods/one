<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
//            $table->string('cover')->nullable();
            $table->string('title')->nullable();
            $table->string('slug')->unique();
//            $table->text('excerpt')->nullable();
//            $table->longText('content')->nullable();
            //$table->timestamp('published_at')->nullable();
//            $table->foreignId('category_id')->nullable()->constrained();
//            $table->foreignId('user_id')->nullable()->constrained();
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
        Schema::dropIfExists('posts');
    }
};
