<?php

namespace App\Livewire;

use App\Models\Game;
use Livewire\Component;

class GameBoard extends Component
{
    public Game $game;

    public function mount(Game $game)
    {
        $this->game = $game;
    }

    public function render()
    {
        $this->game->load('categories.questions');
        
        return view('livewire.game-board');
    }
}