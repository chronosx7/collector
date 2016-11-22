<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Factories\DataFactory;
use App\Http\Requests;

class GameDataController extends Controller
{
    public function get_options($game){
        $factory = new DataFactory($game);
        return response()->json(array('data' => $factory->get_options()));
    }
}









