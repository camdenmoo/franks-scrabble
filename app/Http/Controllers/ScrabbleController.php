<?php

namespace App\Http\Controllers;

use App\Models\Scrabble;
use Illuminate\Http\Request;

class ScrabbleController extends Controller
{   
    // Let's load our game with everything we need
    public function index(){
        $scrabble_model =(new Scrabble);
        $tiles = $scrabble_model->generate_tiles();
        $safe_tiles = $scrabble_model->safe_tiles($tiles);        
        $words = $scrabble_model->discard_words_we_cant_spell($safe_tiles);

        $string_lengths = $scrabble_model->number_of_string_lengths($words);


        //dd($string_lengths);
        return view('scrabble', [
            'tiles' => $tiles,
            'safe_tiles' => $safe_tiles,
            'words' => $words,
            'string_lengths' => $string_lengths

        ]);
    }
}
