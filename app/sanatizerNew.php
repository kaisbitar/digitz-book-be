<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use stdClass;


class SanatizerNew extends Model
{
    public $allSurasData;    

    public function __construct()
    {
    }
    
    public function sanatizeSuras()
    {
        $allSurasData = json_decode(file_get_contents(storage_path('allSurasDataRaw')));
        $suraString = "";
        $arr =[];
        foreach($allSurasData as $key=>$value){
            
            $number = str_pad($value->suraNumber, 3, '0', STR_PAD_LEFT);
            $fileName = $value->sura;
            $fileName = preg_replace('/[0-9_]+/', '', $fileName .$number );
            $fileName = $fileName.$number;
            $obj = new stdClass();
            $obj->fileName = $fileName;
            $obj->verseIndex = $value->verseNumber;
            $obj->verseNumberToQuran = $value->verseNumberToQuran;
            $obj->verseText = $this->applyStringRules($value->verseText);

            array_push($arr, $obj);
        }
        file_put_contents(storage_path('categorized_suras/search_basics/oneQuranFile'), json_encode($arr ));
        
    }

    public function applyStringRules($string)
    {
        // $string = preg_replace('/ويءآدم/i', '/ويآدم/i', $string);
        
        $string = str_replace('يء', 'ي', $string);

        $string = str_replace('ءا', 'آ', $string);
        

        $string = str_replace('ويءادم', 'ويآدم', $string);
        $string = str_replace('يادم', 'يآدم', $string);
        $string = str_replace('ءادم', 'آدم', $string);
        $string = str_replace('لءآدم', 'لآدم', $string);

        $string = str_replace('رآ', 'رءا', $string);
        $string = str_replace('تبوآ','تبوءا', $string);
        $string = str_replace('جزآ', 'جزءا', $string);
        $string = str_replace('ورآ','ورءا', $string);
        $string = str_replace('ترآ', 'ترءا', $string);
        $string = str_replace('سوآ', 'سوءا', $string);
        $string = str_replace('ردآ', 'ردءا', $string);

        $string = str_replace('يستء', 'يست', $string);

        $string = str_replace('الءن', 'الن', $string);
        $string = str_replace('ألءن', 'ألن', $string);
        $string = str_replace('آلءن', 'آلن', $string);

        $string = str_replace('لءي', 'لي', $string);
        $string = str_replace('لءو', 'لو', $string);
        $string = str_replace('بءو', 'بؤ', $string);
        $string = str_replace('سءو', 'سؤ', $string);
        $string = str_replace('كءو', 'كؤ', $string);
        $string = str_replace('هءو', 'هؤ', $string);
        $string = str_replace('فءو', 'فؤ', $string);
        $string = str_replace('زءو', 'زؤ', $string);
        $string = str_replace('نءو', 'نؤ', $string);
        $string = str_replace('تءو', 'تؤ', $string);
        $string = str_replace('طءو', 'طؤ', $string);
        
        $string = str_replace('كءي', 'كي', $string);
        $string = str_replace('زءي', 'زي', $string);
        $string = str_replace('سءي', 'سي', $string);
        $string = str_replace('بءي', 'بي', $string);
        $string = str_replace('سء', 'س', $string);

        $string = str_replace('ىه', 'يه', $string);
        $string = str_replace('ىل', 'يل', $string);
        $string = str_replace('ىك', 'يك', $string);
        $string = str_replace('ىة', 'ية', $string);
        $string = str_replace('تىن', 'تين', $string);
        
        
        $string = str_replace('أفءدة', 'أفدة', $string);
        $string = str_replace('والأفءدة', 'والأفدة', $string);
        $string = str_replace('أنجىنا', 'أنجينا', $string);
        $string = str_replace('أفءدتهم', 'أفدتهم', $string);
        $string = str_replace('تجءرون', 'تجرون', $string);
        $string = str_replace('المستءخرين', 'المستخرين', $string);
        $string = str_replace('شطءه', 'شطه', $string);
        $string = str_replace('استءذن', 'استذن', $string);
        $string = str_replace('استءذنوك', 'استذنوك', $string);
        $string = str_replace('ووقىنا', 'ووقينا', $string);
        $string = str_replace('لخطءين', 'لخطين', $string);
        $string = str_replace('يجءرون', 'يجرون', $string);
        $string = str_replace('جءروا', 'جروا', $string);
        
        $string = str_replace('قرءان', 'قرآن', $string);
              
        return $string;   
    }   
}