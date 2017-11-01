<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('authplus.tables.group_users', 'group_users'), function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('group_id')->unsigned();
            $table->unique(['user_id', 'group_id']);
            $table->foreign('user_id')
                  ->references('id')->on(config('authplus.tables.users', 'users'))
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreign('group_id')
                  ->references('id')->on(config('authplus.tables.groups', 'groups'))
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('authplus.tables.group_users'));
    }
}
