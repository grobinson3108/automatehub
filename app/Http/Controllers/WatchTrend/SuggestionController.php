<?php

namespace App\Http\Controllers\WatchTrend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SuggestionController extends Controller
{
    public function feedback(Request $request, $analysis)
    {
        return response()->json(['success' => true]);
    }

    public function markRead($item)
    {
        return response()->json(['success' => true]);
    }

    public function toggleFavorite($item)
    {
        return response()->json(['success' => true]);
    }
}
