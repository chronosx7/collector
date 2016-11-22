<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'validated', 'uses_social', 
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function get_user_by_email($email){
        $user = DB::table('users')
        ->select('id', 'name', 'email', 'validated')
        ->where('email', $email)->get();
        if(count($user) > 0){
            $res = $user[0];
        }
        else{
            $res = null;
        }
        return $res;
    }
    
    public function check_user_exists($name='', $email=''){
        $res = false;
        $val = '';
        switch($val){
            case '':{
                if($name != ''){
                    $res = $this->user_name_exists($name);
                    if($res){
                        break;
                    }
                }
                if($emai != ''){
                    $res2 = $this->user_email_exists($email);
                    if($res){
                        break;
                    }
                }
            }
        }
        return $res;
    }
    
    public function is_validated($id){
        return DB::table('users')->select('validated')
        ->where('id', $id)->get();
    }
    
    public function user_name_exists($name){
        $query = DB::table('users')->select('id');
        $query->where('name', $name);
        $result = $query->get();
        
        if(count($result) > 0){
            $res = true;
        }
        else{
            $res = false;
        }
        return $res;
    }
    
    public function user_email_exists($mail){
        $query = DB::table('users')->select('id');
        $query->where('email', $mail);
        $result = $query->get();
        
        if(count($result) > 0){
            $res = true;
        }
        else{
            $res = false;
        }
        return $res;
    }

    /**
    * Get random user name
    * 
    * @param String $name Name to use as a base for a random non-existing name.
    * 
    * @return String Name certified to not be present in the database.
    */
    public function get_random_name($base_name=''){
        $valid = false;
        if($base_name != ''){
            do{
                $key = $this->get_random_key();
                $name = $base_name . '_' . $key;
                $valid = $this->user_name_exists($name);
            }while(!$valid);
        }
        else{
            do{
                $prefix = $this->optional_prefixes[rand(0, count($this->optional_prefixes) - 1)];
                $title = $this->optional_names[rand(0, count($this->optional_names) - 1)];
                $key = $this->get_random_key();
                $name = $prefix . '_' . $title . '_' . $key;
                $valid = $this->user_name_exists($name);
            }while(!$valid);
        }
        return $name;
    }
    
    public function get_random_key(){
        return rand(1, 9999);
    }
    
    private $optional_names = array(
        'user',
        'dragon',
        'centaur',
        'gryphon',
        'elf',
        'chimera',
        'halberd',
        'shield',
        'sword',
        'dagger',
        'helm',
        'titan',
        'golem',
        'colosus',
        'phalanx',
        'scythe',
        'rapier',
        'chariot',
        'grimoire',
    );
    
    private $optional_prefixes = array(
        'brave',
        'tenacious',
        'honest',
        'clever',
        'resolute',
        'daring',
        'loyal',
        'mysterious',
    );
    
    // get random user password
    public function get_random_password(){
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 15; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
}










