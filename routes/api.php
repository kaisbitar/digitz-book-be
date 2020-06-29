<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('allSurasData/all', 'ViewController@allSurasData');

//Examples:
// api/view/sura/suraName/نوح         api/view/verses/verseNumberToQuran/نوح
// api/view/sura/suraNumber/نوح         api/view/verses/verseNumber/نوح
// api/view/sura/numberOfWords/نوح         api/view/verses/verseText/نوح
// api/view/sura/numberOfLetters/نوح         api/view/verses/score/نوح
// api/view/sura/versesMap/نوح         api/view/verses/numberOfWords/نوح
// api/view/sura/wordsScores/نوح         api/view/verses/numberOfLetters/نوح
// api/view/sura/versesScores/نوح         api/view/verses/letterOccurrences/نوح
// api/view/sura/letterOccurrences/نوح         api/view/verses/letterIndexes/نوح
// api/view/sura/wordOccurrences/نوح         api/view/verses/wordOccurrences/نوح
// api/view/sura/wordIndex/نوح         api/view/verses/wordIndexes/نوح
// api/view/sura/letterIndexes/نوح         api/view/verses/score/نوح

Route::get('view/sura/{dataType}/{suraName}', 'ViewController@viewSuraElement');
Route::get('view/verses/{dataType}/{suraName}', 'ViewController@viewVerseElement');
Route::get('view/{suraName}', 'ViewController@viewSuraMap');
Route::get('quran-index', 'ViewController@viewQuranIndex');
Route::get('fileNames', 'ViewController@viewFileNames');
Route::get('view/verses-basics/{suraName}', 'ViewController@viewVersesBasics');

//files Categorizer
Route::get('categorize', 'CategorizerController@categorize');


// Backend calculations
Route::get('sura-map/{fileName}', 'CalculatorController@mapSura');
Route::get('verses-map/{fileName}', 'CalculatorController@mapVerses');
Route::get('letters-map/{fileName}', 'CalculatorController@mapLetters');
Route::get('sanatize', 'SanatizerController@createAllSurasFiles');

//send quraIndex
Route::get('quran-index/{fileName}', 'CalculatorController@listSuras');
Route::get('decode-all', 'CalculatorController@runBackend');

//you have to decode-all before running Quran map
Route::get('quran-map/{fileName}', 'CalculatorController@mapComplete');
Route::get('count-letters/{suraIndex}', 'CollecterController@countLetters');
Route::get('calculate-sura-19/{suraName}', 'CollecterController@calculateSura19');

//Scores:
Route::get('words-score/{fileName}', 'ScoreController@eachWordScore');
Route::get('verses-score/{fileName}', 'ScoreController@eachVerseScore');
Route::get('verse-score/{fileName}/{verseIndex}', 'ScoreController@singleVerseScore');
Route::get('find-19-in-sura/{fileName}', 'ScoreController@find19InSura');