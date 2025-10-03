<?php

namespace App\Livewire;

use App\Models\GameSession;
use App\Models\Question;
use Livewire\Attributes\On;
use Livewire\Component;

class PresentationView extends Component
{
    public GameSession $session;
    public $game;
    public $categories;
    public $teams;
    public $currentQuestion = null;
    public $showAnswer = false;
    public $selectedTeam = null;
    public $buzzersEnabled = false;

    public function mount(GameSession $session)
    {
        $this->session = $session;
        $this->game = $session->game;
        $this->loadGameData();
    }

    public function loadGameData()
    {
        $this->categories = $this->game->categories()
            ->with(['questions' => function ($query) {
                $query->orderBy('order');
            }])
            ->orderBy('order')
            ->get();
        
        $this->teams = $this->session->teams()->orderBy('score', 'desc')->get();
        
        if ($this->session->current_question_id) {
            $this->currentQuestion = Question::with('category')->find($this->session->current_question_id);
        }
        
        if ($this->session->buzzed_team_id) {
            $this->selectedTeam = $this->teams->firstWhere('id', $this->session->buzzed_team_id);
        }
    }

    #[On('echo:game-session.{session.id},GameSessionStarted')]
    public function onSessionStarted($data)
    {
        $this->session->refresh();
        $this->dispatch('session-started');
    }

    #[On('echo:game-session.{session.id},QuestionRevealed')]
    public function onQuestionRevealed($data)
    {
        $this->currentQuestion = Question::with('category')->find($data['question']['id']);
        $this->showAnswer = false;
        $this->selectedTeam = null;
        $this->buzzersEnabled = false;
        $this->dispatch('question-revealed');
    }

    #[On('echo:game-session.{session.id},BuzzersEnabled')]
    public function onBuzzersEnabled($data)
    {
        $this->buzzersEnabled = $data['buzzers_enabled'];
        $this->dispatch($data['buzzers_enabled'] ? 'buzzers-enabled' : 'buzzers-disabled');
    }

    #[On('echo:game-session.{session.id},TeamSelected')]
    public function onTeamSelected($data)
    {
        $this->selectedTeam = (object) $data['team'];
        $this->dispatch('team-selected');
    }

    #[On('echo:game-session.{session.id},AnswerRevealed')]
    public function onAnswerRevealed($data)
    {
        $this->showAnswer = true;
        $this->dispatch('answer-revealed');
    }

    #[On('echo:game-session.{session.id},ScoreUpdated')]
    public function onScoreUpdated($data)
    {
        $this->loadGameData();
        $this->dispatch('score-updated', isCorrect: $data['is_correct']);
    }

    #[On('echo:game-session.{session.id},GameSessionEnded')]
    public function onSessionEnded($data)
    {
        $this->session->refresh();
        $this->loadGameData();
        $this->dispatch('session-ended');
    }

    public function render()
    {
        return view('livewire.presentation-view')->layout('layouts.presentation');
    }
}