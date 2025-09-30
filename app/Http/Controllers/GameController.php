<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the user's games.
     */
    public function index()
    {
        $games = Auth::user()->games()
            ->with('categories.questions')
            ->latest()
            ->get();

        $canCreateMore = Game::canCreateGame(Auth::user());

        return view('games.index', compact('games', 'canCreateMore'));
    }

    /**
     * Show the form for creating a new game.
     */
    public function create()
    {
        if (!Game::canCreateGame(Auth::user())) {
            return redirect()->route('games.index')
                ->with('error', 'You have reached the maximum number of games for your subscription tier.');
        }

        return view('games.create');
    }

    /**
     * Store a newly created game in storage.
     */
    public function store(Request $request)
    {
        if (!Game::canCreateGame(Auth::user())) {
            return redirect()->route('games.index')
                ->with('error', 'You have reached the maximum number of games for your subscription tier.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $game = Auth::user()->games()->create($validated);

        // Create default settings for the game
        GameSettings::create([
            'game_id' => $game->id,
            'theme' => 'classic',
            'buzzer_timer' => 5,
        ]);

        return redirect()->route('games.edit', $game)
            ->with('success', 'Game created successfully! Now add categories and questions.');
    }

    /**
     * Show the form for editing the specified game.
     */
    public function edit(Game $game)
    {
        $this->authorize('update', $game);

        $game->load('categories.questions', 'settings');

        return view('games.edit', compact('game'));
    }

    /**
     * Update the specified game in storage.
     */
    public function update(Request $request, Game $game)
    {
        $this->authorize('update', $game);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $game->update($validated);

        return redirect()->route('games.edit', $game)
            ->with('success', 'Game updated successfully!');
    }

    /**
     * Remove the specified game from storage.
     */
    public function destroy(Game $game)
    {
        $this->authorize('delete', $game);

        $game->delete();

        return redirect()->route('games.index')
            ->with('success', 'Game deleted successfully!');
    }
}
