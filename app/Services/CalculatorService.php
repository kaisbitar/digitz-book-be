<?php

namespace App\Services;

use App\Counter;
use App\FullSura;
use App\Indexer;
use App\Verse;
use Illuminate\Support\Facades\File;
use App\Controllers\CalculatorController;
use App\Http\Controllers\ScoreController;
use Illuminate\Http\Request;
use App\Services\CategorizorService;


class CalculatorService
{
    private $request; 
    private $counter;
    private $fullSura;

    public function __construct($suraFile = null, $fileName = null)
    {   
        $this->counter = new Counter();
        $this->indexer = new Indexer();
        if($fileName == "quran-index"){
            return;
        }
        elseif($fileName == "decode-all"){
            return;
        }
        else{
            $this->fullSura = new FullSura($suraFile);
            $this->fullSura->Name = $fileName; 
        }      
    }

    public function mapSura()
    {   
        $resultFileName = $this->fullSura->Name . '_data.json';
        // if (!file_exists(storage_path('decoded_suras/' . $resultFileName))) {
            
            $this->fullSura->numberOfWords = $this->fullSura->calculateNumberOfWords();
            $this->fullSura->numberOfLetters = $this->fullSura->calculateNumberOfLetters();
            $this->fullSura->suraString = implode(",", $this->fullSura->verses);
            $verses = $this->processVerses($this->fullSura->verses);
            // $this->fullSura->SuraLettersCount = $this->counter->countLettersInString($this->fullSura->suraString);

            $this->fullSura->versesMap = $verses;

            $this->fullSura->wordOccurrences = $this->counter->countWordsInString($this->fullSura->suraString);
            // dd($this->fullSura->verses);
            $this->fullSura->wordIndexes = $this->indexer->indexWordsInString($this->fullSura->suraString);
            $this->fullSura->letterOccurrences = $this->counter->countLettersInString($this->fullSura->suraString);
            $this->fullSura->letterIndexes = $this->indexer->indexLettersInString($this->fullSura->verses);
            
            file_put_contents(storage_path('decoded_suras/' . $resultFileName), $this->fullSura);

        $mappedSura = file_get_contents(storage_path('decoded_suras/' . $this->fullSura->Name . '_data.json'));

        return $mappedSura;
    }

    public function mapLetters()
    {
        $this->fullSura->letterCount = $this->counter->countLettersInString($this->fullSura->suraString);
        $verses = $this->processVerses($this->fullSura->verses);

        return $verses;
    }

    public function mapVerses()
    {                
        $resultFileName = $this->fullSura->Name . '_data.json';
        // if (!file_exists(storage_path('decoded_verses/' . $resultFileName))) {
            $verses = $this->processVerses($this->fullSura->verses);
            $verses["SuraLettersCount"] = $this->counter->countLettersInString($this->fullSura->suraString);
            $resultFileName = $this->fullSura->Name . '_data.json';
            file_put_contents(storage_path('decoded_verses/'. $resultFileName), json_encode($verses, JSON_UNESCAPED_UNICODE));
        // }
        $mappedSura = file_get_contents(storage_path('decoded_verses/' . $this->fullSura->Name . '_data.json'));
        
        return $mappedSura;
    }
    

    private function processVerses($verses)
    {       
        $returnArray = [];

        foreach ($verses as $index => $verse) {
            $verseObject = new Verse($verse, $index);

            // $suraName = preg_replace("/[0-9]/", "", $this->fullSura->Name);
            $suraName = $this->fullSura->Name;
            $verseObject->Sura = $suraName;
            $verseObject->verseIndx = $index+1;

            $verseObject->verseText = $verseObject->verseString;
            $verseObject->verseText = $verseObject->verseString;
            $verseObject->numberOfWords = sizeof($verseObject->verseArray);
            $verseObject->numberOfLetters = $verseObject->countVerseLetters();

            $verseObject->letterOccurrences = $this->counter->countLettersInString($verse);
            $verseObject->letterIndexes = $this->indexer->indexLettersInString($verseObject->verseArray);

            $verseObject->wordOccurrences = $this->counter->countWordsInString($verse);
            $verseObject->wordIndexes = $this->indexer->indexWordsInString($verse);

            $returnArray[$index + 1] = $verseObject;
        }

        return $returnArray;
    }

