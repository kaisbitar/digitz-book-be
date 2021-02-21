<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Services\CategorizorService;
use Illuminate\Http\Request;
use stdClass;

class CategorizerController extends Controller
{
    public function categorize()
    {
        $this->service = new CategorizorService();
        return $this->service->categorize();
    }
    public function categorizeOneQuranFile()
    {
        $this->service = new CategorizorService();
        return $this->service->categorizeOneQuranFile();
    }
}