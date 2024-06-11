<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastOnlineAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('model', function (Blueprint $table) {
            $table->timestamp('last_online_at')->nullable();
            $table->dropIndex("nick_ex_id");
        });

        Schema::table('model', function (Blueprint $table) {
            $table->unique(['nick','external_id'], 'nick_external_id');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
