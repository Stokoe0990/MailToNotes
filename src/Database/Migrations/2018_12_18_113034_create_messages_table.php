<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('message_id');
            $table->string('context_id');
            $table->string('sender');
            $table->string('subject');
            $table->longText('recipients');
            $table->string('in_reply_to')->nullable();
            $table->longText('references')->nullable();
            $table->string('context_uri');
            $table->longText('body')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
