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
        file_put_contents(storage_path('SanatizedSuras/000المصحف'), $suraString);
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

        echo $suraString;
        echo '<br>';
        echo '<br>';
        echo '<br>';
        
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
            $wordsArr = explode(" ", $objToView[$versekey]->verseText);
            foreach($wordsArr as $wordKey => $wordVal){

                $wordsArr[$wordKey] = str_replace('يء', 'ي', $wordsArr[$wordKey]);

                $wordsArr[$wordKey] = str_replace('ءا', 'آ', $wordsArr[$wordKey]);
             

                $wordsArr[$wordKey] = str_replace('ويءادم', 'ويآدم', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('يادم', 'يآدم', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('ءادم', 'آدم', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('لءآدم', 'لآدم', $wordsArr[$wordKey]);

                $wordsArr[$wordKey] = str_replace('رآ', 'رءا', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('تبوآ','تبوءا', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('جزآ', 'جزءا', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('ورآ','ورءا', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('ترآ', 'ترءا', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('سوآ', 'سوءا', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('ردآ', 'ردءا', $wordsArr[$wordKey]);

                $wordsArr[$wordKey] = str_replace('يستء', 'يست', $wordsArr[$wordKey]);

                $wordsArr[$wordKey] = str_replace('الءن', 'الن', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('ألءن', 'ألن', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('آلءن', 'آلن', $wordsArr[$wordKey]);

                $wordsArr[$wordKey] = str_replace('لءي', 'لي', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('لءو', 'لو', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('بءو', 'بؤ', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('سءو', 'سؤ', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('كءو', 'كؤ', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('هءو', 'هؤ', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('فءو', 'فؤ', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('زءو', 'زؤ', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('نءو', 'نؤ', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('تءو', 'تؤ', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('طءو', 'طؤ', $wordsArr[$wordKey]);
                
                $wordsArr[$wordKey] = str_replace('كءي', 'كي', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('زءي', 'زي', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('سءي', 'سي', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('بءي', 'بي', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('سء', 'س', $wordsArr[$wordKey]);

                $wordsArr[$wordKey] = str_replace('ىه', 'يه', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('ىل', 'يل', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('ىك', 'يك', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('ىة', 'ية', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('تىن', 'تين', $wordsArr[$wordKey]);
                
                
                $wordsArr[$wordKey] = str_replace('والأفءدة', 'والأفدة', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('أنجىنا', 'أنجينا', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('أفءدتهم', 'أفدتهم', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('تجءرون', 'تجرون', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('المستءخرين', 'المستخرين', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('شطءه', 'شطه', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('استءذن', 'استذن', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('استءذنوك', 'استذنوك', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('ووقىنا', 'ووقينا', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('لخطءين', 'لخطين', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('يجءرون', 'يجرون', $wordsArr[$wordKey]);
                $wordsArr[$wordKey] = str_replace('جءروا', 'جروا', $wordsArr[$wordKey]);
                
                $wordsArr[$wordKey] = str_replace('قرءان', 'قرآن', $wordsArr[$wordKey]);
              
            }
            $objToView[$versekey]->verseText = implode(' ', $wordsArr);
        }

        return $objToView;
    }   
    
}

?>
