<?php

namespace App\Yugioh;

use Illuminate\Database\Eloquent\Model;
use App\Libs\ImageFile;
use App\Libs\DataArray;
use Validator;
use Carbon\Carbon;
use DB;
use Log;

class YugiohCard extends Model
{
    private $img_tag = '__svr_img_';
    
    public function get_options(){
        $card_classes = DB::table('yugioh_card_classes_list')->select('id', 'class')->get();
        
        $monster_attributes = DB::table('yugioh_monster_attributes_list')->select(
            'id', 'attribute')->get();
            
        $monster_types = DB::table('yugioh_monster_types_list')->select('id', 'type')->get();
        
        $monster_families = DB::table('yugioh_families_list')->select('id', 'family')->get();
        
        $spell_types = DB::table('yugioh_spell_types_list')->select('id', 'type')
        ->where('type', '!=', 'Counter')->get();
        
        $trap_types = DB::table('yugioh_spell_types_list')->select('id', 'type')
        ->whereIn('type', ['Normal', 'Continuous', 'Counter'])->get();
        
        return array(
            'card_classes' => $card_classes,
            'monster_attributes' => $monster_attributes,
            'monster_types' => $monster_types,
            'monster_families' => $monster_families,
            'spell_types' => $spell_types,
            'trap_types' => $trap_types,
        );
    }
    
    public function save(array $data = array()){
        $data = $this->format_strings($data);

        // Perform validations based on card class (Monster, Spell or Trap)
        $error_list = $this->validate_card_data($data);

        // Validate card name uniqueness only when creating a card
        if(isset($data['card_name']) && $this->card_name_repeated($data['card_name'])){
            $error_list[] = 'Card names must be unique.';
            $error_list[] = 'The card "' . $data['card_name'] . '" already exists.';
        }

        if(count($error_list) < 1){
            switch($data['class']){
                case '1':{
                    try{
                        $this->create_monster($data);
                        $status = 'ok';
                    }
                    catch(Exception $e){
                        $error_list[] = $e->getMessage();
                        $status = 'error';
                    }
                    break;
                }
                case '2':{
                    try{
                        $this->create_spell($data, 'SPELL');
                        $status = 'ok';
                    }
                    catch(Exception $e){
                        $error_list[] = $e->getMessage();
                        $status = 'error';
                    }
                    break;
                }
                case '3':{
                    try{
                        $this->create_spell($data, 'TRAP');
                        $status = 'ok';
                    }
                    catch(Exception $e){
                        $error_list[] = $e->getMessage();
                        $status = 'error';
                    }
                    break;
                }
            }
        }
        else{
            $status = 'error';
        }
        return (object)array(
            'status' => $status,
            'errors' => $error_list,
        );
    }
    
    public function update(array $data = array(), array $options = array()){
        $data = $this->format_strings($data);
        $error_list = array();

        if(!DataArray::has_non_empty_key($data, 'id')){
            $error_list[] = 'Invalid card Id.';
        }
        else{
            // Perform validations based on card class (Monster, Spell or Trap)
            $error_list = $this->validate_card_data($data);

            if(!isset($data['card_name']) && $data['card_name'] != ''){
                // Validate card name uniqueness
                $current_id = DB::table('yugioh_cards_list')->select('id')
                ->where('name', $data['card_name'])->get();

                if($current_id != $data['id']){
                    $error_list[] = "A different card already exists with name '" . $data['card_name'] . "'.";
                }
            }
        }

        if(count($error_list) < 1){
            // Add card image entry for current card
            try{
                switch($data['class']){
                    case '1':{
                        $this->update_monster($data);
                        break;
                    }
                    case '2':{
                        $this->update_spell($data, 'SPELL');
                        break;
                    }
                    case '3':{
                        $this->update_spell($data, 'TRAP');
                        break;
                    }
                }
                $status = 'ok';
            }
            catch(Exception $e){
                $error_list[] = $e->getMessage();
                $status = 'error';
            }
        }
        else{
            $status = 'error';
        }
        return (object)array(
            'status' => $status,
            'errors' => $error_list,
        );
    }

