<?php

namespace App\Http\Controllers\WatchTrend;

use App\Http\Controllers\Controller;
use App\Models\WatchtrendWatch;
use App\Models\WatchtrendWatchShare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ShareController extends Controller
{
    private function getOwnerWatch(int $watchId): WatchtrendWatch
    {
        return WatchtrendWatch::where('user_id', Auth::id())->findOrFail($watchId);
    }

    private function getOwnerShare(int $shareId): WatchtrendWatchShare
    {
        $share = WatchtrendWatchShare::with('watch')->findOrFail($shareId);

        if ($share->watch->user_id !== Auth::id()) {
            abort(403);
        }

        return $share;
    }

    public function index($watch)
    {
        $watch  = $this->getOwnerWatch((int) $watch);
        $shares = $watch->shares()->with('sharedWith')->orderByDesc('created_at')->get();

        return response()->json([
            'success' => true,
            'shares'  => $shares->map(fn ($s) => [
                'id'                => $s->id,
                'shared_with_email' => $s->shared_with_email,
                'permission'        => $s->permission,
                'accepted_at'       => $s->accepted_at?->toISOString(),
                'created_at'        => $s->created_at->toISOString(),
                'user_name'         => $s->sharedWith?->name,
            ]),
        ]);
    }

    public function invite(Request $request, $watch)
    {
        $watch = $this->getOwnerWatch((int) $watch);

        $validated = $request->validate([
            'email'      => 'required|email|max:255',
            'permission' => 'in:view,edit',
        ]);

        // Prevent owner from inviting themselves
        if ($validated['email'] === Auth::user()->email) {
            return response()->json(['success' => false, 'message' => 'Vous ne pouvez pas partager avec vous-même.'], 422);
        }

        // Check if share already exists
        if ($watch->shares()->where('shared_with_email', $validated['email'])->exists()) {
            return response()->json(['success' => false, 'message' => 'Une invitation a déjà été envoyée à cet email.'], 422);
        }

        $share = WatchtrendWatchShare::create([
            'watch_id'          => $watch->id,
            'shared_by_user_id' => Auth::id(),
            'shared_with_email' => $validated['email'],
            'permission'        => $validated['permission'] ?? 'view',
            'token'             => Str::random(64),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Invitation envoyée.',
            'share'   => [
                'id'                => $share->id,
                'shared_with_email' => $share->shared_with_email,
                'permission'        => $share->permission,
                'accepted_at'       => null,
                'created_at'        => $share->created_at->toISOString(),
                'user_name'         => null,
            ],
        ]);
    }

    public function accept($token)
    {
        $share = WatchtrendWatchShare::where('token', $token)->firstOrFail();

        // Already accepted
        if ($share->accepted_at) {
            return redirect()->route('watchtrend.dashboard')->with('success', 'Cette invitation a déjà été acceptée.');
        }

        $share->update([
            'shared_with_user_id' => Auth::id(),
            'accepted_at'         => now(),
        ]);

        return redirect()->route('watchtrend.dashboard')->with('success', 'Invitation acceptée ! La veille est maintenant accessible dans votre tableau de bord.');
    }

    public function updatePermission(Request $request, $share)
    {
        $share = $this->getOwnerShare((int) $share);

        $validated = $request->validate([
            'permission' => 'required|in:view,edit',
        ]);

        $share->update(['permission' => $validated['permission']]);

        return response()->json(['success' => true, 'message' => 'Permission mise à jour.']);
    }

    public function revoke($share)
    {
        $share = $this->getOwnerShare((int) $share);
        $share->delete();

        return response()->json(['success' => true, 'message' => 'Partage révoqué.']);
    }

    public function leave($watch)
    {
        $watch = WatchtrendWatch::findOrFail((int) $watch);

        $share = WatchtrendWatchShare::where('watch_id', $watch->id)
            ->where('shared_with_user_id', Auth::id())
            ->firstOrFail();

        $share->delete();

        return response()->json(['success' => true, 'message' => 'Vous avez quitté cette veille partagée.']);
    }
}
