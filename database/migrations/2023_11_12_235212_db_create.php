<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DbCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('model')) {
            Schema::create('model', function (Blueprint $table) {
                $table->id();
                $table->string('nick');
                $table->integer('external_id')->nullable();
                $table->integer('vendor_id')->nullable();
                $table->boolean('is_active')->nullable()->default(1);
                $table->boolean('profile_hidden')->nullable()->default(false);
                $table->boolean('album_hidden')->nullable()->default(false);
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('category')) {
            Schema::create('category', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->integer('external_id')->nullable();
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('model_data')) {
            Schema::table('model_data', function (Blueprint $table) {
                $table->id();
                $table->integer('model_id');
                $table->string('type');
                $table->text('value');
                $table->timestamps();
                $table->index(['model_id', 'type'], 'model_type');
                $table->fullText(['type', 'value'], 'type_value');
            });
        }

        if(!Schema::hasTable('model_to_category')) {
            Schema::create('model_to_category', function (Blueprint $table) {
                $table->id();
                $table->integer('model_id');
                $table->integer('category_id');

                $table->index(['model_id', 'category_id'], 'model_cat');
                $table->index(['category_id', 'model_id'], 'cat_model');
            });
        }
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
