<?php

namespace App\Http\Controllers;

use App\Services\CalculatorService;
use App\Counter;
use App\Http\Controllers\Controller;
use App\Indexer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CalculatorController extends Controller
{
    private $request;
    private $fullSura;
    private $counter;
    private $service;

    public function __construct($fileName = null, Request $request = null)
    { 
        $this->counter = new Counter();
        $this->indexer = new Indexer();
            
        if($request->path() == ("api/quran-index/quranIndex")){
            $fileName = 'quranIndex';
            $this->service = new CalculatorService(null, $fileName);
        }
        elseif($request->path() == ("api/decode-all")){
            $fileName = 'decode-all';
            $this->service = new CalculatorService(null, $fileName);
        }
        else {
            $fileName = $request->fileName;     
            $suraFile = File::get(storage_path('SanatizedSuras' . '/' .$fileName));
            if (!($suraFile)) {
                throw new \Exception("Sura file not found");
            }
            $this->service = new CalculatorService($suraFile, $fileName);
        }
    }

    public function mapSura()
    {   
        return $this->service->mapSura();
    }
    
    public function mapLetters()
    {   
        return $this->service->mapLetters();
    }
    
    public function mapVerses()
    {        
        return $this->service->mapVerses();
    }

    public function listSuras()
    {
        return $this->service->listSuras();
    }

    public function runBackend()
    {
        return $this->service->runBackend();
    }
    
    public function mapComplete(){
        
        return $this->service->mapComplete();
    }
}
