<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scrabble extends Model
{
    use HasFactory;

    // Define a constant for our character cap
    const CHARACTER_CAP = 7;

    // Define a constant for letter values
    const LETTER_VALUES = ['A' => 1, 'B' => 3, 'C' => 3, 'D' => 2, 'E' => 1, 'F' => 4, 'G' => 2, 'H' => 4, 'I' => 1, 'J' => 8, 'K' => 5, 'L' => 1, 'M' => 3, 'N' => 1, 'O' => 1, 'P' => 3, 'Q' => 10, 'R' => 1, 'S' => 1, 'T' => 1, 'U' => 1, 'V' => 4, 'W' => 4, 'X' => 8, 'Y' => 4, 'Z' => 1];
    
    // Define a contant for the quantity of each letter
    const LETTER_QUANTITIES = [['letters' => ['E'], 'quantity' => 12], ['letters' => ['A', 'I'], 'quantity' => 9], ['letters' => ['O'], 'quantity' => 8], ['letters' => ['N', 'R', 'T'], 'quantity' => 6], ['letters' => ['L', 'S', 'U', 'D'], 'quantity' => 4], ['letters' => ['G'], 'quantity' => 3], ['letters' => ['B', 'C', 'M', 'P', 'F', 'H', 'V', 'W', 'Y'], 'quantity' => 2], ['letters' => ['K', 'J', 'X', 'Q', 'Z'], 'quantity' => 1]];
    

    // Load up of bag out letter tiles with the correct quantity of each lettter
    public function available_letters(){
        // Assign the contant to a variable
        $letter_quantities = self::LETTER_QUANTITIES;
        // Create an array for our letters
        $letters = [];
        // For each group of quantaties, populate $letters with that number of tiles
        foreach($letter_quantities as $lq){
            foreach($lq['letters'] as $letter){
                $x = 1;
                while($x <= $lq['quantity']) {
                    $letters[] = $letter;
                    $x++;
                }
            }
        }
        // Return our collection of letters
        return $letters;
    }

    // Genterate random titles from our bag - 7 by default
    public function generate_tiles($number_of_tiles = self::CHARACTER_CAP){
        $letters = self::available_letters();
        $selection = array_rand($letters, $number_of_tiles);
        $tiles = [];
        $x = 0;
        while($x < $number_of_tiles) {
            $letter = $letters[$selection[$x]];
            $value = self::letter_value($letter);
            $tiles[] = ['letter' => $letter, 'value' => $value];
            $x++;
        }
        return self::shuffle_tiles($tiles);
    }


    // Shuffle our selection of tiles so they don't appear in order of value
    public function shuffle_tiles($tiles){
        $keys = array_keys($tiles);
        shuffle($keys);
        foreach($keys as $key) {
            $shuffled_tiles[$key] = $tiles[$key];
        }
        return $shuffled_tiles;
    }

    // Build an array of tiles containing only their letter
    // This will make it easier for us to find matching words later
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
        $values = self::LETTER_VALUES;  
        return $values[$letter];
    }

    // Allowed words - discard words containing more than our 'character cap'
    public function allowed_words($character_cap = self::CHARACTER_CAP){
        $words = file(asset('words.txt'), FILE_IGNORE_NEW_LINES);
        $allowed_words = [];
        foreach($words as $word){          
            if(strlen($word) <= $character_cap){
                $allowed_words[] = $word;
            }
        }
        return $allowed_words;
    }

    // Score a word based on th value of its letters
    public function score_word($word = 'winning'){
        $word = strtoupper($word);
        $letters = str_split($word);
        $scores = self::LETTER_VALUES;
        $total = 0;
        foreach($letters as $letter){
            $total += $scores[$letter];
        }
        return $total;
    }

    // Find the highest value letter of a given word
    public function highest_value_letter($word){
        $letters = str_split($word);
        $scores = self::LETTER_VALUES;
        $values = [];
        foreach($letters as $letter){
            $values[] = $scores[$letter];
        }
        return max($values);
    }

    // Tripple letter score
    public function tripple_letter_score($word_score, $highest_value_letter){
        $score = $word_score + ($highest_value_letter * 2);
        return $score;
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
                $words_to_keep[] = ['word' => $word, 'value' => self::score_word($word), 'highest_value_letter' => self::highest_value_letter($word), 'tripple_letter_score' => self::tripple_letter_score(self::score_word($word), self::highest_value_letter($word))];
            }
        }

        // Sort available words by the hightest sscore descending
        $value = array_column($words_to_keep, 'value');
        array_multisort($value, SORT_DESC, $words_to_keep);
        return $words_to_keep;

    }


    
}
