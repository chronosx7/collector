<?php

namespace App\Factories;

use App\Yugioh\YugiohDeck;

class DeckFactory{
    private $game;
    private $instance;

    public function __construct($game){
        $this->game = strtoupper($game);
        $this->instance = $this->get_instance();
    }

    private function get_instance(){
        switch($this->game){
            case 'YUGIOH':
            default:{
                $manager = new YugiohDeck();
                break;
            }
        }
        return $manager;
    }

    public function save($data){
        return $this->instance->save($data);
    }
}