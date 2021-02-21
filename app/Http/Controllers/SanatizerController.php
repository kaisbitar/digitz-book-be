<?php

namespace App\Http\Controllers;

use App\Sanatizer;
use App\SanatizerNew;
use Illuminate\Support\Facades\File;

class SanatizerController extends Controller
{
    public function createAllSurasFiles()
    {
        $sanatizer = new Sanatizer();
        // $sanatizer->createAllSurasFiles();
        // $sanatizer->createAllSurasFiles('SanatizedSuras/', 'view');
        // $sanatizer->createAllSurasFiles('toCalculateSuras/', 'calculate');
        $sanatizer->CreateoneQuranFile();
    }

    public function CreateoneQuranFile()
    {
        $sanatizer = new Sanatizer();
        $sanatizer->CreateoneQuranFile();
    }

    public function SanatizerNew()
    {
        $sanatizer = new SanatizerNew();
        $sanatizer->sanatizeSuras();
    }
    
}
