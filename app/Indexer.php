<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use stdClass;

class Indexer extends Model
{
    public function __construct()
    {
        return $this;
    }

    public function indexLettersInString($array)
    {
        $oneWordString = implode("", $array);
        $lettersArray = preg_split('//u', $oneWordString, -1, PREG_SPLIT_NO_EMPTY);
        $returnArray = [];

        foreach ($lettersArray as $index => $char) {
            if (!isset($returnArray[$char])) {
                $returnArray[$char] = [];
            }
            
            array_push($returnArray[$char], $index + 1);
        }
        asort($returnArray);
        return $returnArray;
    }

    public function indexWordsInString($string)
    {
        $string = \str_replace(",", " ", $string);

        $wordsArray = explode(" ", $string);
        $returnArray = new stdClass();

        foreach ($wordsArray as $index => $word) {
            if(!property_exists($returnArray, $word)){
            // if (!(array)($returnArray[$word])) {
                $returnArray->$word = [];
            }
            array_push($returnArray->$word, $index + 1);
        }
        // asort($returnArray);
        return $returnArray;
    }
}
