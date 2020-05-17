<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Counter extends Model
{
    public function countLettersInString($inputString)
    {
        $lettersScore = new \stdClass(); 
        $alphabet = File::get(storage_path('الابجدية'));

        if (!isset($alphabet)) {
            return response()->json(["error" => "Alphabet file not found"]);
        }

        $alphabetArray = explode(" ", $alphabet);
        foreach ($alphabetArray as $letter) {
           //remove to return the missing letters too ;) 
           if (!empty(substr_count($inputString, $letter))) {
                $lettersScore->$letter = substr_count($inputString, $letter);
            }
        }

        return $lettersScore;
    }

    public function countWordsInString($inputString)
    {
        $occurrences = array_count_values($this->splitStringToArray($inputString));
        asort($occurrences);

        return $occurrences;
    }

    public function splitStringToArray($string)
    {
        mb_internal_encoding('UTF-8');
        mb_regex_encoding('UTF-8');
        $words = mb_split('[^\x{0600}-\x{06FF}]', $string);

        return $words;
    }

}