    public function search(array $data = array()){
        $alias = 'list';
        $query = DB::table('yugioh_cards_list as ' . $alias)
        ->select($alias . '.id', $alias . '.name', $alias . '.card_type', 
        'gallery.active_file');
        
        if(array_key_exists('class', $data)){
            $query->where('list.class', $data['class']);
            switch($data['class']){
                case 1:{
                    $this->make_monster_search_filters($query, $data, $alias . '.id', 'data');
                    $this->make_family_search_filters($query, $data, $alias . '.id', 'family');
                    break;
                }
                case 2:{
                    if(array_key_exists('spell_type', $data) && trim($data['spell_type']) != ''){
                        $query->where($alias . '.spell_type', '=', $data['spell_type']);
                    }
                    break;
                }
                case 3:{
                    if(array_key_exists('trap_type', $data) && trim($data['trap_type']) != ''){
                        $query->where($alias . '.spell_type', '=', $data['trap_type']);
                    }
                    break;
                }
            }
        }
        if(array_key_exists('official', $data)){
            $query->where($alias . '.official', '=', true);
        }
        else{
            $query->where($alias . '.official', '=', false);
        }
        if(array_key_exists('description', $data) && trim($data['description']) != ''){
            $query->where($alias . '.description', 'like', '%' . $data['description'] . '%');
        }
        if(array_key_exists('card_name', $data) && trim($data['card_name']) != ''){
            $query->where($alias . '.name', 'like', '%' . $data['card_name'] . '%');
        }
        
        $query->where([
            [$alias . '.active', '=', true],
            ['gallery.active', '=', true]
        ]);
        $query->join('yugioh_card_images as images', 'images.card_id', '=', $alias . '.id');
        $query->join('yugioh_images_list as gallery', 'gallery.id', '=', 'images.image_id');
        $res = $query->take(200)->orderBy($alias . '.name')->get();

        return array('data' => $res);
    }
    
    public function card_images($id){
        $text_query = "
        select gallery.active_file 
        from 
        yugioh_cards_list cards, 
        yugioh_card_images images, 
        yugioh_images_list gallery 
        where 
        cards.id = images.card_id 
        and gallery.id = images.image_id 
        and cards.id = 21;";
        $query = DB::table('yugioh_card_images as images')->select('gallery.active_file')
        ->where([
            ['gallery.active', '=', true],
            ['cards.active', '=', true],
            ['cards.id', '=', $id]
        ])
        ->join('yugioh_images_list as gallery', 'gallery.id', '=', 'images.image_id')
        ->join('yugioh_cards_list as cards', 'cards.id', '=', 'images.card_id');
        $res = $query->get();

        return array('data' => $res);
    }

    public function card_info($id){
        $card = DB::table('yugioh_cards_list as cards')->select('cards.class', 'cards.description', 
        'cards.name as card_name', 'cards.official', 'cards.spell_type', 'cards.user_id', 'users.name as user_name')
        ->join('users', 'users.id', '=', 'cards.user_id')
        ->where([
            ['cards.id', '=', $id],
            ['cards.active', '=', true],
        ])
        ->get();
        
        if(count($card) > 0){
            $res = array();
            if($card[0]->class == 1){
                $monster_info = DB::table('yugioh_monster_data as data')->select(
                'attr.id as attribute', 'attr.attribute as attribute_name', 'data.level', 
                'types.id as monster_type', 'types.type as monster_type_name', 'data.left_scale', 'data.right_scale', 
                'data.pendulum_effect', 'data.attack', 'data.defense')
                ->join('yugioh_monster_types_list as types', 'data.type', '=', 'types.id')
                ->join('yugioh_monster_attributes_list as attr', 'data.attribute', '=', 'attr.id')
                ->where('monster_id', $id)->get();

                $monster_families = DB::table('yugioh_monster_families as families')
                ->select('list.family')
                ->join('yugioh_families_list as list', 'list.id', '=', 'families.family_id')
                ->where('families.monster_id', $id)->get();
                
                foreach($card as $row){
                    foreach($row as $key => $value){
                        $res[$key] = $value;
                    }
                }
                foreach($monster_info as $row){
                    foreach($row as $key => $value){
                        $res[$key] = $value;
                    }
                }
                $res['families'] = array();
                foreach($monster_families as $family){
                    $res['families'][] = $family->family;
                }
                unset($res['spell_type']);
            }
            else{
                $spell_type = DB::table('yugioh_spell_types_list')->select('type')
                ->where('id', $card[0]->spell_type)->get();
                $card[0]->spell_type_name = $spell_type[0]->type;
                foreach($card as $row){
                    foreach($row as $key => $value){
                        $res[$key] = $value;
                    }
                }
            }
            $card_images = $this->card_images($id);
            $res['status'] = 'ok';
            $res['images'] = $card_images['data'];
        }
        else{
            $res = array('status' => 'error', 'error' => 'No cards found.');
        }
        
        return $res;
    }
    
