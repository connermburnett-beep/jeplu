<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Game;
use App\Models\Question;
use Livewire\Component;

class QuestionManager extends Component
{
    public Category $category;
    public Game $game;
    public $questions;
    public $showAddForm = false;
    public $editingQuestionId = null;

    // Form fields
    public $question = '';
    public $answer = '';
    public $points = 100;
    public $time_limit = 30;

    protected $rules = [
        'question' => 'required|string',
        'answer' => 'required|string',
        'points' => 'required|integer|min:0',
        'time_limit' => 'required|integer|min:5|max:300',
    ];

    public function mount(Category $category, Game $game)
    {
        $this->category = $category;
        $this->game = $game;
        $this->loadQuestions();
    }

    public function loadQuestions()
    {
        $this->questions = $this->category->questions()->orderBy('order')->get();
    }

    public function toggleAddForm()
    {
        $this->showAddForm = !$this->showAddForm;
        $this->resetForm();
    }

    public function addQuestion()
    {
        // Check if user can add more questions
        if ($this->questions->count() >= $this->game->max_questions_per_category) {
            session()->flash('error', 'You have reached the maximum number of questions per category for your subscription tier.');
            return;
        }

        $this->validate();

        Question::create([
            'category_id' => $this->category->id,
            'question' => $this->question,
            'answer' => $this->answer,
            'points' => $this->points,
            'time_limit' => $this->time_limit,
            'order' => $this->questions->count(),
        ]);

        $this->resetForm();
        $this->showAddForm = false;
        $this->loadQuestions();
        $this->dispatch('questionAdded');
        session()->flash('success', 'Question added successfully!');
    }

    public function editQuestion($questionId)
    {
        $question = Question::find($questionId);
        $this->editingQuestionId = $questionId;
        $this->question = $question->question;
        $this->answer = $question->answer;
        $this->points = $question->points;
        $this->time_limit = $question->time_limit;
    }

    public function updateQuestion()
    {
        $this->validate();

        $question = Question::find($this->editingQuestionId);
        $question->update([
            'question' => $this->question,
            'answer' => $this->answer,
            'points' => $this->points,
            'time_limit' => $this->time_limit,
        ]);

        $this->resetForm();
        $this->editingQuestionId = null;
        $this->loadQuestions();
        $this->dispatch('questionUpdated');
        session()->flash('success', 'Question updated successfully!');
    }

    public function cancelEdit()
    {
        $this->editingQuestionId = null;
        $this->resetForm();
    }

    public function deleteQuestion($questionId)
    {
        Question::find($questionId)->delete();
        $this->loadQuestions();
        $this->dispatch('questionDeleted');
        session()->flash('success', 'Question deleted successfully!');
    }

    public function moveUp($questionId)
    {
        $question = Question::find($questionId);
        $previousQuestion = Question::where('category_id', $this->category->id)
            ->where('order', '<', $question->order)
            ->orderBy('order', 'desc')
            ->first();

        if ($previousQuestion) {
            $tempOrder = $question->order;
            $question->update(['order' => $previousQuestion->order]);
            $previousQuestion->update(['order' => $tempOrder]);
            $this->loadQuestions();
        }
    }

    public function moveDown($questionId)
    {
        $question = Question::find($questionId);
        $nextQuestion = Question::where('category_id', $this->category->id)
            ->where('order', '>', $question->order)
            ->orderBy('order', 'asc')
            ->first();

        if ($nextQuestion) {
            $tempOrder = $question->order;
            $question->update(['order' => $nextQuestion->order]);
            $nextQuestion->update(['order' => $tempOrder]);
            $this->loadQuestions();
        }
    }

    public function resetForm()
    {
        $this->question = '';
        $this->answer = '';
        $this->points = 100;
        $this->time_limit = 30;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.question-manager');
    }
}