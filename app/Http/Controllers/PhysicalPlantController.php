<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PhysicalPlantController extends Controller
{
    public function dashboard()
    {
        return view('ppi.dashboard');
    }
}
