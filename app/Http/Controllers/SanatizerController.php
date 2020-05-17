<?php

namespace App\Http\Controllers;

use App\Sanatizer;
use Illuminate\Support\Facades\File;

class SanatizerController extends Controller
{
    public function createAllSurasFiles()
    {
        $sanatizer = new Sanatizer();
        $sanatizer->createAllSurasFiles();
    }

    public function CreateoneQuranFile()
    {
        $sanatizer = new Sanatizer();
        $sanatizer->CreateoneQuranFile();
    }
}
