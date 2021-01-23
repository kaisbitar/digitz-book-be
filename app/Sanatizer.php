<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;


class Sanatizer extends Model
{
    public $allSurasData;    

    public function __construct()
    {
        $this->allSurasData = file_get_contents(storage_path('allSurasDataRaw'));
        $this->allSurasData = json_decode($this->allSurasData);
        $this->quranIndex = $this->getQuranIndex($this->allSurasData);

    }
    public function CreateoneQuranFile()
    {
        $this->applyStringRules();
        $suraString = $this->getQuranString();
        file_put_contents(storage_path('SanatizedSuras/المصحف'), $suraString);
    } 

    public function getQuranString()
    {
        $allSurasData = $this->allSurasData;
        $suraString = "";

        foreach($allSurasData as $key=>$value){
            $suraString .= $allSurasData[$key]->verseText . ",";   
        }

        $suraString = rtrim($suraString, ",");

        return $suraString;
    }
    
    public function createAllSurasFiles($folder, $type)
    { 
        $quranIndex = $this->quranIndex; 

        if($type == 'view'){
        $obj = $this->applyStringRules();}

        else{
            $obj = $this->prepareToCalculate();
        }

        File::deleteDirectory(storage_path($folder));
        File::makeDirectory(storage_path($folder));

        foreach($quranIndex as $key => $value){

            $suraName = $value;
            $suraName = preg_replace('/[0-9_]+/', '', $suraName);
            $string = $this->getSuraString($suraName, $obj);
            $number = str_pad($key, 3, '0', STR_PAD_LEFT);
            file_put_contents(storage_path($folder . ($number) . $suraName), $string);

        }
        
    } 

    public function getQuranIndex()
    {
        $quranIndex = file_get_contents(storage_path('quranIndex'));
        $quranIndex = json_decode($quranIndex);

        return $quranIndex;
    }


    public function getSuraString($suraName, $suraObj)
    {
        $suraString = "";
        foreach($suraObj as $key=>$value){
            if($suraObj[$key]->sura == $suraName){
                if($key != sizeof($suraObj)-1)
                $suraString .= $suraObj[$key]->verseText . ",";   
            }
        }
        $suraString = rtrim($suraString, ",");
        return $suraString;
    }

    public function prepareToCalculate()
    {
        $objToCal = $this->allSurasData; 

        foreach($objToCal as $versekey => $value){
            $objToCal[$versekey]->verseText = str_replace('ء', 'ا', $objToCal[$versekey]->verseText);
            $objToCal[$versekey]->verseText = str_replace('ى', 'ا', $objToCal[$versekey]->verseText);
            $objToCal[$versekey]->verseText = str_replace('أ', 'ا', $objToCal[$versekey]->verseText);
            $objToCal[$versekey]->verseText = str_replace('إ', 'ا', $objToCal[$versekey]->verseText);
            $objToCal[$versekey]->verseText = str_replace('آ', 'ا', $objToCal[$versekey]->verseText);

            $objToCal[$versekey]->verseText = str_replace('ؤ', 'و', $objToCal[$versekey]->verseText);


            $objToCal[$versekey]->verseText = str_replace('ئ', 'ي', $objToCal[$versekey]->verseText);

            $objToCal[$versekey]->verseText = str_replace('ة', 'ه', $objToCal[$versekey]->verseText);
        }
        
        return $objToCal;


    }

    public function applyStringRules()
    {
        $objToView = $this->allSurasData; 

        foreach($objToView as $versekey => $value){

            // $objToView[$versekey]->verseText = str_replace('يء', 'ࢨ', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace('يء', 'ي', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace('ءا', 'آ', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace(' رأ ', ' رءا ', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace(' تبوأ  ', ' تبوءا ', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace(' جزأ ', ' جزءا ', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace(' ,ورأ  ', ' ورءا ', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace(' ترأ ', ' ترءا ', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace(' سوأ ', ' سوءا ', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace(' ردأ ', ' ردءا ', $objToView[$versekey]->verseText);

            $objToView[$versekey]->verseText = str_replace(' ويءادم ', ' ويآدم ', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace('يستء', 'يست', $objToView[$versekey]->verseText);

            $objToView[$versekey]->verseText = str_replace('الءن', 'الن', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace('ألءن', 'ألن', $objToView[$versekey]->verseText);

            $objToView[$versekey]->verseText = str_replace('لءي', 'لي', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace('لءو', 'لو', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace('بءو', 'بؤ', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace('سءو', 'سؤ', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace('كءو', 'كؤ', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace('هءو', 'هؤ', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace('فءو', 'فؤ', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace('زءو', 'زؤ', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace('نءو', 'نؤ', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace('تءو', 'تؤ', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace('طءو', 'طؤ', $objToView[$versekey]->verseText);
            
            $objToView[$versekey]->verseText = str_replace('كءي', 'كي', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace('زءي', 'زي', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace('سءي', 'سي', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace('بءي', 'بي', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace('سء', 'س', $objToView[$versekey]->verseText);

            $objToView[$versekey]->verseText = str_replace('ىه', 'يه', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace('ىل', 'يل', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace('ىك', 'يك', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace('ىة', 'ية', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace('تىن', 'تين', $objToView[$versekey]->verseText);
            
            
            $objToView[$versekey]->verseText = str_replace('والأفءدة', 'والأفدة', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace('أنجىنا', 'أنجينا', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace('أفءدتهم', 'أفدتهم', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace('تجءرون', 'تجرون', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace('المستءخرين', 'المستخرين', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace('شطءه', 'شطه', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace('استءذن', 'استذن', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace('استءذنوك', 'استذنوك', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace('ووقىنا', 'ووقينا', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace('لخطءين', 'لخطين', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace('يجءرون', 'يجرون', $objToView[$versekey]->verseText);
            $objToView[$versekey]->verseText = str_replace('جءروا', 'جروا', $objToView[$versekey]->verseText);
        }

        return $objToView;
    }   
    
}

?>
