<?php

namespace App\Http\Controllers;
ini_set('memory_limit', '1G');


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use stdClass;

class Viewcontroller extends Controller
{
    public $mappedSura;
    public $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    public function viewSuraDetails()
    {    
        $suraName = $this->request->suraName;
        return file_get_contents(storage_path('categorized_suras/suras_details/' . $suraName));
    }

    public function viewSuraCharts(){
        $suraName = $this->request->suraName;
        return file_get_contents(storage_path('categorized_suras/suras_charts/' . $suraName));
    }

    public function allSurasData()
    {    
        return file_get_contents(storage_path('categorized_suras/search_basics/allSurasData'));
    }

    public function viewVersesBasics()
    {    
        $suraName = $this->request->suraName;
        return file_get_contents(storage_path('categorized_suras/verses_basics/' . $suraName));
    } 
    
    public function viewSuraBasics()
    {    
        $suraName = $this->request->suraName;
        return file_get_contents(storage_path('categorized_suras/suras_basics/' . $suraName));
    }
    public function viewSuraText()
    {    
        $suraName = $this->request->suraName;
        return file_get_contents(storage_path('categorized_suras/suras_text/' . $suraName));
    }
    
    public function viewSuraElement()
    {
        $suraName = $this->request->suraName;
        $mappedSura = file_get_contents(storage_path('decoded_suras/' . $suraName . '_data.json'));
        $mappedSura = json_decode($mappedSura);
        $dataType = $this->request->dataType;
        $this->suraMap[$dataType]= $mappedSura->{$dataType};
        return $this->parseArrayToObj($this->suraMap[$dataType]);
    }
    public function parseArrayToObj($results){
        $tmp = [];
        $index = 0;
        if(is_object($results))
        {
            foreach($results as $key => $value){
            $tmp[$key] = $value;
            $index++;
        }
            return $tmp;
        }
        else    return json_encode($results);
    }
    public function viewVerseElement(){
        $suraName = $this->request->suraName;
        $mappedSura = file_get_contents(storage_path('decoded_suras/' . $suraName . '_data.json'));
        $mappedSura = json_decode($mappedSura);
        $dataType = $this->request->dataType;
        dd('Needs work!');
        return $this->parseVerseObject($dataType, $mappedSura);

    }

    public function viewQuranIndex(){
        $quranIndex = file_get_contents(storage_path('quranIndexWithData'));
        return $quranIndex;
    }

    public function viewFileNames(){
        $quranIndex = file_get_contents(storage_path('quranIndex'));
        return $quranIndex;
    }

    public function viewQuranString(){
        $file = file_get_contents(storage_path('allSurasDataRaw'));
        $file = json_decode($file);
        $allQuranString = "";
        
        foreach($file as $value){
            $allQuranString .= ($value->verseText).',';
        }
        file_put_contents(storage_path('rawQuranText'), ($allQuranString));

        return $allQuranString;
    }
}
