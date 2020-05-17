<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;


class Sanatizer extends Model
{
    public $allSurasData;    

    public function __construct()
    {
        $this->allSurasData = file_get_contents(storage_path('allSurasData'));
        $this->allSurasData = json_decode($this->allSurasData);
        $this->quranIndex = self::getQuranIndex($this->allSurasData);

    }
    public function CreateoneQuranFile()
    {
        self::applyStringRules();
        $suraString = self::getQuranString();
        file_put_contents(storage_path('SanatizedSuras/المصحف'), $suraString);
        dd( $suraString);
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

    public function createAllSurasFiles()
    {
        $quranIndex = $this->quranIndex;
        self::applyStringRules();
        // self::CreateoneQuranFile();
        File::deleteDirectory(storage_path('SanatizedSuras/'));
        File::makeDirectory(storage_path('SanatizedSuras/'));

        foreach($quranIndex as $key => $value){

            $suraName = $value;
            $suraName = preg_replace('/[0-9_]+/', '', $suraName);
            $suraString = self::getSuraString($suraName);
            $number = str_pad($key, 3, '0', STR_PAD_LEFT);
            
            file_put_contents(storage_path('SanatizedSuras/' . ($number) . $suraName), $suraString);

        }
        
    } 

    public function getQuranIndex()
    {
        $quranIndex = file_get_contents(storage_path('quranIndex'));
        $quranIndex = json_decode($quranIndex);

        return $quranIndex;
    }

    public function saveIndividualSura($readyToSaveSura, $fileNameToSave)
    {
        $sanataizedDir = fopen($this->cleanedSurasPath.'/'.$fileNameToSave, 'w');
        fwrite($sanataizedDir, $readyToSaveSura);
        fclose($sanataizedDir);
    }
    public function saveAllSuras($readyToSaveSura, $fileNameToSave, $suraNumber)
    {
        $readyToSaveSura = $readyToSaveSura . ",";
        // dump($readyToSaveSura);
        $sanataizedDir = fopen($this->cleanedSurasPath.'/المصحف', 'a');
        fwrite($sanataizedDir, $readyToSaveSura);
        fclose($sanataizedDir);
    }

    public function getSuraString($suraName)
    {
        $allSurasData = $this->allSurasData;
        $suraString = "";
        foreach($allSurasData as $key=>$value){
            if($allSurasData[$key]->sura == $suraName){
                if($key != sizeof($allSurasData)-1)
                $suraString .= $allSurasData[$key]->verseText . ",";   
            }
        }
        $suraString = rtrim($suraString, ",");

        echo $suraString;
        echo '<br>';
        echo '<br>';
        echo '<br>';
        echo '<br>';

        return $suraString;
    }

    public function applyStringRules()
    {
        foreach($this->allSurasData as $versekey => $value){

            // $this->allSurasData[$versekey]->verseText = str_replace('يء', 'ࢨ', $this->allSurasData[$versekey]->verseText);
            $this->allSurasData[$versekey]->verseText = str_replace('يء', 'ي', $this->allSurasData[$versekey]->verseText);
            $this->allSurasData[$versekey]->verseText = str_replace('ءا', 'آ', $this->allSurasData[$versekey]->verseText);
            $this->allSurasData[$versekey]->verseText = str_replace(' رأ ', ' رءا ', $this->allSurasData[$versekey]->verseText);
            $this->allSurasData[$versekey]->verseText = str_replace(' تبوأ  ', ' تبوءا ', $this->allSurasData[$versekey]->verseText);
            $this->allSurasData[$versekey]->verseText = str_replace(' جزأ ', ' جزءا ', $this->allSurasData[$versekey]->verseText);
            $this->allSurasData[$versekey]->verseText = str_replace(' ,ورأ  ', ' ورءا ', $this->allSurasData[$versekey]->verseText);
            $this->allSurasData[$versekey]->verseText = str_replace(' ترأ ', ' ترءا ', $this->allSurasData[$versekey]->verseText);
            $this->allSurasData[$versekey]->verseText = str_replace(' سوأ ', ' سوءا ', $this->allSurasData[$versekey]->verseText);
            $this->allSurasData[$versekey]->verseText = str_replace(' ردأ ', ' ردءا ', $this->allSurasData[$versekey]->verseText);

            $this->allSurasData[$versekey]->verseText = str_replace(' ويءادم ', ' ويأدم ', $this->allSurasData[$versekey]->verseText);
            $this->allSurasData[$versekey]->verseText = str_replace('يستء', 'يست', $this->allSurasData[$versekey]->verseText);

            $this->allSurasData[$versekey]->verseText = str_replace('الءن', 'الن', $this->allSurasData[$versekey]->verseText);
            $this->allSurasData[$versekey]->verseText = str_replace('ألءن', 'ألن', $this->allSurasData[$versekey]->verseText);

            $this->allSurasData[$versekey]->verseText = str_replace('لءي', 'لي', $this->allSurasData[$versekey]->verseText);
            $this->allSurasData[$versekey]->verseText = str_replace('لءو', 'لو', $this->allSurasData[$versekey]->verseText);
            $this->allSurasData[$versekey]->verseText = str_replace('بءو', 'بؤ', $this->allSurasData[$versekey]->verseText);
            $this->allSurasData[$versekey]->verseText = str_replace('سءو', 'سؤ', $this->allSurasData[$versekey]->verseText);
            $this->allSurasData[$versekey]->verseText = str_replace('كءو', 'كؤ', $this->allSurasData[$versekey]->verseText);
            $this->allSurasData[$versekey]->verseText = str_replace('هءو', 'هؤ', $this->allSurasData[$versekey]->verseText);
            $this->allSurasData[$versekey]->verseText = str_replace('فءو', 'فؤ', $this->allSurasData[$versekey]->verseText);
            $this->allSurasData[$versekey]->verseText = str_replace('زءو', 'زؤ', $this->allSurasData[$versekey]->verseText);
            $this->allSurasData[$versekey]->verseText = str_replace('نءو', 'نؤ', $this->allSurasData[$versekey]->verseText);
            $this->allSurasData[$versekey]->verseText = str_replace('تءو', 'تؤ', $this->allSurasData[$versekey]->verseText);
            $this->allSurasData[$versekey]->verseText = str_replace('طءو', 'طؤ', $this->allSurasData[$versekey]->verseText);
            
            $this->allSurasData[$versekey]->verseText = str_replace('كءي', 'كي', $this->allSurasData[$versekey]->verseText);
            $this->allSurasData[$versekey]->verseText = str_replace('زءي', 'زي', $this->allSurasData[$versekey]->verseText);
            $this->allSurasData[$versekey]->verseText = str_replace('سءي', 'سي', $this->allSurasData[$versekey]->verseText);
            $this->allSurasData[$versekey]->verseText = str_replace('بءي', 'بي', $this->allSurasData[$versekey]->verseText);
            $this->allSurasData[$versekey]->verseText = str_replace('سء', 'س', $this->allSurasData[$versekey]->verseText);

            $this->allSurasData[$versekey]->verseText = str_replace('ىه', 'يه', $this->allSurasData[$versekey]->verseText);
            $this->allSurasData[$versekey]->verseText = str_replace('ىل', 'يل', $this->allSurasData[$versekey]->verseText);
            $this->allSurasData[$versekey]->verseText = str_replace('ىك', 'يك', $this->allSurasData[$versekey]->verseText);
            $this->allSurasData[$versekey]->verseText = str_replace('ىة', 'ية', $this->allSurasData[$versekey]->verseText);
            $this->allSurasData[$versekey]->verseText = str_replace('تىن', 'تين', $this->allSurasData[$versekey]->verseText);
            
            
            //use for testing
            // if(strpos($this->allSurasData[$versekey]->verseText," و")){
            //     echo $this->allSurasData[$versekey]->verseText;
            //     echo '<br>';
            //     echo '<br>';
            // }
        }    

        return $this->allSurasData;
    }

    public function cleanSuraString_old($suraStringToClean)
    {
        $SuraArrayToClean = explode(' ', $suraStringToClean);
        $tmpToClean= array();
        $cleanedSura= array();
        foreach($SuraArrayToClean as $key=>$ArrayElement) {
            $ArrayElement = str_replace("&nbsp;", "", $ArrayElement);
            $ArrayElement = str_replace("\n","",$ArrayElement);
            $ArrayElement = str_replace("\r","",$ArrayElement); 
            $ArrayElement = str_replace(" ","",$ArrayElement); 
            $tmpToClean[$key] = $ArrayElement;
        }
        
        $tmpToClean = implode(" ", $tmpToClean);    
        $cleanedSura = strstr($tmpToClean, 'انتهت', true); 
        $cleanedSura = str_replace('(', '', $cleanedSura); 
        $cleanedSura = str_replace(')', '', $cleanedSura); 
        $cleanedSura = str_replace(',', '', $cleanedSura); 
        $cleanedSura = preg_replace('/[0-9]+/', ',', $cleanedSura);
        $cleanedSura = str_replace(' , ', ',', $cleanedSura); 
        $cleanedSura = str_replace(' ,', ',', $cleanedSura);  
        $cleanedSura = str_replace(', ', ',', $cleanedSura); 
        $cleanedSura = str_replace('الاالذين', 'الا الذين', $cleanedSura);  
        $cleanedSura = str_replace('البحربما ينفع الناس', 'البحر بما ينفع الناس', $cleanedSura); 
        $cleanedSura = str_replace('قومامسرفين', 'قوما مسرفين', $cleanedSura); 
        $cleanedSura = str_replace('فوقهافاما', 'فوقها فاما', $cleanedSura);   
        $cleanedSura = str_replace('تحصوهاإن', 'تحصوها إن', $cleanedSura);  
        $cleanedSura = str_replace(' احى ', ' أحي ', $cleanedSura); 
        $cleanedSura = str_replace('واوليك', 'واولئك', $cleanedSura); 
        $cleanedSura = str_replace('لايخفىعليه', 'لايخفى عليه', $cleanedSura);  
        $cleanedSura = str_replace('تلبسونهاوترى', 'تلبسونها وترى', $cleanedSura); 
        $cleanedSura = str_replace(' شىء ', ' شئ ', $cleanedSura); 
        $cleanedSura = str_replace('بريء', 'برئ', $cleanedSura);  
        $cleanedSura = str_replace('الخبء', 'الخبئ', $cleanedSura);   
        $cleanedSura = str_replace(' قلبى ', ' قلبي ', $cleanedSura);    
        
        $cleanedSura = str_replace(' من ولى ', ' من ولي ', $cleanedSura);    
        $cleanedSura = str_replace(' وايى ', ' وايي ', $cleanedSura);    
        $cleanedSura = str_replace(' بعهدى ', ' بعهدي ', $cleanedSura);    
        $cleanedSura = str_replace(' بيتى ', ' بيتي ', $cleanedSura);    
        $cleanedSura = str_replace(' عهدى ', ' عهدي ', $cleanedSura);    
        $cleanedSura = str_replace(' ذريتى ', ' ذريتي ', $cleanedSura);    
        $cleanedSura = str_replace('انى جاعل', 'اني جاعل', $cleanedSura);    
        $cleanedSura = str_replace('انى اعلم', 'اني اعلم', $cleanedSura);    
        $cleanedSura = str_replace('انى فضلتكم', 'اني فضلتكم', $cleanedSura);    
        $cleanedSura = str_replace('الا امانى', 'الا اماني', $cleanedSura);    
        $cleanedSura = str_replace('وانى فضلتكم', 'واني فضلتكم', $cleanedSura);    
        $cleanedSura = str_replace('فانى قريب', 'فاني قريب', $cleanedSura);    
        $cleanedSura = str_replace('انى جاعلك', 'اني جاعلك', $cleanedSura);   
        $cleanedSura = str_replace('انى', 'اني', $cleanedSura); 
        $cleanedSura = str_replace('عبادى عنى', 'عبادي عني', $cleanedSura); 
        $cleanedSura = str_replace('والله غنى', 'والله غني', $cleanedSura); 
        $cleanedSura = str_replace('وليؤمنوا بى', 'وليؤمنوا بي', $cleanedSura); 
        $cleanedSura = str_replace('فهى', 'فهي', $cleanedSura); 
        $cleanedSura = str_replace('ياتينكم منى هدى فمن تبع هداى', 'ياتينكم مني هدى فمن تبع هداي', $cleanedSura); 
        
        
        
        // لى 
        // بى 
        $cleanedSura = str_replace('مثليها قلتم اني هذا', 'مثليها قلتم انى هذا', $cleanedSura);   
        $cleanedSura = str_replace('اني يكون له ولد ولم', 'انى يكون له ولد ولم', $cleanedSura);   
        $cleanedSura = str_replace('اني يكون له الملك', 'انى يكون له الملك', $cleanedSura);   
        $cleanedSura = str_replace('يمريم اني لك هذا', 'يمريم انى لك هذا', $cleanedSura);   
        $cleanedSura = str_replace('اني يكون لي ولد', 'انى يكون لي ولد', $cleanedSura);   
        $cleanedSura = str_replace('حرثكم اني شيتم', 'حرثكم انى شيتم', $cleanedSura);   
        $cleanedSura = str_replace('قال رب اني يكون لي غلم', 'قال رب انى يكون لي غلم', $cleanedSura);   
        $cleanedSura = str_replace('فاني تؤفكون', 'فانى تؤفكون', $cleanedSura);   
        $cleanedSura = str_replace('اني تؤفكون', 'انى تؤفكون', $cleanedSura);    
        $cleanedSura = str_replace(' كفي ', ' كفى ', $cleanedSura);    
        $cleanedSura = str_replace(' وكفي ', ' وكفى ', $cleanedSura);    
        $cleanedSura = str_replace(' فكفي بالله ', ' فكفى بالله ', $cleanedSura);    
         
               
        
        //important postion
        $cleanedSura = str_replace('جاءوقال', 'جاءو قال', $cleanedSura);   
        $cleanedSura = str_replace('وذى', 'وذي', $cleanedSura);   
        $cleanedSura = str_replace('صم بكم عمى', 'صم بكم عمي', $cleanedSura);   
        $cleanedSura = str_replace('الا اذىوان', 'الا اذى وان', $cleanedSura);   
        $cleanedSura = str_replace('الجار ذي القربي', 'الجار ذي القربي', $cleanedSura);   
        $cleanedSura = str_replace('خزى', 'خزي', $cleanedSura);   
        $cleanedSura = str_replace('سيء', 'سئ', $cleanedSura);   
        $cleanedSura = str_replace('فىابرهيم', 'في ابرهيم', $cleanedSura);   
        $cleanedSura = str_replace('العلى العظيم', 'العلي العظيم', $cleanedSura);   
        $cleanedSura = str_replace(' الحى ', ' الحي ', $cleanedSura);   
        $cleanedSura = str_replace(' الغى ', ' الغي ', $cleanedSura);   
        $cleanedSura = str_replace('الله ولى', 'الله ولي', $cleanedSura);   

        $cleanedSura = str_replace(' وقضى الامر ', ' وقضي الامر ', $cleanedSura);   
        $cleanedSura = str_replace(' بنى ', ' بني ', $cleanedSura);   
        $cleanedSura = str_replace(' عفى ', ' عفي ', $cleanedSura);   
        $cleanedSura = str_replace(' ياولى ', ' ياولي ', $cleanedSura);   
        $cleanedSura = str_replace(' بشىء ', ' بشي ', $cleanedSura);   
        $cleanedSura = str_replace(' لى ', ' لي ', $cleanedSura);   
        $cleanedSura = str_replace(' فى ', ' في ', $cleanedSura);   
        $cleanedSura = str_replace('دفء', 'دفئ', $cleanedSura);  
        $cleanedSura = str_replace(' وهى ', ' وهي ', $cleanedSura);         
        $cleanedSura = str_replace(' هى ', ' هي ', $cleanedSura);         
        $cleanedSura = str_replace('ءاتخذ', 'أاتخذ', $cleanedSura);         
        $cleanedSura = str_replace('ءآلله', 'آالله', $cleanedSura);         
        $cleanedSura = str_replace('ءالله', 'آالله', $cleanedSura);
        $cleanedSura = str_replace('ءأنت', 'أانت', $cleanedSura);   
        $cleanedSura = str_replace('ءأرباب', 'أارباب', $cleanedSura);   
        $cleanedSura = str_replace('ءأسجد', 'أاسجد', $cleanedSura);   
        $cleanedSura = str_replace('ءأشكر', 'أاشكر', $cleanedSura);       
        $cleanedSura = str_replace('ءانذرتهم', 'أانذرتهم', $cleanedSura);         
        $cleanedSura = str_replace('ء انذرتهم', 'أانذرتهم', $cleanedSura);         
        $cleanedSura = str_replace('ء ال يعقوب', 'آل يعقوب', $cleanedSura);         
        $cleanedSura = str_replace('وءاخرجني', 'واخرجني', $cleanedSura);         
        $cleanedSura = str_replace('وءاخرجنا', 'واخرجنا', $cleanedSura);         
        $cleanedSura = str_replace('ءاعجمي', 'أاعجمي', $cleanedSura);         
        $cleanedSura = str_replace('ءانتم', 'أانتم', $cleanedSura); 
        $cleanedSura = str_replace(',ءاشفقتم', ',ااشفقتم', $cleanedSura); 
        $cleanedSura = str_replace(',ءأمنتم', ',اامنتم', $cleanedSura);
        $cleanedSura = str_replace('ولقاىءالاخرة', 'ولقاء الاخرة', $cleanedSura); 
        $cleanedSura = str_replace('ولءامنينهمولامرنهم', 'ولامنينهم ولامرنهم', $cleanedSura);  //PHP can't find the sunstring, although it exist in the sting hmmmm 
        //important that this step comes after              
        //  
        $cleanedSura = str_replace(' ءا', ' ا', $cleanedSura);         
        $cleanedSura = str_replace(',وءا', ',وا', $cleanedSura);        
        $cleanedSura = str_replace(' وءا', ' وا', $cleanedSura);  
        $cleanedSura = str_replace(',ءا', ',ا', $cleanedSura);        
        $cleanedSura = str_replace('أ', 'ا', $cleanedSura);        
        $cleanedSura = str_replace('آ', 'ا', $cleanedSura);        
        $cleanedSura = str_replace('إ', 'ا', $cleanedSura);  
        
        //important position
        $cleanedSura = str_replace('ءا', 'ا', $cleanedSura); 

        //important postion
        $cleanedSura = str_replace(' را ', ' رءا ', $cleanedSura);
        $cleanedSura = str_replace(' واخيه ان تبوا ', ' واخيه ان تبوءا ', $cleanedSura);
        $cleanedSura = str_replace(' جزا ', ' جزءا ', $cleanedSura);
        $cleanedSura = str_replace(' ورا ', ' ورءا ', $cleanedSura);
        $cleanedSura = str_replace(' ترا ', ' ترءا ', $cleanedSura);
        $cleanedSura = str_replace(' سوا ', ' سوءا ', $cleanedSura);
        $cleanedSura = str_replace(' ردا ', ' ردءا ', $cleanedSura);

        $cleanedSura = str_replace('منهن جزا', ' منهن جزءا', $cleanedSura);   
        $cleanedSura = str_replace('له من عباده جزا', 'له من عباده جزءا', $cleanedSura);   
        $cleanedSura = str_replace('ارنى', 'ارني', $cleanedSura);  
        $cleanedSura = str_replace('ربى', 'ربي', $cleanedSura); 
        $cleanedSura = str_replace('ياتى', 'ياتي', $cleanedSura); 
        $cleanedSura = str_replace('تحى', 'تحي', $cleanedSura); 
        $cleanedSura = str_replace('شئ', 'شي', $cleanedSura); 
        $cleanedSura = str_replace('وتثبيتامن', 'وتثبيتا من', $cleanedSura); 
        $cleanedSura = str_replace('شيء', 'شي', $cleanedSura); 
        $cleanedSura = str_replace('الذى', 'الذي', $cleanedSura);  
        $cleanedSura = str_replace('ابرهيملابيه', 'ابرهيم لابيه', $cleanedSura);  
        $cleanedSura = str_replace('بلقاىءربهم', 'بلقاء ربهم', $cleanedSura); 
        $cleanedSura = str_replace('مصلىوعهدنا', 'مصلى وعهدنا', $cleanedSura);  
        $cleanedSura = str_replace('يحى', 'يحي', $cleanedSura);  
        $cleanedSura = str_replace('قال اني يحي هذه', 'قال انى يحي هذه', $cleanedSura);   
        $cleanedSura = str_replace('آناى', 'انائ', $cleanedSura);  
        $cleanedSura = str_replace('لايخفى', 'لا يخفى', $cleanedSura); 
        $cleanedSura = mb_substr($cleanedSura, 0, -1);
        $cleanedSura = str_replace('عمران,', '', $cleanedSura); 
        $cleanedSura = ltrim($cleanedSura, ',');
        $cleanedSura = ltrim($cleanedSura, ' ');
        $cleanedSura = rtrim($cleanedSura, '  ');
        $cleanedSura = (strip_tags($cleanedSura));

        substr_replace($cleanedSura, '.', -1, 0);

        $readyToSaveString = $cleanedSura;
        if($suraName == "آل"){
            $suraName = "آل_عمران";
       }
        echo $suraName;       
        // echo strlen($readyToSaveString);

        echo ":<br><br>";
        echo($readyToSaveString);
        echo "<br><br><br>";
        $readyToSaveSura["theSura"] = $readyToSaveString;
        $readyToSaveSura["suraName"] = $suraName;

      return $readyToSaveSura;
    }        
    

    
}

?>