    private function make_monster_search_filters(&$handler, $data, $list_join='list.id', $data_alias='data'){
        $count = 0;
        
        if(array_key_exists('attribute', $data) && trim($data['attribute']) != ''){
            $handler->where($data_alias.'.attribute', '=', $data['attribute']);
            $count++;
        }
        if(array_key_exists('level', $data) && trim($data['level']) != ''){
            $handler->where($data_alias.'.level', '=', $data['level']);
            $count++;
        }
        if(array_key_exists('monster_type', $data) && trim($data['monster_type']) != ''){
            $handler->where($data_alias.'.type', '=', $data['monster_type']);
            $count++;
        }
        if(array_key_exists('left_scale', $data) && trim($data['left_scale']) != ''){
            $handler->where($data_alias.'.left_scale', '=', $data['left_scale']);
            $count++;
        }
        if(array_key_exists('right_scale', $data) && trim($data['right_scale']) != ''){
            $handler->where($data_alias.'.right_scale', '=', $data['right_scale']);
            $count++;
        }
        if(array_key_exists('pendulum_effect', $data) && trim($data['pendulum_effect']) != ''){
            $handler->where($data_alias.'.pendulum_effect', 'like', "%{$data['pendulum_effect']}%");
            $count++;
        }
        if(array_key_exists('attack', $data) && trim($data['attack']) != ''){
            $handler->where($data_alias.'.attack', '=', $data['attack']);
            $count++;
        }
        if(array_key_exists('defense', $data) && trim($data['defense']) != ''){
            $handler->where($data_alias.'.defense', '=', $data['defense']);
            $count++;
        }
        if($count > 0){
            $handler->join('yugioh_monster_data as ' . $data_alias, 
            $data_alias.'.monster_id', '=', $list_join);
        }
    }
    
    private function make_family_search_filters(&$handler, $data, $list_join='list.id', $family_alias='family'){
        $query = DB::table('yugioh_families_list')->select('id', 'family')->get();
        $count = 0;
        $families = array();
        foreach($query as $record){
            $lower_family = strtolower($record->family);
            if(array_key_exists($lower_family, $data) && 
            $data[$lower_family] == 'true'){
                $families[] = $record->id;
                $count++;
            }
        }
        if($count > 0){
            $handler->whereIn($family_alias . '.family_id', $families);
            $handler->join('yugioh_monster_families as ' . $family_alias, 
            $family_alias . '.monster_id', '=', $list_join);
        }
    }
    
    public function monster_families_list(){
        return DB::table('yugioh_families_list')->select('family')->get();
    }
    
    public function make_filters(&$handler, $data, $fields, $prefix=''){
        $count = 0;
        $prefix = $prefix == ''? $prefix: $prefix.".";
        foreach($fields as $field => $operator){
            if(array_key_exists($field, $data)){
                $count++;
                $handler->where($prefix.$field, $operator, $data[$field]);
            }
        }
        return $count;
    }
    
    public function monster_fields_list($data){
        return array('attribute', 'level', 'rank', 'type', 
        'left_scale', 'right_scale', 'pendulum_effect', 'attack', 'defense');
    }
    
    public function has_families($data){
        $families = $this->monster_families_list();
        $res = false;
        foreach($families as $family){
            $family_name = strtolower($family->family);
            if(array_key_exists($family_name, $data) && $data[$family_name] == true){
                $res = true;
                break;
            }
        }
        return $res;
    }
    
