<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogRss extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rss__log', function (Blueprint $table) {
            $table->id();
            $table->timestamp('requestDate');
            $table->string('requestMethod')->nullable();
            $table->string('requestURL')->nullable();
            $table->integer('response_HTTP_code')->nullable();
            $table->longText('responseBody')->nullable();
            $table->float('executionTime')->nullable();
            $table->integer('active')->default(0)->comment('0 - в обработке, 1 - завершен');
            $table->string('error')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_rss');
    }
}
