<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMobilesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobiles', function($table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('mobile_id', 250);
            $table->dateTime('created_at');

            $table->index('user_id');
            $table->index('mobile_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('mobiles');
    }

}