    private function validate_card_data($data){
        DataArray::release_null_values($data);
        $error_list = array();
        $val = '';

        switch($val){
            case '':{
                if(!isset($data['class'])){
                    $error_list[] = 'Every card must have a class(Monster, Spell or Trap).';
                    break;
                }
                if(!isset($data['card_name'])){
                    $error_list[] = 'Every card must have a Name.';
                }
                if(!isset($data['user_id'])){
                    $error_list[] = 'Every card must be related to an user.';
                }
                if(!isset($data['description'])){
                    $error_list[] = 'Every card must have a Description or Effect.';
                }
                else if(strlen(trim($data['description'])) < 10){
                    $error_list[] = 'Card Descriptions or Effects must be at least 10 characters long.';
                }
                switch($data['class']){
                    case '1':{
                        $validator = Validator::make($data, [
                            'monster_type' => 'string|required',
                            'attribute' => 'string|required',
                            'level' => 'integer|between:1,12',
                            'left_scale' => 'required_if:pendulum,true|integer|between:1,12',
                            'right_scale' => 'required_if:pendulum,true|integer|between:1,12',
                            'pendulum_effect' => 'required_if:pendulum,true|string',
                            'attack' => 'required|regex:/[0-9?]/',
                            'defense' => 'required|regex:/[0-9?]/',
                        ],
                        array(
                            'monster_type.in' => 'The selected Monster Type is not valid.',
                            'monster_type.required' => 'Monsters must have a Type(Dragon, Zombie, etc.).',
                            'monster_type.string' => 'Monster Type must by a lowercase alphabetic string.',
                            'attribute.string' => 'Attribute must by a lowercase alphabetic string.',
                            'attribute.required' => 'Monsters must have an Attribute(Fire, Water, etc.).',
                            'attribute.in' => 'The selected Attribute is not valid.',
                            'level.integer' => 'Level must be a number.',
                            'level.between' => 'Level must be between 1 and 12.',
                            'left_scale.required_if' => 'Pendulum monsters must have a Left Scale.',
                            'left_scale.integer' => 'Left Scale must be a number',
                            'left_scale.between' => 'Left Scale must be between 1 and 12.',
                            'right_scale.required_if' => 'Pendulum monsters must have a Right Scale.',
                            'right_scale.integer' => 'Right Scale must be a number',
                            'right_scale.between' => 'Right Scale must be between 1 and 12.',
                            'pendulum_effect.required_if' => 'Pendulum monsters must have Pendulum Effect.',
                            'pendulum_effect.string' => 'Pendulum Effect must be a alpha-numeric string.',
                            'attack.required' => 'Monsters must have Attack points.',
                            'attack.regex' => 'Attack must be a number or "?"',
                            'defense.required' => 'Monsters must have Defense points.',
                            'defense.regex' => 'Defense must be a number or "?"',
                        ));
                        if($validator->fails()){
                            $error_list = array_merge($error_list, $validator->errors()->all());
                        }
                        if(array_key_exists('monster_type', $data) && 
                        !$this->validate_monster_type($data['monster_type'])){
                            $error_list[] = 'Invalid Monster Type.';
                        }
                        if(array_key_exists('attribute', $data) && 
                        !$this->validate_monster_attribute($data['attribute'])){
                            $error_list[] = 'Invalid Monster Attribute.';
                        }
                        if(!$this->has_families($data)){
                            $error_list[] = 'Every monster must have at least one Family(Normal, Fusion, etc).';
                        }
                        if(!array_key_exists('level', $data) && !array_key_exists('rank', $data)){
                            $error_list[] = 'Monsters must have either Level or Rank.';
                        }
                        break;
                    }
                    case '2':{
                        $validator = Validator::make($data, [
                            'spell_type' => 'integer|required',
                        ],
                        array(
                            'spell_type.integer' => 'Invalid Spell type.',
                            'spell_type.required' => 'Every Spell must have a type (Normal, Quick-Play, etc).',
                        ));
                        if($validator->fails()){
                            $error_list = array_merge($error_list, $validator->errors()->all());
                        }
                        break;
                    }
                    case '3':{
                        $validator = Validator::make($data, [
                            'trap_type' => 'integer|required',
                        ],
                        array(
                            'trap_type.integer' => 'Invalid Trap type.',
                            'trap_type.required' => 'Every Trap must have a type (Normal, Counter, etc).',
                        ));
                        if($validator->fails()){
                            $error_list = array_merge($error_list, $validator->errors()->all());
                        }
                        break;
                    }
                    default:{
                        $error_list[] = 'Card class must be Monster, Spell or Trap';
                        break;
                    }
                }
            }
        }

        return $error_list;
    }

    public function validate_monster_type($type){
        $cursor = DB::table('yugioh_monster_types_list')->select('id')->
        where('id', '=', $type)->get();
        $res = true;
        if(count($cursor) < 1){
            $res = false;
        }
        return $res;
    }
    
    public function validate_monster_attribute($attr){
        $cursor = DB::table('yugioh_monster_attributes_list')->select('id')->
        where('id', '=', $attr)->get();
        $res = true;
        if(count($cursor) < 1){
            $res = false;
        }
        return $res;
    }

    public function user_cards($id){
        $alias = 'list';
        $query = DB::table('yugioh_cards_list as ' . $alias)
        ->select($alias . '.id', $alias . '.name', $alias . '.card_type', 
        'gallery.active_file')
        ->where([
            [$alias . '.user_id', '=', $id],
            [$alias . '.active', '=', true],
        ]);
        $query->join('yugioh_card_images as images', 'images.card_id', '=', $alias . '.id');
        $query->join('yugioh_images_list as gallery', 'gallery.id', '=', 'images.image_id');
        $res = $query->take(200)->orderBy($alias . '.name')->get();

        return array('data'=> $res);
    }
    
