<?php
namespace App\Factories;

use App\Yugioh\YugiohCard;

class DataFactory{
    private $game;
    private $instance;
    
    public function __construct($game){
        $this->game = strtoupper($game);
        $this->instance = $this->get_instance();
    }
    
    public function get_options(){
        return $this->instance->get_options();
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









