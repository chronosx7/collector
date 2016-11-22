<?php

namespace App\Yugioh;

use Illuminate\Database\Eloquent\Model;
use Validator;
use Carbon\Carbon;
use DB;

class YugiohDeck extends Model
{
    //

    public function save(array $data = array()){
        $error_list = array();
        $val = '';
        switch($val){
            case '':{
                $validator = Validator::make($data, [
                    'user_id' => 'integer|required',
                    'deck_name' => 'string|required',
                ], array(
                    'user_id.number' => 'Invalid User ID.',
                    'user_id.required' => 'Every deck must be related to a user.',
                    'deck_name.string' => 'Invalid Deck Name.',
                    'deck_name.required' => 'Every deck must have a name.',
                ));
                if($validator->fails()){
                    $error_list = array_merge($error_list, $validator->errors()->all());
                }
                if(!array_key_exists('main_deck', $data)){
                    $error_list[] = 'No data for Main Deck cards.';
                }
                if(!array_key_exists('extra_deck', $data)){
                    $error_list[] = 'No data for Extra Deck cards.';
                }
                if(!array_key_exists('side_deck', $data)){
                    $error_list[] = 'No data for Side Deck cards.';
                }

                if(count($error_list) > 0){
                    $status = 'error';
                    break;
                }

                try{
                    $this->save_deck($data);
                    $status = 'ok';
                }
                catch(Exception $e){
                    $status = 'error';
                    $error_list[] = $e->getMessage();
                }
            }
        }

        return (object)array(
            'status' => $status,
            'errors' => $error_list,
        );
    }

    public function deck_exists($user_id, $deck_name){
        $res = -1;
        $deck = DB::table('yugioh_deck_list')->select(array('id'))->where([
            ['user_id', $user_id],
            ['name', $deck_name],
            ['active', true],
        ])->take(1)->get();

        if(count($deck) > 0){
            $res = $deck[0]->id;
        }

        return $res;
    }
    
    private function save_deck($data){
        $data['deck_name'] = ucwords(strtolower($data['deck_name']));
        $data['deck_name'] = preg_replace("/\s{2,}/", ' ', $data['deck_name']);
        
        $deck_id = $this->deck_exists($data['user_id'], $data['deck_name']);
        DB::transaction(function()use($data, $deck_id){
            if($deck_id < 0){
                $deck_id = DB::table('yugioh_deck_list')->insertGetId(array(
                    'name' => $data['deck_name'],
                    'user_id' => $data['user_id'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ));
            }
            else{
                DB::table('yugioh_deck_list')->where('id', $deck_id)->update(array(
                    'name' => $data['deck_name'],
                    'updated_at' => Carbon::now(),
                ));

                DB::table('yugioh_main_deck_cards')->where('deck_id', $deck_id)->delete();
                DB::table('yugioh_extra_deck_cards')->where('deck_id', $deck_id)->delete();
                DB::table('yugioh_side_deck_cards')->where('deck_id', $deck_id)->delete();
            }

            $cards = array();
            foreach($data['main_deck'] as $card){
                $cards[] = array(
                    'deck_id' => $deck_id,
                    'card_id' => $card,
                );
            }
            DB::table('yugioh_main_deck_cards')->insert($cards);

            $cards = array();
            foreach($data['extra_deck'] as $card){
                $cards[] = array(
                    'deck_id' => $deck_id,
                    'card_id' => $card,
                );
            }
            DB::table('yugioh_extra_deck_cards')->insert($cards);

            $cards = array();
            foreach($data['side_deck'] as $card){
                $cards[] = array(
                    'deck_id' => $deck_id,
                    'card_id' => $card,
                );
            }
            DB::table('yugioh_side_deck_cards')->insert($cards);
        });
    }

}
