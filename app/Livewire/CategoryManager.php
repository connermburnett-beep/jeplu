<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Game;
use Livewire\Component;

class CategoryManager extends Component
{
    public Game $game;
    public $categories;
    public $newCategoryName = '';
    public $editingCategoryId = null;
    public $editingCategoryName = '';

    protected $rules = [
        'newCategoryName' => 'required|string|max:255',
        'editingCategoryName' => 'required|string|max:255',
    ];

    public function mount(Game $game)
    {
        $this->game = $game;
        $this->loadCategories();
    }

    public function loadCategories()
    {
        $this->categories = $this->game->categories()->with('questions')->orderBy('order')->get();
    }

    public function addCategory()
    {
        // Check if user can add more categories
        if ($this->categories->count() >= $this->game->max_categories) {
            session()->flash('error', 'You have reached the maximum number of categories for your subscription tier.');
            return;
        }

        $this->validate(['newCategoryName' => 'required|string|max:255']);

        Category::create([
            'game_id' => $this->game->id,
            'name' => $this->newCategoryName,
            'order' => $this->categories->count(),
        ]);

        $this->newCategoryName = '';
        $this->loadCategories();
        session()->flash('success', 'Category added successfully!');
    }

    public function startEdit($categoryId)
    {
        $category = $this->categories->firstWhere('id', $categoryId);
        $this->editingCategoryId = $categoryId;
        $this->editingCategoryName = $category->name;
    }

    public function updateCategory()
    {
        $this->validate(['editingCategoryName' => 'required|string|max:255']);

        $category = Category::find($this->editingCategoryId);
        $category->update(['name' => $this->editingCategoryName]);

        $this->editingCategoryId = null;
        $this->editingCategoryName = '';
        $this->loadCategories();
        session()->flash('success', 'Category updated successfully!');
    }

    public function cancelEdit()
    {
        $this->editingCategoryId = null;
        $this->editingCategoryName = '';
    }

    public function deleteCategory($categoryId)
    {
        Category::find($categoryId)->delete();
        $this->loadCategories();
        session()->flash('success', 'Category deleted successfully!');
    }

    public function moveUp($categoryId)
    {
        $category = Category::find($categoryId);
        $previousCategory = Category::where('game_id', $this->game->id)
            ->where('order', '<', $category->order)
            ->orderBy('order', 'desc')
            ->first();

        if ($previousCategory) {
            $tempOrder = $category->order;
            $category->update(['order' => $previousCategory->order]);
            $previousCategory->update(['order' => $tempOrder]);
            $this->loadCategories();
        }
    }

    public function moveDown($categoryId)
    {
        $category = Category::find($categoryId);
        $nextCategory = Category::where('game_id', $this->game->id)
            ->where('order', '>', $category->order)
            ->orderBy('order', 'asc')
            ->first();

        if ($nextCategory) {
            $tempOrder = $category->order;
            $category->update(['order' => $nextCategory->order]);
            $nextCategory->update(['order' => $tempOrder]);
            $this->loadCategories();
        }
    }

    public function render()
    {
        return view('livewire.category-manager');
    }
}