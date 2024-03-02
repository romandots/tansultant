<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class SystemMonitor extends Controller
{
    public function dashboard(): \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('dashboard');
    }
}