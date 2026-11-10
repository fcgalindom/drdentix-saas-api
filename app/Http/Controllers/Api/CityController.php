<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;

class CityController extends Controller
{
    public function index()
    {
        return response()->json(
            City::orderBy('department')->orderBy('name')->get(['id', 'name', 'department'])
        );
    }
}
