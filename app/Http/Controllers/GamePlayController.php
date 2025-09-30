<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GamePlayController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['presentation', 'team']);
    }

    /**
     * Start a new game session (Teacher view)
     */
    public function start(Game $game)
    {
        $this->authorize('view', $game);

        // Create a new game session
        $session = GameSession::create([
            'game_id' => $game->id,
            'user_id' => Auth::id(),
            'status' => 'waiting',
        ]);

        return redirect()->route('game-play.teacher', $session);
    }

    /**
     * Teacher control view
     */
    public function teacher(GameSession $session)
    {
        $this->authorize('view', $session->game);

        $session->load('game.categories.questions', 'teams');

        return view('game-play.teacher', compact('session'));
    }

    /**
     * Presentation view (for projector/board)
     */
    public function presentation(GameSession $session)
    {
        $session->load('game.categories.questions', 'teams', 'currentQuestion');

        return view('game-play.presentation', compact('session'));
    }

    /**
     * Team/Student view (buzzer interface)
     */
    public function team(Request $request)
    {
        $sessionCode = $request->query('code');
        $teamCode = $request->query('team');

        if (!$sessionCode) {
            return view('game-play.join');
        }

        $session = GameSession::where('session_code', $sessionCode)->firstOrFail();
        $team = null;

        if ($teamCode) {
            $team = $session->teams()->where('buzzer_code', $teamCode)->first();
        }

        return view('game-play.team', compact('session', 'team'));
    }

    /**
     * End game session
     */
    public function end(GameSession $session)
    {
        $this->authorize('view', $session->game);

        $session->complete();

        return redirect()->route('games.index')
            ->with('success', 'Game session completed!');
    }
}