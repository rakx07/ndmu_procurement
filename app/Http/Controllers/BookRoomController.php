<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookRoomController extends Controller
{
    public function dashboard()
    {
        return view('bookroom.dashboard');
    }
}
