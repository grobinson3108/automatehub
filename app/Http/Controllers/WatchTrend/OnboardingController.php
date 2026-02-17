<?php

namespace App\Http\Controllers\WatchTrend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function index()
    {
        return view('watchtrend.dashboard.index');
    }

    public function saveInterests(Request $request)
    {
        return redirect()->route('watchtrend.onboarding.index');
    }

    public function saveSources(Request $request)
    {
        return redirect()->route('watchtrend.onboarding.index');
    }

    public function calibration()
    {
        return view('watchtrend.dashboard.index');
    }

    public function submitCalibrationFeedback(Request $request)
    {
        return response()->json(['success' => true]);
    }

    public function saveFrequency(Request $request)
    {
        return redirect()->route('watchtrend.onboarding.index');
    }

    public function complete()
    {
        return redirect()->route('watchtrend.dashboard');
    }
}
