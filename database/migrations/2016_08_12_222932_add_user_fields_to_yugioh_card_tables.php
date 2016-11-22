<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserFieldsToYugiohCardTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('yugioh_cards_list', function($table){
            $table->integer('user_id')->default(-1);
        });
        Schema::table('yugioh_images_list', function($table){
            $table->integer('user_id')->default(-1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('yugioh_cards_list', function ($table) {
            $table->dropColumn('user_id');
        });
        Schema::table('yugioh_images_list', function ($table) {
            $table->dropColumn(['user_id', 'created_at', 'updated_at']);
        });
    }
}
