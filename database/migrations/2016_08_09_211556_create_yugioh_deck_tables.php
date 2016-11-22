<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYugiohDeckTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yugioh_deck_list', function(Blueprint $table){
            $table->increments('id');
            $table->integer('user_id');
            $table->string('name');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        Schema::create('yugioh_main_deck_cards', function(Blueprint $table){
            $table->integer('deck_id');
            $table->integer('card_id');
        });
        Schema::create('yugioh_extra_deck_cards', function(Blueprint $table){
            $table->integer('deck_id');
            $table->integer('card_id');
        });
        Schema::create('yugioh_side_deck_cards', function(Blueprint $table){
            $table->integer('deck_id');
            $table->integer('card_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('yugioh_deck_list');
        Schema::drop('yugioh_main_deck_cards');
        Schema::drop('yugioh_extra_deck_cards');
        Schema::drop('yugioh_side_deck_cards');
    }
}
