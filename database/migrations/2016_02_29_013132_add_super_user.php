<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSuperUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('authplus.tables.users', 'users'), function (Blueprint $table) {
            $table->boolean('super_user')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('authplus.tables.users', 'users'), function (Blueprint $table) {
            $table->dropColumn('super_user');
        });
    }
}
