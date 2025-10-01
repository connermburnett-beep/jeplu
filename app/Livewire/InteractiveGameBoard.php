<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Game;
use App\Models\Question;
use Livewire\Component;

class InteractiveGameBoard extends Component
{
    public Game $game;
    
    // Category Modal
    public $showCategoryModal = false;
    public $editingCategoryId = null;
    public $categoryName = '';
    
    // Question Modal
    public $showQuestionModal = false;
    public $editingQuestionId = null;
    public $selectedCategoryId = null;
    public $questionText = '';
    public $questionAnswer = '';
    public $questionPoints = 100;
    public $questionTimeLimit = 30;

    protected $rules = [
        'categoryName' => 'required|string|max:255',
        'questionText' => 'required|string|max:1000',
        'questionAnswer' => 'required|string|max:500',
        'questionPoints' => 'required|integer|min:0',
        'questionTimeLimit' => 'required|integer|min:5|max:300',
    ];

    public function mount(Game $game)
    {
        $this->game = $game;
    }

    // Category Modal Methods
    public function openCategoryModal($categoryId = null)
    {
        $this->resetCategoryForm();
        
        if ($categoryId) {
            $category = Category::find($categoryId);
            $this->editingCategoryId = $categoryId;
            $this->categoryName = $category->name;
        }
        
        $this->showCategoryModal = true;
    }

    public function closeCategoryModal()
    {
        $this->showCategoryModal = false;
        $this->resetCategoryForm();
    }

    public function saveCategory()
    {
        // Check category limit
        if (!$this->editingCategoryId && $this->game->categories->count() >= $this->game->max_categories) {
            session()->flash('error', 'You have reached the maximum number of categories for your subscription tier.');
            $this->closeCategoryModal();
            return;
        }

        $this->validate(['categoryName' => 'required|string|max:255']);

        if ($this->editingCategoryId) {
            // Update existing category
            $category = Category::find($this->editingCategoryId);
            $category->update(['name' => $this->categoryName]);
            session()->flash('success', 'Category updated successfully!');
        } else {
            // Create new category
            Category::create([
                'game_id' => $this->game->id,
                'name' => $this->categoryName,
                'order' => $this->game->categories->count(),
            ]);
            session()->flash('success', 'Category added successfully!');
        }

        $this->closeCategoryModal();
        $this->game->refresh();
    }

    public function deleteCategory()
    {
        if ($this->editingCategoryId) {
            Category::find($this->editingCategoryId)->delete();
            session()->flash('success', 'Category deleted successfully!');
            $this->closeCategoryModal();
            $this->game->refresh();
        }
    }

    private function resetCategoryForm()
    {
        $this->editingCategoryId = null;
        $this->categoryName = '';
        $this->resetErrorBag();
    }

    // Question Modal Methods
    public function openQuestionModal($categoryId, $questionId = null)
    {
        $this->resetQuestionForm();
        $this->selectedCategoryId = $categoryId;
        
        if ($questionId) {
            $question = Question::find($questionId);
            $this->editingQuestionId = $questionId;
            $this->questionText = $question->question;
            $this->questionAnswer = $question->answer;
            $this->questionPoints = $question->points;
            $this->questionTimeLimit = $question->time_limit;
        }
        
        $this->showQuestionModal = true;
    }

    public function closeQuestionModal()
    {
        $this->showQuestionModal = false;
        $this->resetQuestionForm();
    }

    public function saveQuestion()
    {
        $category = Category::find($this->selectedCategoryId);
        
        // Check question limit
        if (!$this->editingQuestionId && $category->questions->count() >= $this->game->max_questions_per_category) {
            session()->flash('error', 'You have reached the maximum number of questions for this category.');
            $this->closeQuestionModal();
            return;
        }

        $this->validate([
            'questionText' => 'required|string|max:1000',
            'questionAnswer' => 'required|string|max:500',
            'questionPoints' => 'required|integer|min:0',
            'questionTimeLimit' => 'required|integer|min:5|max:300',
        ]);

        if ($this->editingQuestionId) {
            // Update existing question
            $question = Question::find($this->editingQuestionId);
            $question->update([
                'question' => $this->questionText,
                'answer' => $this->questionAnswer,
                'points' => $this->questionPoints,
                'time_limit' => $this->questionTimeLimit,
            ]);
            session()->flash('success', 'Question updated successfully!');
        } else {
            // Create new question
            Question::create([
                'category_id' => $this->selectedCategoryId,
                'question' => $this->questionText,
                'answer' => $this->questionAnswer,
                'points' => $this->questionPoints,
                'time_limit' => $this->questionTimeLimit,
                'order' => $category->questions->count(),
            ]);
            session()->flash('success', 'Question added successfully!');
        }

        $this->closeQuestionModal();
        $this->game->refresh();
    }

    public function deleteQuestion()
    {
        if ($this->editingQuestionId) {
            Question::find($this->editingQuestionId)->delete();
            session()->flash('success', 'Question deleted successfully!');
            $this->closeQuestionModal();
            $this->game->refresh();
        }
    }

    private function resetQuestionForm()
    {
        $this->editingQuestionId = null;
        $this->selectedCategoryId = null;
        $this->questionText = '';
        $this->questionAnswer = '';
        $this->questionPoints = 100;
        $this->questionTimeLimit = 30;
        $this->resetErrorBag();
    }

    public function render()
    {
        $this->game->load(['categories' => function($query) {
            $query->orderBy('order')->with(['questions' => function($q) {
                $q->orderBy('order');
            }]);
        }]);
        
        return view('livewire.interactive-game-board');
    }
}