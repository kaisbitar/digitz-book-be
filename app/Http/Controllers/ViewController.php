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
    public function viewSuraMap()
    {    
        $suraName = $this->request->suraName;
        $this->mappedSura = file_get_contents(storage_path('categorized_suras/suras_basics/' . $suraName));

        return $this->mappedSura;
        
    }
    public function allSurasData()
    {    
        return file_get_contents(storage_path('categorized_suras/search_basics/allSurasData'));
        
    }

    public function viewElement()
    {
        $suraName = $this->request->suraName;
        $this->mappedSura = file_get_contents(storage_path('decoded_suras/' . $suraName . '_data.json'));
        $this->mappedSura = json_decode($this->mappedSura);

        $dataType = $this->request->dataType;
        $this->suraMap[$dataType]= $this->mappedSura->{$dataType};

        $elementType = $this->request->elementType;
        $results = (($this->suraMap[$dataType]));

        if($elementType == 'sura'){

            $tmp = [];
            $index = 0;
            foreach($results as $value){
                $tmp[$index] = $value;
                $index++;
            }

            return $tmp;

        }

        elseif($elementType == 'verses'){

            $tmp = [];
            $index = 0;
            foreach($results as $key => $value){
                $obj = new stdClass();
                $obj->$key = $value;
                $tmp[$index] = $obj;
                $index++;
            }
            return $tmp;
        }
        
        else{

            echo 'Please type the correct Element: verses, sura';
        }
    }

    public function viewQuranIndex(){
        $quranIndex = file_get_contents(storage_path('quranIndex'));
        // $quranIndex = json_decode($quranIndex);
        // dd('viewQuranIndex');z
        return $quranIndex;
    }

    public function viewQuranString(){
        $file = file_get_contents(storage_path('allSurasData'));
        $file = json_decode($file);

        $allQuranString = "";
        
        foreach($file as $value){

            $allQuranString .= ($value->verseText).',';
        }

        file_put_contents(storage_path('rawQuranText'), ($allQuranString));

        return $allQuranString;
    }
}
