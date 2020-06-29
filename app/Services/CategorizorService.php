<?php

namespace App\Services;
use Illuminate\Http\Request;
use stdClass;

class CategorizorService
{

    public $suraName;
    public function __construct()
    {    }


    public function categorize()
    {   
        $this->quranIndex = self::viewQuranIndex();
        $qIndex = json_decode($this->quranIndex);
        $this->bigIndx = 1;
        $this->searchBasics = [];

        foreach ($qIndex as $item) {
            $this->fileName = $item->fileName;
            $this->mappedSura = file_get_contents(storage_path('decoded_suras/' . $this->fileName . '_data.json'));
            self::categorizeSuraBasics();
            self::categorizeVersesBasic();
            self::categorizeVersesdetails();
        }
        file_put_contents(storage_path('categorized_suras/search_basics/allSurasData'), json_encode($this->searchBasics, JSON_PRETTY_PRINT),FILE_APPEND );
    }

    public function categorizeVersesdetails()
    {
        $results =  json_decode($this->mappedSura);
        $verseIndex = 0;
        $suraDetails = [];
        
        foreach ($results->versesMap as $value) {
            foreach($value as $key => $innerValue){
                $tmp = [];
                $neededDtls = [
                    "LetterOccurrences",
                    "LetterIndexes",
                    "WordOccurrences",
                    "WordIndexes"
                ];
                if(in_array($key, $neededDtls)){
                    $container[$key] = [];
                    $index = 0;
                    foreach($innerValue as $innerKey => $item){
                        $obj = new stdClass();
                        $obj->$innerKey = $item;
                        $tmp[$index] = $obj;
                        $index++;
                    }
                    $container[$key] = $tmp;
                    $suraDetails[$verseIndex] = $container;
                }
            }
            $verseIndex++;
        }

        file_put_contents(storage_path('categorized_suras/details/' . $this->fileName), json_encode($suraDetails, JSON_PRETTY_PRINT),FILE_APPEND );

    }
    public function categorizeVersesBasic()
    {
        $results =  json_decode($this->mappedSura);
        $versesBasics = [];
        $index = 0;
        foreach($results->versesMap as $value){
            $obj = new stdClass();
            $obj->bigIndx = $this->bigIndx;
            $obj->verseIndx = $value->verseIndx;
            $obj->verseText = $value->verseText;
            $obj->NumberOfWords = $value->NumberOfWords;
            $obj->NumberOfLetters = $value->NumberOfLetters;
            $versesBasics[$index] = $obj;
            
            $obj2 = new stdClass();
            $obj2->verseNumberToQuran = $this->bigIndx;
            $obj2->suraNumber = $this->suraNumber;
            $obj2->sura = $this->fileName;
            $obj2->verseIndx = $value->verseIndx;
            $obj2->verseText = $value->verseText;
            array_push($this->searchBasics, $obj2);

            $index++;
            $this->bigIndx++;
        }

        file_put_contents(storage_path('categorized_suras/verses_basics/' . $this->fileName ), json_encode($versesBasics, JSON_PRETTY_PRINT));
    }

    public function categorizeSuraBasics()
    {
        $results =  json_decode($this->mappedSura);
        $this->suraNumber = preg_match_all('!\d+!', $results->Name, $matches);
        $this->suraName =  preg_replace('/[0-9]+/', "",$results->Name);
        $tmp = new stdClass();
        $tmp->fileName =  $results->Name; 
        $tmp->name = $this->suraNumber;
        $tmp->suraNumber = $this->suraName;
        
        $tmp->numberOfWords = $results->NumberOfWords; 
        $tmp->numberOfLetters = $results->NumberOfLetters; 
        $tmp->numberOfVerses = count((array)($results->versesMap)); 
        $tmp->suraString = explode(',', $results->suraString); 

        file_put_contents(storage_path('categorized_suras/suras_basics/' . $this->fileName ), json_encode($tmp));
    }

    public function allSurasData()
    {    
        return file_get_contents(storage_path('allSurasData'));
        
    }
    
    public function viewQuranIndex(){
        $quranIndex = file_get_contents(storage_path('quranIndexWithData'));

        return $quranIndex;
    }
}