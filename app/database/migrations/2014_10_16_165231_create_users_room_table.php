<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersRoomTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_room', function($table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('room_id');
            $table->integer('status');
            $table->dateTime('created_at');

            // Indexes
            $table->index('user_id');
            $table->index('room_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_room');
    }
}
