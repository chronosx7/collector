<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Factories\CardFactory;

class CardsController extends Controller
{
    public function __construct(){
        $this->middleware('auth', ['only' => [
            'create', 'store', 'edit', 'update', 'destroy'
        ]]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($game)
    {
        $game = strtolower($game);
        $view_dir = "$game/card_search";
        $param = array('game' => $game);
        return view($view_dir, $param);
    }

    public function search(Request $request, $game){
        $data = $request->all();
        $manager = new CardFactory($game);
        return response()->json($manager->search($data));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($game)
    {
        $game = strtolower($game);
        $view_dir = "$game/card_creation";
        $param = array('game' => $game);
        return view($view_dir, $param);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $game)
    {
        $data = $request->except('_token');
        $user = $request->user();
        $data['user_id'] = $user->id;
        $manager = new CardFactory($game);
        return response()->json($manager->save($data));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($game, $id)
    {
        $manager = new CardFactory($game);
        return response()->json($manager->card_info($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($game, $id)
    {
        $view_dir = "$game/card_creation";
        $param = array('game' => $game, 'card_id' => $id);
        return view($view_dir, $param);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $game)
    {
        $data = $request->except('_token');
        $user = $request->user();
        $data['user_id'] = $user->id;
        $manager = new CardFactory($game);
        return response()->json($manager->update($data));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function user_cards($game, $id){
        $manager = new CardFactory($game);
        return response()->json($manager->user_cards($id));
    }
}
