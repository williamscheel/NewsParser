<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rss__news', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('shortDescription');
            $table->timestamp('pubDate');
            $table->string('author')->nullable();;
            $table->string('image')->nullable();;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rss_news');
    }
}
