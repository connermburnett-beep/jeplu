<?php

namespace App\Livewire;

use App\Events\GamePlay\AnswerRevealed;
use App\Events\GamePlay\BuzzersEnabled;
use App\Events\GamePlay\GameSessionEnded;
use App\Events\GamePlay\GameSessionStarted;
use App\Events\GamePlay\QuestionRevealed;
use App\Events\GamePlay\ScoreUpdated;
use App\Events\GamePlay\TeamSelected;
use App\Models\BuzzerEvent;
use App\Models\GameSession;
use App\Models\Question;
use App\Models\Team;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class GamePlay extends Component
{
    public GameSession $session;
    public $game;
    public $categories;
    public $teams;
    public $currentQuestion = null;
    public $buzzedTeams = [];
    public $buzzersEnabled = false;
    public $showAnswer = false;
    public $selectedTeamId = null;
    
    // Team management
    public $showTeamModal = false;
    public $teamName = '';
    public $teamColor = '#3B82F6';
    
    // Available colors for teams
    public $availableColors = [
        '#3B82F6', // Blue
        '#EF4444', // Red
        '#22C55E', // Green
        '#F59E0B', // Yellow
        '#8B5CF6', // Purple
        '#EC4899', // Pink
        '#14B8A6', // Teal
        '#F97316', // Orange
    ];

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
        
        $this->teams = $this->session->teams()->orderBy('created_at')->get();
        
        if ($this->session->current_question_id) {
            $this->currentQuestion = Question::with('category')->find($this->session->current_question_id);
        }
    }

    public function startSession()
    {
        $this->session->start();
        broadcast(new GameSessionStarted($this->session))->toOthers();
        
        $this->dispatch('session-started');
        session()->flash('success', 'Game session started!');
    }

    public function pauseSession()
    {
        $this->session->pause();
        $this->dispatch('session-paused');
        session()->flash('info', 'Game session paused.');
    }

    public function resumeSession()
    {
        $this->session->resume();
        $this->dispatch('session-resumed');
        session()->flash('success', 'Game session resumed!');
    }

    public function endSession()
    {
        $this->session->complete();
        broadcast(new GameSessionEnded($this->session))->toOthers();
        
        $this->dispatch('session-ended');
        session()->flash('success', 'Game session completed!');
    }

    public function revealQuestion(Question $question)
    {
        if (!$this->session->isActive()) {
            session()->flash('error', 'Session must be active to reveal questions.');
            return;
        }

        $this->session->update([
            'current_question_id' => $question->id,
            'question_started_at' => now(),
            'buzzed_team_id' => null,
        ]);

        $this->currentQuestion = $question->load('category');
        $this->buzzedTeams = [];
        $this->buzzersEnabled = false;
        $this->showAnswer = false;
        $this->selectedTeamId = null;

        broadcast(new QuestionRevealed($this->session, $question))->toOthers();
        
        $this->dispatch('question-revealed', questionId: $question->id);
    }

    public function enableBuzzers()
    {
        $this->buzzersEnabled = true;
        broadcast(new BuzzersEnabled($this->session, true))->toOthers();
        
        $this->dispatch('buzzers-enabled');
    }

    public function disableBuzzers()
    {
        $this->buzzersEnabled = false;
        broadcast(new BuzzersEnabled($this->session, false))->toOthers();
        
        $this->dispatch('buzzers-disabled');
    }

    #[On('team-buzzed')]
    public function handleTeamBuzzed($teamId, $responseTimeMs)
    {
        if (!$this->buzzersEnabled || !$this->currentQuestion) {
            return;
        }

        $team = Team::find($teamId);
        
        if (!$team || in_array($teamId, array_column($this->buzzedTeams, 'id'))) {
            return;
        }

        // Record the buzzer event
        BuzzerEvent::create([
            'game_session_id' => $this->session->id,
            'team_id' => $teamId,
            'question_id' => $this->currentQuestion->id,
            'buzzed_at' => now(),
            'response_time_ms' => $responseTimeMs,
        ]);

        $this->buzzedTeams[] = [
            'id' => $team->id,
            'name' => $team->name,
            'color' => $team->color,
            'response_time_ms' => $responseTimeMs,
        ];

        // Auto-disable buzzers after first buzz (can be changed based on game rules)
        $this->disableBuzzers();
    }

    public function selectTeam($teamId)
    {
        $team = Team::find($teamId);
        
        if (!$team) {
            return;
        }

        $this->selectedTeamId = $teamId;
        
        $this->session->update([
            'buzzed_team_id' => $teamId,
        ]);

        broadcast(new TeamSelected($this->session, $team))->toOthers();
        
        $this->dispatch('team-selected', teamId: $teamId);
    }

    public function awardPoints($teamId, $correct = true)
    {
        $team = Team::find($teamId);
        
        if (!$team || !$this->currentQuestion) {
            return;
        }

        $points = $this->currentQuestion->points;
        
        if ($correct) {
            $team->addPoints($points);
            
            // Mark question as answered
            $this->currentQuestion->update(['is_answered' => true]);
        } else {
            $team->subtractPoints($points);
        }

        broadcast(new ScoreUpdated($this->session, $team, $points, $correct))->toOthers();
        
        $this->loadGameData();
        $this->dispatch('score-updated', teamId: $teamId, correct: $correct);
        
        session()->flash('success', $correct ? 'Points awarded!' : 'Points deducted.');
    }

    public function revealAnswer()
    {
        if (!$this->currentQuestion) {
            return;
        }

        $this->showAnswer = true;
        broadcast(new AnswerRevealed($this->session, $this->currentQuestion))->toOthers();
        
        $this->dispatch('answer-revealed');
    }

    public function closeQuestion()
    {
        $this->currentQuestion = null;
        $this->buzzedTeams = [];
        $this->buzzersEnabled = false;
        $this->showAnswer = false;
        $this->selectedTeamId = null;
        
        $this->session->update([
            'current_question_id' => null,
            'question_started_at' => null,
            'buzzed_team_id' => null,
        ]);
        
        $this->dispatch('question-closed');
    }

    // Team Management Methods
    public function openTeamModal()
    {
        $this->showTeamModal = true;
        $this->teamName = '';
        $this->teamColor = $this->availableColors[0];
    }

    public function closeTeamModal()
    {
        $this->showTeamModal = false;
        $this->teamName = '';
        $this->reset(['teamName', 'teamColor']);
    }

    public function addTeam()
    {
        $this->validate([
            'teamName' => 'required|string|max:255',
            'teamColor' => 'required|string',
        ]);

        Team::create([
            'game_session_id' => $this->session->id,
            'name' => $this->teamName,
            'color' => $this->teamColor,
            'score' => 0,
        ]);

        $this->loadGameData();
        $this->closeTeamModal();
        
        session()->flash('success', 'Team added successfully!');
    }

    public function removeTeam($teamId)
    {
        $team = Team::find($teamId);
        
        if ($team &amp;&amp; $team->game_session_id === $this->session->id) {
            $team->delete();
            $this->loadGameData();
            
            session()->flash('success', 'Team removed successfully!');
        }
    }

    public function render()
    {
        return view('livewire.game-play');
    }
}