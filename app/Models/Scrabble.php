<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scrabble extends Model
{
    use HasFactory;

    const letter_values = [
        'A' => 1,
        'B' => 3,
        'C' => 3,
        'D' => 2,
        'E' => 1,
        'F' => 4,
        'G' => 2,
        'H' => 4,
        'I' => 1,
        'J' => 8,
        'K' => 5,
        'L' => 1,
        'M' => 3,
        'N' => 1,
        'O' => 1,
        'P' => 3,
        'Q' => 10,
        'R' => 1,
        'S' => 1,
        'T' => 1,
        'U' => 1,
        'V' => 4,
        'W' => 4,
        'X' => 8,
        'Y' => 4,
        'Z' => 1
    ];

    public function available_letters(){
        
        // Create an array for oour letters
        $letters = [];

        // Twelve tiles for the letter 'E'
        $x = 1;
        while($x <= 12) {
            $letters[] = 'E';
            $x++;
        }

        // Nine tiles for letters 'A' and 'I'
        $x = 1;
        while($x <= 9) {
            $letters[] = 'A';
            $letters[] = 'I';
            $x++;
        }

        // Eight tiles for letters 'O'
        $x = 1;
        while($x <= 8) {
            $letters[] = 'O';
            $x++;
        }

        // Six tiles for letters 'N', 'R' and 'T'
        $x = 1;
        while($x <= 6) {
            $letters[] = 'N';
            $letters[] = 'R';
            $letters[] = 'T';
            $x++;
        }

        // Four tiles for letters 'L', 'S', 'U' and 'D'
        $x = 1;
        while($x <= 4) {
            $letters[] = 'L';
            $letters[] = 'S';
            $letters[] = 'U';
            $letters[] = 'D';
            $x++;
        }

        // Three tiles for letters 'G'
        $x = 1;
        while($x <= 3) {
            $letters[] = 'G';
            $x++;
        }

        // Two tiles for letters 'B', 'C', 'M', 'P', 'F', 'H', 'V', 'W', 'Y'
        $x = 1;
        while($x <= 2) {
            $letters[] = 'B';
            $letters[] = 'C';
            $letters[] = 'M';
            $letters[] = 'P';
            $letters[] = 'F';
            $letters[] = 'H';
            $letters[] = 'V';
            $letters[] = 'W';
            $letters[] = 'Y';
            $x++;
        }

        // One tile for letter 'K', 'J', 'X', 'Q', 'Z'
        $letters[] = 'K';
        $letters[] = 'J';
        $letters[] = 'X';
        $letters[] = 'Q';
        $letters[] = 'Z';
        return $letters;
    }
    // Shuffle the tiles
    public function shuffle_tiles($tiles){
        $keys = array_keys($tiles);
        shuffle($keys);
        foreach($keys as $key) {
            $new_tiles[$key] = $tiles[$key];
        }
        $tiles = $new_tiles;
        return $tiles;
    }

    // Generate new tiles
    public function generate_tiles(){
        $letters = self::available_letters();
        $number_of_letters = 7;
        $selection = array_rand($letters, $number_of_letters);
        $tiles = [];
        $x = 0;
        while($x < $number_of_letters) {
            $letter = $letters[$selection[$x]];
            $value = self::letter_value($letter);
            $tiles[] = ['letter' => $letter, 'value' => $value];
            $x++;
        }
        return self::shuffle_tiles($tiles);
    }

    // Safe tiles
    public function safe_tiles($tiles){
        $safe_tiles = [];
        foreach($tiles as $i => $tile){
            $safe_tiles[] = $tiles[$i]['letter'];
        }
        return $safe_tiles;
    }

    // Get the point value of a given letter
    public function letter_value($letter){
        $letter = strtoupper($letter);
        $values = self::letter_values;  
        return $values[$letter];
    }

    // Allowed words - discard words that are more than 7 characters
    public function allowed_words(){
        $words = file(asset('words.txt'), FILE_IGNORE_NEW_LINES);
        $allowed_words = [];
        foreach($words as $word){          
            if(strlen($word) <= 7){
                $allowed_words[] = $word;
            }
        }
        return $allowed_words;
    }

    // Score a word based on th value of its letters
    public function score_word($word = 'guardian'){
        $word = strtoupper($word);
        $letters = str_split($word);
        $scores = self::letter_values;
        $total = 0;
        foreach($letters as $letter){
            $total += $scores[$letter];
        }
        return $total;
    }

    // Discard word that have a character that is not contained in our tiles
    public function discard_words_we_cant_spell($our_tiles = [0 => 'u', 1 => 'o', 2 => 's', 3 =>'h', 4 => 'e', 5 => 'd', 6 => 't']){
        $words = self::allowed_words();

        $words_to_keep = [];
        foreach($words as $word){

            $word = strtoupper($word);

            $kept_words = [];
            
            $word_letters = str_split($word);
            $now_tiles = $our_tiles;

            
            $action = 'keep';
            foreach($word_letters as $word_letter){
                if(in_array($word_letter, $now_tiles)){

                    if(substr_count($word, $word_letter) > 1){
                        $num = substr_count($word, $word_letter);
                        $check = array_count_values($now_tiles);
                        $check = $check[$word_letter];
                        if($num > $check){
                            $action = "discard";
                        }else{

                        }
                    }
                    else{
                        
                    }
                }
                else{
                    $action = "discard";
                }
                
            }


            
            if($action != 'discard'){
                $words_to_keep[] = ['word' => $word, 'value' => self::score_word($word)];
            }
        }


        $value = array_column($words_to_keep, 'value');

        array_multisort($value, SORT_DESC, $words_to_keep);
        return $words_to_keep;

    }


    
}
