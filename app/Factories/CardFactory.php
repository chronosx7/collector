<?php

namespace App\Factories;

use App\Yugioh\YugiohCard;

class CardFactory{
    private $game;
    private $instance;
    
    public function __construct($game){
        $this->game = strtoupper($game);
        $this->instance = $this->get_instance();
    }
    
    public function save($data){
        return $this->instance->save($data);
    }
    
    public function search($data){
        return $this->instance->search($data);
    }

    public function edit($id){
        return $this->instance->edit($id);
    }
    
    public function update($data){
        return $this->instance->update($data);
    }
    
    public function card_info($id){
        return $this->instance->card_info($id);
    }

    public function user_cards($id){
        return $this->instance->user_cards($id);
    }
    
    private function get_instance(){
        switch($this->game){
            case 'YUGIOH':
            default:{
                $manager = new YugiohCard();
                break;
            }
        }
        return $manager;
    }
}