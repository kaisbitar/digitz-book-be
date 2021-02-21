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
        $this->quranIndex = $this->viewQuranIndex();
        $qIndex = json_decode($this->quranIndex);
        $this->bigIndx = 1;
        $this->searchBasics = [];
        // $this->categorizeOneQuranFile();

        foreach ($qIndex as $item) {
            $this->fileName = $item->fileName;
            $this->mappedSura = file_get_contents(storage_path('decoded_suras/' . $this->fileName . '_data.json'));
            $this->categorizeSuraBasics();
            // $this->categorizeSuraCharts();
            $this->categorizeSuraText();
            $this->categorizeVersesBasic();
            $this->categorizeVersesdetails();
        }
        // dd($this->searchBasics);
        file_put_contents(storage_path('categorized_suras/search_basics/allSurasData'), json_encode($this->searchBasics, JSON_PRETTY_PRINT),FILE_APPEND );
    }

    public function categorizeOccurrences($occ)
    {
        $tmp = [];
        $index = 0;
        asort($occ);
        foreach($occ as $innerKey => $item){
            $obj = new stdClass();
            $obj->x = $innerKey;
            $obj->y = $item;    
            $tmp[$index] = $obj;
            $index++;
        }
        return $tmp;
    }
    public function categorizeVersesdetails()
    {
        $results =  json_decode($this->mappedSura, true);
        $suraDetails = [];
        $letterOcc = $results['letterOccurrences'];
        $wordOcc = $results['wordOccurrences'];
        $suraDetails['letterIndexes'] = $results['letterIndexes'];
        $suraDetails['wordIndexes']= $results['wordIndexes'];
        $suraDetails['letterOccurrences'] = $this->categorizeOccurrences($letterOcc);
        $suraDetails['wordOccurrences'] = $this->categorizeOccurrences($wordOcc);
        

        file_put_contents(storage_path('categorized_suras/suras_details/' . $this->fileName), json_encode($suraDetails) );

    }
    public function categorizeVersesBasic()
    {
         
        $results =  json_decode($this->mappedSura);
        $versesBasics = [];
        $index = 0;
        if($this->fileName !== '000المصحف'){
            $this->bigIndx = 1;
        }
        foreach($results->versesMap as $value){
            //versesBasics
            $obj = new stdClass();
            $obj->sura = $this->fileName;
            $obj->bigIndx = $this->bigIndx;
            $obj->verseIndex = $index + 1;
            $obj->numberOfWords = $value->numberOfWords;
            $obj->numberOfLetters = $value->numberOfLetters;
            $versesBasics[$index] = $obj;
           
            if($this->fileName !== '000المصحف'){
                //searchBasics
                $obj2 = new stdClass();
                $obj2->verseNumberToQuran = $this->bigIndx;
                $obj2->suraNumber = $this->suraNumber;
                $obj2->sura = $this->fileName;
                $obj2->verseIndex = $index + 1;
                $obj2->verseText = $value->verseText;
            
                array_push($this->searchBasics, $obj2);
            }

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
        
        $tmp->numberOfWords = $results->numberOfWords; 
        $tmp->numberOfLetters = $results->numberOfLetters; 
        $tmp->numberOfVerses = count((array)($results->versesMap)); 

        file_put_contents(storage_path('categorized_suras/suras_basics/' . $this->fileName ), json_encode($tmp));
    }
    public function categorizeSuraText()
    {
        $results =  json_decode($this->mappedSura);
        $tmp = new stdClass();
        $tmp->suraText = explode(',', $results->suraString); 

        file_put_contents(storage_path('categorized_suras/suras_text/' . $this->fileName ), json_encode($tmp));
    }

    public function allSurasData()
    {    
        return file_get_contents(storage_path('allSurasDataRaw'));
        
    }
    
    public function viewQuranIndex(){
        $quranIndex = file_get_contents(storage_path('quranIndexWithData'));

        return $quranIndex;
    }


    public function categorizeSuraCharts(){
        $results =  json_decode($this->mappedSura);

        
        $letters= $this->parseVerseObject('numberOfLetters', $results->versesMap);
        $words = $this->parseVerseObject('numberOfWords', $results->versesMap);
        $suraCharts = new stdClass();
        $suraCharts->letters = $letters;
        $suraCharts->words = $words;
        file_put_contents(storage_path('categorized_suras/suras_charts/' . $this->fileName ), json_encode($suraCharts));
    }
    public function parseVerseObject($dataType, $versesMap){
        $suraMap= $versesMap;

        $tmp = [];
        $index = 0;
        foreach($suraMap as $key => $value){
            $tmp[$index] = $value->{$dataType};
            $index++;
        }

        return $tmp;

    }

}
