<?php

namespace App\Livewire;

use App\Events\GamePlay\TeamBuzzed;
use App\Models\GameSession;
use App\Models\Team;
use Livewire\Attributes\On;
use Livewire\Component;

class TeamBuzzer extends Component
{
    public ?GameSession $session = null;
    public ?Team $team = null;
    public $sessionCode = '';
    public $teamCode = '';
    public $currentQuestion = null;
    public $buzzersEnabled = false;
    public $hasBuzzed = false;
    public $isSelected = false;
    public $showAnswer = false;
    public $questionStartTime = null;
    public $teams = [];

    public function mount($session = null, $team = null)
    {
        $this->session = $session;
        $this->team = $team;
        
        if ($this->session) {
            $this->loadTeams();
        }
    }

    public function loadTeams()
    {
        if ($this->session) {
            $this->teams = $this->session->teams()->orderBy('score', 'desc')->get();
        }
    }

    public function joinSession()
    {
        $this->validate([
            'sessionCode' => 'required|string',
            'teamCode' => 'required|string',
        ]);

        $session = GameSession::where('session_code', strtoupper($this->sessionCode))->first();
        
        if (!$session) {
            session()->flash('error', 'Invalid session code.');
            return;
        }

        $team = $session->teams()->where('buzzer_code', strtoupper($this->teamCode))->first();
        
        if (!$team) {
            session()->flash('error', 'Invalid team code.');
            return;
        }

        // Redirect to the team buzzer page with codes
        return redirect()->route('play.team', [
            'code' => $session->session_code,
            'team' => $team->buzzer_code
        ]);
    }

    #[On('echo:game-session.{session.id},QuestionRevealed')]
    public function onQuestionRevealed($data)
    {
        $this->currentQuestion = (object) $data['question'];
        $this->questionStartTime = now();
        $this->hasBuzzed = false;
        $this->isSelected = false;
        $this->showAnswer = false;
        $this->dispatch('question-revealed');
    }

    #[On('echo:game-session.{session.id},BuzzersEnabled')]
    public function onBuzzersEnabled($data)
    {
        $this->buzzersEnabled = $data['buzzers_enabled'];
        
        if (!$this->buzzersEnabled) {
            // Reset buzz state when buzzers are disabled
            $this->hasBuzzed = false;
        }
        
        $this->dispatch($data['buzzers_enabled'] ? 'buzzers-enabled' : 'buzzers-disabled');
    }

    #[On('echo:game-session.{session.id},TeamSelected')]
    public function onTeamSelected($data)
    {
        if ($this->team && $data['team']['id'] === $this->team->id) {
            $this->isSelected = true;
            $this->dispatch('team-selected');
        } else {
            $this->isSelected = false;
        }
    }

    #[On('echo:game-session.{session.id},AnswerRevealed')]
    public function onAnswerRevealed($data)
    {
        $this->showAnswer = true;
        if ($this->currentQuestion) {
            $this->currentQuestion->answer = $data['answer'];
        }
        $this->dispatch('answer-revealed');
    }

    #[On('echo:game-session.{session.id},ScoreUpdated')]
    public function onScoreUpdated($data)
    {
        $this->loadTeams();
        
        if ($this->team && $data['team']['id'] === $this->team->id) {
            $this->team->refresh();
            $this->dispatch('score-updated', isCorrect: $data['is_correct']);
        }
    }

    #[On('echo:game-session.{session.id},GameSessionEnded')]
    public function onSessionEnded($data)
    {
        $this->session->refresh();
        $this->loadTeams();
        $this->dispatch('session-ended');
    }

    public function buzz()
    {
        if (!$this->buzzersEnabled || $this->hasBuzzed || !$this->currentQuestion || !$this->team) {
            return;
        }

        $this->hasBuzzed = true;
        
        // Calculate response time
        $responseTimeMs = $this->questionStartTime 
            ? now()->diffInMilliseconds($this->questionStartTime)
            : 0;

        // Broadcast the buzz event
        broadcast(new TeamBuzzed($this->session, $this->team, $responseTimeMs))->toOthers();
        
        // Dispatch to teacher's component
        $this->dispatch('team-buzzed', teamId: $this->team->id, responseTimeMs: $responseTimeMs)->to(GamePlay::class);
        
        $this->dispatch('buzzed');
    }

    public function render()
    {
        if (!$this->session || !$this->team) {
            return view('livewire.team-buzzer-join');
        }
        
        return view('livewire.team-buzzer')->layout('layouts.buzzer');
    }
}