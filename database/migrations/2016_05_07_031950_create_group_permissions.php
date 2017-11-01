<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('authplus.tables.group_permissions', 'group_permissions'),
            function (Blueprint $table) {
                $table->increments('id');
                $table->integer('group_id')->unsigned();
                $table->string('resource')->comment('Model name');
                $table->string('subresource')->default('*');
                $table->string('ability', 8)->comment('CRUD Action');
                $table->boolean('authorized');
                $table->unique(['group_id', 'resource', 'subresource', 'ability']);

                $table->foreign('group_id')
                      ->references('id')->on(config('authplus.tables.groups', 'groups'))
                      ->onDelete('cascade')
                      ->onUpdate('cascade');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('authplus.tables.group_permissions', 'group_permissions'));
    }
}
