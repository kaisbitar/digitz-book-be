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
            
        $this->fullSura->numberOfWords = $this->fullSura->calculateNumberOfWords();
        $this->fullSura->numberOfLetters = $this->fullSura->calculateNumberOfLetters();
        $this->fullSura->suraString = implode(",", $this->fullSura->verses);
        $verses = $this->processVerses($this->fullSura->verses);

        $this->fullSura->versesMap = $verses;

        $this->fullSura->wordOccurrences = $this->counter->countWordsInString($this->fullSura->suraString);
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
            $verses = $this->processVerses($this->fullSura->verses);
            $verses["SuraLettersCount"] = $this->counter->countLettersInString($this->fullSura->suraString);
            $resultFileName = $this->fullSura->Name . '_data.json';
            file_put_contents(storage_path('decoded_verses/'. $resultFileName), json_encode($verses, JSON_UNESCAPED_UNICODE));
        $mappedSura = file_get_contents(storage_path('decoded_verses/' . $this->fullSura->Name . '_data.json'));
        
        return $mappedSura;
    }
    

    private function processVerses($verses)
    {       
        $returnArray = [];

        foreach ($verses as $index => $verse) {
            $verseObject = new Verse($verse, $index);

            $suraName = $this->fullSura->Name;
            $verseObject->Sura = $suraName;
            $verseObject->verseIndex = $index+1;

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
            $listOfSuras = [];
            $surasFiles = scandir(storage_path('decoded_suras'));
            $suraIndex = 0;
            $allSuras = [];
            $i=0;
            foreach ($surasFiles as $suraFile) {
                if (($suraFile != '.')&&($suraFile != '..')) {
                    $mappedSura = file_get_contents(storage_path('decoded_suras/' . $suraFile));
                    $mappedSura = json_decode($mappedSura, true);
                    $indexInfo["fileName"] = $mappedSura["Name"];
                    // $indexInfo["Name"] = $indexInfo["fileName"];     
                    // $indexInfo["Name"] = preg_replace('/[0-9]+/', '', $indexInfo["Name"]);

                    // $indexInfo["Name"] = str_replace("_" , "" , $indexInfo["Name"]);      
                    $indexInfo["numberOfVerses"] = count($mappedSura["versesMap"]);
                    $indexInfo["numberOfWords"] = $mappedSura["numberOfWords"];
                    $indexInfo["numberOfLetters"] = $mappedSura["numberOfLetters"];
                    array_push($allSuras, $indexInfo);
                 $i++;
                }
            }                
            file_put_contents(
                storage_path('quranIndexWithData'),
                json_encode($allSuras, JSON_UNESCAPED_UNICODE)
            );
        $this->addSuraIndexToQuran();
        return file_get_contents(storage_path('quranIndexWithData')) ;
    }

    public function addSuraIndexToQuran(){
        $quranIndex = json_decode(file_get_contents(storage_path('quranIndexWithData')));
        $oneQuranIndex = json_decode(file_get_contents(storage_path('categorized_suras/search_basics/oneQuranFile')));
        $i = 0;
        foreach ($quranIndex as $key => $value) {
            if($value->fileName !== '000المصحف'){
                $value->verseNumberToQuran = $oneQuranIndex[$i]->verseNumberToQuran;
                $i = $i + $value->numberOfVerses;
            }
        }
        file_put_contents(
            storage_path('quranIndexWithData'),
            json_encode($quranIndex, JSON_UNESCAPED_UNICODE)
        );
        
    }

    public function deleteDirctory($directory){
        $files = scandir(storage_path($directory));
        foreach($files as $file){ 
            if (($file != '.')&&($file != '..')){
                if(is_file((storage_path($directory).'/'.$file))){
                    unlink(storage_path($directory).'/'.$file);
                }
            }
        }
    }

    // Run all the backend to create mapped suras and verses
    public function runBackend()
    {
        $this->deleteDirctory('decoded_suras');
        $this->deleteDirctory('decoded_verses');
        $this->deleteDirctory('scored_verses');
        $this->deleteDirctory('categorized_suras/verses_basics');
        $this->deleteDirctory('categorized_suras/search_basics');
        $this->deleteDirctory('categorized_suras/suras_basics');
        $this->deleteDirctory('categorized_suras/suras_charts');
        $this->deleteDirctory('categorized_suras/suras_details');
        $this->deleteDirctory('categorized_suras/suras_text');
        
        dump( 'Directories deleted.');
        dump('Mapping suras and verses... ');
        
        $surasFiles = scandir(storage_path('SanatizedSuras'));
        foreach ($surasFiles as $suraFileName) {
            if (($suraFileName != '.')&&($suraFileName != '..')) {
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
        // $this->mapComplete();
        
        $categorizer = new CategorizorService();
        $categorizer->categorize();

        dump( 'Directories created.');

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