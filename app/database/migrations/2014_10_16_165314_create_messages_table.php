<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function($table) {
            $table->increments('id');
            $table->integer('user_sender_id');
            $table->integer('user_receiver_id');
            $table->dateTime('created_at');


            // Indexes
            $table->index('user_sender_id');
            $table->index('user_receiver_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('messages');
    }

}
