<?php

namespace App\Http\Controllers\WatchTrend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('watchtrend.dashboard.index');
    }

    public function suggestions()
    {
        return view('watchtrend.dashboard.index');
    }
}
