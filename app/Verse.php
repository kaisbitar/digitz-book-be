<?php

namespace App;

use App\Word;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Verse extends Model
{
    public $verseString;
    public $verseArray;
    public $verseIndex;
    public $lettersCount = [];

    public function __construct($verse, $index)
    {
        $this->verseString = $verse;
        $this->verseArray = explode(" ", $verse);
        $this->verseIndex = $index + 1;

        return $this;
    }

    public function countVerseLetters()
    {
        $verseNoSpaces = implode("", $this->verseArray);
        $lettersCount = mb_strlen($verseNoSpaces);

        return $lettersCount;
    }
}
