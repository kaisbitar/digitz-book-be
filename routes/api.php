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

// Route::get('allSurasData/all', 'ViewController@allSurasData');

Route::get('quran-index', 'ViewController@viewQuranIndex');
Route::get('fileNames', 'ViewController@viewFileNames');

Route::get('view/verses-basics/{suraName}', 'ViewController@viewVersesBasics');


Route::get('view/sura-details/{suraName}', 'ViewController@viewSuraDetails');
Route::get('view/sura-charts/{suraName}', 'ViewController@viewSuraCharts');
Route::get('view/sura-basics/{suraName}', 'ViewController@viewSuraBasics');
Route::get('view/sura-text/{suraName}', 'ViewController@viewSuraText');
Route::get('view/search/{searchQuery}', 'ViewController@viewSearchResults');
Route::get('allSurasData/all', 'ViewController@viewOneQuranFile');




//files Categorizer
Route::get('categorize', 'CategorizerController@categorize');


// Backend calculations
Route::get('sura-map/{fileName}', 'CalculatorController@mapSura');
Route::get('verses-map/{fileName}', 'CalculatorController@mapVerses');
Route::get('letters-map/{fileName}', 'CalculatorController@mapLetters');
Route::get('sanatize', 'SanatizerController@createAllSurasFiles');
Route::get('SanatizerNew', 'SanatizerController@SanatizerNew');

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