    public function listSuras()
    {
        //if quranIndex file doesn't exist create one and read from it 
        // if(!file_exists(storage_path('quranIndex'))){
            $listOfSuras = [];
            $surasFiles = scandir(storage_path('decoded_suras'));
            $suraIndex = 0;
            $allSuras = [];
            $i=1;
            foreach ($surasFiles as $suraFile) {
                if (($suraFile != '.')&&($suraFile != '..')&&($suraFile != 'المصحف_data.json')&&($suraFile != 'المصحف_fe_data.json')) {
                    // if (!file_exists(storage_path($suraFile.'_data.json'))) {
                        $mappedSura = file_get_contents(storage_path('decoded_suras/' . $suraFile));
                        $mappedSura = json_decode($mappedSura, true);
                        // $surasFiles = (ksort($surasFiles));
                        // dd($mappedSura);
                        $indexInfo["fileName"] = $mappedSura["Name"];
                        $indexInfo["Name"] = $indexInfo["fileName"];     
                        $indexInfo["Name"] = preg_replace('/[0-9]+/', '', $indexInfo["Name"]);

                        $indexInfo["Name"] = str_replace("_" , "" , $indexInfo["Name"]);      
                        $indexInfo["suraIndex"] = $i;              
                        // dd(count($mappedSura["versesMap"])-1);
                        $indexInfo["numberOfVerses"] = count($mappedSura["versesMap"]);
                        $indexInfo["numberOfWords"] = $mappedSura["numberOfWords"];
                        $indexInfo["numberOfLetters"] = $mappedSura["numberOfLetters"];
                        array_push($allSuras, $indexInfo);
                    // }  
                 $i++;
                }
            }                
            file_put_contents(
                storage_path('quranIndexWithData'),
                json_encode($allSuras, JSON_UNESCAPED_UNICODE)
            );
        // }

        return file_get_contents(storage_path('quranIndexWithData')) ;
    } 

    public function deleteDirctory($directory){
        $files = scandir(storage_path($directory));
        foreach($files as $file){ 
            if (($file != '.')&&($file != '..')){
                // $fileContent = File::get(storage_path($directory).'/'.$file);
                if(is_file((storage_path($directory).'/'.$file))){
                    unlink(storage_path($directory).'/'.$file);
                }
            }
        }
    }

    // Run all the backend to create mapped suras and verses
    public function runBackend()
    {
        self::deleteDirctory('decoded_suras');
        self::deleteDirctory('decoded_verses');
        self::deleteDirctory('scored_verses');
        self::deleteDirctory('categorized_suras/verses_basics');
        self::deleteDirctory('categorized_suras/suras_basics');
        self::deleteDirctory('categorized_suras/search_basics');
        self::deleteDirctory('categorized_suras/details');
        // self::deleteDirctory('categorized_suras/details/LetterOccurrences');
        // self::deleteDirctory('categorized_suras/details/WordIndexes');
        // self::deleteDirctory('categorized_suras/details/WordOccurrences');
        
        $surasFiles = scandir(storage_path('SanatizedSuras'));
        foreach ($surasFiles as $suraFileName) {
            if (($suraFileName != '.')&&($suraFileName != '..')&&($suraFileName != 'المصحف')) {
                $suraFile = File::get(storage_path('SanatizedSuras' . '/' .$suraFileName));
                
                $this->fullSura = new FullSura($suraFile);
                $this->fullSura->Name = $suraFileName;

                $this->mapSura();
                $this->mapVerses();

                $fileToScore = new Request();
                $fileToScore->fileName = $suraFileName;

                $score = new ScoreController($fileToScore);
                $score->eachVerseScore();

            }
        }
        $this->mapComplete();
        
        $categorizer = new CategorizorService();
        $categorizer->categorize();

        return;
    }

    public function mapComplete(){
        $allSurasFiles = scandir(storage_path('decoded_suras'));
        $mappedQuran = [];
        $index = 1;
        // if(!file_exists(storage_path('decoded_suras/'.'المصحف_fe'.'_data.json'))){
            foreach ($allSurasFiles as $suraFile) {
                if (($suraFile != '.')&&($suraFile != '..')) {
                    $suraInfo = File::get(storage_path('decoded_suras/'.$suraFile));
                    $suraInfo = json_decode($suraInfo);
                    foreach($suraInfo->versesMap as $verseInfo){
                        $mappedQuran[$index] = $verseInfo;
                        $index++;
                    }
                }                   
            }
            file_put_contents(
                storage_path('decoded_suras/'.'المصحف'.'_data.json'),
                json_encode($mappedQuran, JSON_UNESCAPED_UNICODE)
            );
        // }
        $mappedQuran = file_get_contents(storage_path('decoded_suras/'.'المصحف'.'_data.json'));
        
        return $mappedQuran;
    }
}