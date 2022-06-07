<?php

namespace App\Http\Controllers;

use App\Models\Scrabble;
use Illuminate\Http\Request;

class ScrabbleController extends Controller
{   
    // Let's load our gam with everything we need
    public function index(){
        $scrabble_model =(new Scrabble);

        // // Run debug
        // $scrabble_model->available_letters();

        $tiles = $scrabble_model->generate_tiles();
        $safe_tiles = $scrabble_model->safe_tiles($tiles);        
        $words = $scrabble_model->discard_words_we_cant_spell($safe_tiles);

        return view('scrabble', [
            'tiles' => $tiles,
            'safe_tiles' => $safe_tiles,
            'words' => $words
        ]);
    }
}
