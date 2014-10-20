<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFriendsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('friends', function($table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('user_friend_id');
            $table->integer('status');
            $table->dateTime('created_at');

            // Indexes
            $table->index('user_id');
            $table->index('user_friend_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('friends');
    }

}
