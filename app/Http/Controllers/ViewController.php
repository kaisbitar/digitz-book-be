<?php

namespace App\Http\Controllers;

ini_set('memory_limit', '5G');


use App\Http\Controllers\Controller;
use App\Services\CalculatorService;
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

    public function viewSearchResults()
    {
        $query = ($this->request->searchQuery);
        $result = [];
        $mappedSura = (array)json_decode(file_get_contents(storage_path('decoded_suras/المصحف_data.json')));
        $mappedSura['suraString'] = '';
        $querySplit = preg_split('//u', $query, -1, PREG_SPLIT_NO_EMPTY);
        for ($i = 0; $i < count($querySplit); $i++) {
            $letterOcc = (array)$mappedSura['letterOccurrences'];
            // $letterIndexes = (array)$mappedSura['letterIndexes'];
            $result['letterOccurrences'][$querySplit[$i]] = $letterOcc[$querySplit[$i]];
            // $result['letterIndexes'][$querySplit[$i]] = $letterIndexes[$querySplit[$i]];
        }
        $wordOcc = (array)$mappedSura['wordOccurrences'];
        $wordIndexes = (array)$mappedSura['wordIndexes'];
        foreach ($wordOcc as $word => $occ) {

            if (mb_ereg($query, $word) !== false) {

                $result['wordOccurrences'][$word] = $occ;
                $result['wordIndexes'][$word] = $wordIndexes[$word];  
            }
        }

        return $result;
    }

    public function viewSuraDetails()
    {
        $suraName = $this->request->suraName;
        $details = file_get_contents(storage_path('categorized_suras/suras_details/' . $suraName));
        $obj = new stdClass;
        $obj->wordIndexes = json_decode($details)->wordIndexes;

        return json_encode($obj);

    }
    public function viewSuraCharts()
    {
        $suraName = $this->request->suraName;
        return file_get_contents(storage_path('categorized_suras/suras_charts/' . $suraName));
    }

    public function allSurasData()
    {
        return file_get_contents(storage_path('categorized_suras/search_basics/allSurasData'));
    }
    public function viewOneQuranFile()
    {
        return file_get_contents(storage_path('categorized_suras/search_basics/oneQuranFile'));
    }
    public function viewAllVersesWithTashkeel()
    {
        return (file_get_contents(storage_path('allVersesWithTashkeel')));
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
        $this->suraMap[$dataType] = $mappedSura->{$dataType};
        return $this->parseArrayToObj($this->suraMap[$dataType]);
    }
    public function parseArrayToObj($results)
    {
        $tmp = [];
        $index = 0;
        if (is_object($results)) {
            foreach ($results as $key => $value) {
                $tmp[$key] = $value;
                $index++;
            }
            return $tmp;
        } else    return json_encode($results);
    }
    public function viewVerseElement()
    {
        $suraName = $this->request->suraName;
        $mappedSura = file_get_contents(storage_path('decoded_suras/' . $suraName . '_data.json'));
        $mappedSura = json_decode($mappedSura);
        $dataType = $this->request->dataType;
        dd('Needs work!');
        return $this->parseVerseObject($dataType, $mappedSura);
    }

    public function viewQuranIndex()
    {
        $quranIndex = file_get_contents(storage_path('quranIndexWithData'));
        return $quranIndex;
    }

    public function viewFileNames()
    {
        $quranIndex = file_get_contents(storage_path('quranIndex'));
        return $quranIndex;
    }

    public function viewQuranString()
    {
        $file = file_get_contents(storage_path('allSurasDataRaw'));
        $file = json_decode($file);
        $allQuranString = "";

        foreach ($file as $value) {
            $allQuranString .= ($value->verseText) . ',';
        }
        file_put_contents(storage_path('rawQuranText'), ($allQuranString));

        return $allQuranString;
    }
}
