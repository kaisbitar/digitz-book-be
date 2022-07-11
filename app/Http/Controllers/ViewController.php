<?php

namespace App\Http\Controllers;

ini_set('memory_limit', '5G');


use App\Http\Controllers\Controller;
use App\Services\CalculatorService;
use Illuminate\Http\Request;
use stdClass;

class Viewcontroller extends Controller
{
    public $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function viewSuraDetails()
    {
        $suraName = $this->request->suraName;
        $details = file_get_contents(storage_path('categorized_suras/suras_details/' . $suraName));
        $obj = new stdClass;
        $obj->wordIndexes = json_decode($details)->wordIndexes;

        return json_encode($obj);

    }
    public function viewOneQuranFile()
    {
        return file_get_contents(storage_path('categorized_suras/search_basics/oneQuranFile'));
    }
    public function viewAllVersesWithTashkeel()
    {
        return (file_get_contents(storage_path('allVersesWithTashkeel')));
    }
    public function viewQuranIndex()
    {
        $quranIndex = file_get_contents(storage_path('quranIndexWithData'));
        return $quranIndex;
    }
}