    /**
    * 
    * 
    * 
    */
    private function create_monster($data){
        // Begin transaction
        DB::transaction(function() use($data){
            // Create card in cards list
            $monster_id = DB::table('yugioh_cards_list')->insertGetId(array(
                'name' => $data['card_name'],
                'description' => $data['description'],
                'class' => '1',
                'active' => true,
                'official' => $data['official'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'user_id' => $data['user_id'],
            ));
            // Create card families records
            $this->add_monster_families($monster_id, $data);

            // Store monster data
            $this->set_monster_data($monster_id, $data);
            
            $this->add_card_images($data['user_id'], $monster_id, $data['file']);
        });
    }

    private function update_monster($data){
        // Create card in cards list
        DB::table('yugioh_cards_list')->where('id', $data['id'])
        ->update(array(
            'name' => $data['card_name'],
            'description' => $data['description'],
            'class' => '1',
            'active' => true,
            'official' => $data['official'],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'user_id' => $data['user_id'],
        ));
        // Create card families records
        $this->add_monster_families($data['id'], $data);

        // Store monster data
        $this->set_monster_data($data['id'], $data);
        
        $this->add_card_images($data['user_id'], $data['id'], $data['file']);
        $this->remove_card_images($data);
    }

    private function add_monster_families($monster_id, &$data){
        $families = $this->monster_families_list();
        
        DB::table('yugioh_monster_families')->where('monster_id', $monster_id)->delete();
        
        foreach($families as $family){
            $family_name = strtolower($family->family);
            if(array_key_exists($family_name, $data) && $data[$family_name] == 'true'){
                $family_id = DB::table('yugioh_families_list')->select('id')
                ->where('family', '=', ucfirst($family_name))->get();
                DB::table('yugioh_monster_families')->insert(array(
                'family_id' => $family_id[0]->id, 'monster_id' => $monster_id));
            }
        }
        $extra_card_families = array('fusion', 'xyz', 'synchro');
        $card_type = 'MainCard';
        foreach($extra_card_families as $family){
            if(array_key_exists($family, $data) && $data[$family] == 'true'){
                $card_type = 'ExtraCard';
                break;
            }
        }
        DB::table('yugioh_cards_list')->where('id', $monster_id)
        ->update(array('card_type' => $card_type));
    }
    
    private function set_monster_data($monster_id, &$data, $action='INSERT'){
        $action = strtoupper($action);
        $monster_data['monster_id'] = $monster_id;
        $monster_data['attribute'] = $data['attribute'];
        if(DataArray::has_non_empty_key($data, 'level')){
            $monster_data['level'] = $data['level'];
        }
        $monster_data['type'] = $data['monster_type'];
        if($this->has_pendulum_data($data)){
            $monster_data['pendulum_effect'] = $data['pendulum_effect'];
            $monster_data['left_scale'] = $data['left_scale'];
            $monster_data['right_scale'] = $data['right_scale'];
        }
        $monster_data['attack'] = $data['attack'];
        $monster_data['defense'] = $data['defense'];
        switch($action){
            case 'INSERT':{
                DB::table('yugioh_monster_data')->insert($monster_data);
                break;
            }
            case 'UPDATE':{
                DB::table('yugioh_monster_data')->where('monster_id', $monster_id)
                ->update($monster_data);
                break;
            }
        }
    }
    
    private function has_pendulum_data(&$data){
        $res = false;
        if(DataArray::key_has_value($data, 'pendulum', 'true')){
            if(DataArray::has_non_empty_key($data, 'pendulum_effect') 
                && DataArray::has_non_empty_key($data, 'left_scale')
                && DataArray::has_non_empty_key($data, 'right_scale')){
                $res = true;
            }
        }
        
        return $res;
    }
    
    private function create_spell($data, $card_type='SPELL'){
        $spell_type = '';
        $this->set_spell_type_vars($data, $card_type, $spell_type);
        
        // Begin transaction
        DB::transaction(function() use($data, $card_type, $spell_type){
            // Create card in cards list
            $card_id = DB::table('yugioh_cards_list')->insertGetId(array(
                'name' => $data['card_name'],
                'description' => $data['description'],
                'class' => $card_type,
                'active' => true,
                'spell_type' => $spell_type,
                'official' => $data['official'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'user_id' => $data['user_id'],
            ));
            // Process image files
            $this->add_card_images($data['user_id'], $card_id, $data['file']);
        });
    }

    private function update_spell($data, $card_type='SPELL'){
        $spell_type = '';
        $this->set_spell_type_vars($data, $card_type, $spell_type);

        DB::transaction(function()use($data, $card_type, $spell_type){
            DB::table('yugioh_cards_list')->where('id', $data['id'])
            ->update([
                'name' => $data['card_name'],
                'description' => $data['description'],
                'spell_type' => $spell_type,
                'official' => $data['official'],
                'updated_at' => Carbon::now(),
            ]);
            
            $this->add_card_images($data['user_id'], $data['id'], $data['file']);
            $this->remove_card_images($data);
        });
    }

    private function add_card_images($user_id, $card_id, &$images){
        $images_count = count($images);
        for($i = 0; $i < $images_count; $i++){
            if($images[$i]->getClientOriginalName() == 'blob'){
                continue;
            }
            // Create partial yugioh_images_list record
            $image_id = DB::table('yugioh_images_list')->insertGetId(array(
                'active' => true,
                'user_id' => $user_id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ));
            
            // Store image file
            $image_file = new ImageFile($image_id, $images[$i], 'yugioh');
            $store_result = $image_file->store();
            
            if($store_result->status == 'ok'){
                try{
                    // Complete yugioh_images_list record if succeeded
                    DB::table('yugioh_images_list')
                    ->where('id', $image_id)
                    ->update(array('active_file'=> $image_file->get_image_name()));
                    
                    // Create yugioh_card_images record
                    DB::table('yugioh_card_images')->insert(array(
                        'card_id' => $card_id,
                        'image_id' => $image_id,
                    ));
                }
                catch(Exception $e){
                    $image_file->remove();
                    throw new Exception('Incomplete image record.');
                }
            }
            else{
                throw new Exception($store_result->message);
            }
        }
    }
    
    private function remove_card_images(&$data){
        if(!DataArray::has_non_empty_key($data, 'deleted_images')){
            return;
        }
        
        $images_count = count($data['deleted_images']);
        for($i = 0; $i < $images_count; $i++){
            $data['deleted_images'][$i] = preg_replace("/[^0-9]/", '', $data['deleted_images'][$i]);
            $this->set_image_status($data['id'], $data['deleted_images'][$i], 'INACTIVE');
        }
    }

    private function set_spell_type_vars(&$data, &$card_type, &$spell_type){
        if($card_type == 'TRAP'){
            $card_type = 3;
            $spell_type = $data['trap_type'];
        }
        else{
            $card_type = 2;
            $spell_type = $data['spell_type'];
        }
    }

    private function card_name_repeated($name){
        $card = DB::table('yugioh_cards_list')->select('id')->where('name', $name)->get();
        if(count($card) > 0){
            $res = true;
        }
        else{
            $res = false;
        }
        return $res;
    }

    private function set_image_status($card_id, $image_id, $status = 'ACTIVE'){
        $status = strtoupper($status);
        switch($status){
            case 'ACTIVE':{
                $status = true;
                break;
            }
            case 'INACTIVE':
            default:{
                $status = false;
                break;
            }
        }
        
        $card_filters = array(['card_id', '=', $card_id], ['image_id', '=', $image_id]);

        $query = DB::table('yugioh_card_images')->select('card_id')
        ->where($card_filters)->get();
        if(count($query) > 0){
            DB::table('yugioh_images_list')->where('id', $image_id)->update([
                'active' => $status,
                'updated_at' => Carbon::now(),
            ]);
            DB::table('yugioh_card_images')->where($card_filters)->delete();
        }
    }    
    
    /**
    * 
    * 
    * 
    */
    private function format_strings($data){
        $expr = "/\s{2,}/";
        if(isset($data['card_name'])){
            $data['card_name'] = ucwords($data['card_name']);
            $data['card_name'] = preg_replace($expr, ' ', $data['card_name']);
        }
        if(isset($data['description'])){
            $data['description'] = $this->format_description_text($data['description']);
        }
        if(isset($data['pendulum_effect'])){
            $data['pendulum_effect'] = $this->format_description_text($data['pendulum_effect']);
        }
        return $data;
    }
    
    private function format_description_text($text){
        $expr = "/\s{2,}/";
        $test = ucfirst(strtolower($text));
        $test = preg_replace($expr, ' ', $test);
        return trim($text);
    }
}









