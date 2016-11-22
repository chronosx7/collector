<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FirstYugiohSchema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yugioh_cards_list', function(Blueprint $table){
            $table->increments('id');
            $table->string('name', 50);
            $table->string('description', 250);
            $table->tinyInteger('class');
            $table->tinyInteger('spell_type');
            $table->boolean('active')->default(true);
            $table->boolean('official');
            $table->string('card_type', 9)->default('MainCard');
            $table->timestamps();
        });
        Schema::create('yugioh_families_list', function(Blueprint $table){
            $table->increments('id');
            $table->string('family');
        });
        Schema::create('yugioh_monster_types_list', function(Blueprint $table){
            $table->increments('id');
            $table->string('type');
        });
        Schema::create('yugioh_monster_attributes_list', function(Blueprint $table){
            $table->increments('id');
            $table->string('attribute');
        });
        Schema::create('yugioh_spell_types_list', function(Blueprint $table){
            $table->increments('id');
            $table->string('type');
        });
        Schema::create('yugioh_card_classes_list', function(Blueprint $table){
            $table->increments('id');
            $table->string('class');
        });
        Schema::create('yugioh_images_list', function(Blueprint $table){
            $table->increments('id');
            $table->string('original_file', 250);
            $table->string('active_file', 250);
            $table->boolean('active')->default(true);
        });
        Schema::create('yugioh_monster_data', function(Blueprint $table){
            $table->integer('monster_id');
            $table->tinyInteger('attribute');
            $table->tinyInteger('level');
            $table->tinyInteger('type');
            $table->tinyInteger('left_scale');
            $table->tinyInteger('right_scale');
            $table->string('pendulum_effect', 250);
            $table->string('attack', 4);
            $table->string('defense', 4);
        });
        Schema::create('yugioh_monster_families', function(Blueprint $table){
            $table->integer('monster_id');
            $table->integer('family_id');
        });
        Schema::create('yugioh_card_images', function(Blueprint $table){
            $table->integer('card_id');
            $table->integer('image_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('yugioh_cards_list');
        Schema::drop('yugioh_families_list');
        Schema::drop('yugioh_monster_types_list');
        Schema::drop('yugioh_monster_attributes_list');
        Schema::drop('yugioh_spell_types_list');
        Schema::drop('yugioh_card_classes_list');
        Schema::drop('yugioh_images_list');
        Schema::drop('yugioh_monster_data');
        Schema::drop('yugioh_monster_families');
        Schema::drop('yugioh_card_images');
    }
}
