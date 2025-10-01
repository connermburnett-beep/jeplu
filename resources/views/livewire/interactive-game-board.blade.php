<div class="relative">
    {{-- Success/Error Messages --}}
    @if(session()->has('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            {{ session('error') }}
        </div>
    @endif

    {{-- Interactive Jeopardy Board --}}
    <div class="bg-gradient-to-b from-blue-900 to-blue-800 p-6 rounded-lg shadow-2xl">
        @if($game->categories->isEmpty())
            {{-- Empty State --}}
            <div class="text-center text-white py-12">
                <svg class="w-20 h-20 mx-auto mb-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <p class="text-xl mb-4">No categories yet. Let's build your game board!</p>
                <button 
                    wire:click="openCategoryModal(null)"
                    class="bg-yellow-400 hover:bg-yellow-500 text-blue-900 font-bold py-3 px-6 rounded-lg text-lg transition-colors shadow-lg"
                >
                    + Add Your First Category
                </button>
            </div>
        @else
            {{-- Game Board Grid --}}
            <div class="grid gap-2 mb-4" style="grid-template-columns: repeat({{ min($game->categories->count(), $game->max_categories) }}, minmax(0, 1fr));">
                @foreach($game->categories as $category)
                    <div class="bg-blue-700 rounded-lg overflow-hidden">
                        {{-- Category Header (Clickable) --}}
                        <div 
                            wire:click="openCategoryModal({{ $category->id }})"
                            class="bg-blue-600 text-white font-bold text-center py-4 px-2 border-b-4 border-yellow-400 cursor-pointer hover:bg-blue-500 transition-colors group relative"
                            title="Click to edit category"
                        >
                            <h3 class="text-sm md:text-base uppercase tracking-wide">{{ $category->name }}</h3>
                            <div class="absolute inset-0 bg-blue-500 opacity-0 group-hover:opacity-20 transition-opacity flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </div>
                        </div>
                        
                        {{-- Questions Grid --}}
                        <div class="space-y-2 p-2">
                            @php
                                $maxQuestions = $game->max_questions_per_category;
                                $currentQuestions = $category->questions->count();
                            @endphp
                            
                            @for($i = 0; $i < $maxQuestions; $i++)
                                @if($i < $currentQuestions)
                                    {{-- Existing Question --}}
                                    @php $question = $category->questions[$i]; @endphp
                                    <div 
                                        wire:click="openQuestionModal({{ $category->id }}, {{ $question->id }})"
                                        class="bg-blue-800 text-yellow-400 font-bold text-center py-6 rounded cursor-pointer hover:bg-blue-700 transition-colors border-2 border-yellow-400 group relative"
                                        title="Click to edit question"
                                    >
                                        <span class="text-2xl md:text-3xl">${{ $question->points }}</span>
                                        <div class="absolute inset-0 bg-blue-700 opacity-0 group-hover:opacity-30 transition-opacity flex items-center justify-center">
                                            <svg class="w-8 h-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                @else
                                    {{-- Empty Question Slot --}}
                                    <div 
                                        wire:click="openQuestionModal({{ $category->id }}, null)"
                                        class="bg-blue-900 bg-opacity-50 text-gray-400 text-center py-6 rounded cursor-pointer hover:bg-blue-800 hover:bg-opacity-70 transition-colors border-2 border-dashed border-blue-600 hover:border-yellow-400 group"
                                        title="Click to add question"
                                    >
                                        <svg class="w-8 h-8 mx-auto text-gray-500 group-hover:text-yellow-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </div>
                                @endif
                            @endfor
                        </div>
                    </div>
                @endforeach

                {{-- Add Category Column (if space available) --}}
                @if($game->categories->count() < $game->max_categories)
                    <div class="bg-blue-700 bg-opacity-30 rounded-lg border-2 border-dashed border-blue-500 hover:border-yellow-400 transition-colors">
                        <button 
                            wire:click="openCategoryModal(null)"
                            class="w-full h-full min-h-[400px] flex flex-col items-center justify-center text-blue-300 hover:text-yellow-400 transition-colors group"
                            title="Add new category"
                        >
                            <svg class="w-16 h-16 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span class="text-lg font-semibold">Add Category</span>
                            <span class="text-sm mt-1">({{ $game->categories->count() }}/{{ $game->max_categories }})</span>
                        </button>
                    </div>
                @endif
            </div>

            {{-- Board Info --}}
            <div class="text-center text-blue-200 text-sm">
                <p>💡 Click on category names to edit categories • Click on dollar amounts to edit questions • Click empty slots to add questions</p>
            </div>
        @endif
    </div>

    {{-- Category Modal --}}
    @if($showCategoryModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" wire:click.self="closeCategoryModal">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 transform transition-all">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-900">
                        {{ $editingCategoryId ? 'Edit Category' : 'Add New Category' }}
                    </h3>
                    <button wire:click="closeCategoryModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category Name</label>
                        <input 
                            type="text" 
                            wire:model="categoryName" 
                            placeholder="e.g., History, Science, Pop Culture"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            wire:keydown.enter="saveCategory"
                            autofocus
                        >
                        @error('categoryName') 
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                        @enderror
                    </div>

                    <div class="flex gap-3 pt-4">
                        @if($editingCategoryId)
                            <button 
                                wire:click="saveCategory" 
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors"
                            >
                                Update Category
                            </button>
                            <button 
                                wire:click="deleteCategory" 
                                onclick="return confirm('Are you sure? This will delete all questions in this category.')"
                                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition-colors"
                            >
                                Delete
                            </button>
                        @else
                            <button 
                                wire:click="saveCategory" 
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors"
                            >
                                Add Category
                            </button>
                        @endif
                        <button 
                            wire:click="closeCategoryModal" 
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Question Modal --}}
    @if($showQuestionModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" wire:click.self="closeQuestionModal">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full p-6 transform transition-all max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-900">
                        {{ $editingQuestionId ? 'Edit Question' : 'Add New Question' }}
                    </h3>
                    <button wire:click="closeQuestionModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Question/Clue
                            <span class="text-gray-500 font-normal">(What the host reads)</span>
                        </label>
                        <textarea 
                            wire:model="questionText" 
                            rows="3"
                            placeholder="This founding father appears on the $100 bill"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        ></textarea>
                        @error('questionText') 
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Answer
                            <span class="text-gray-500 font-normal">(In question form: "Who is..." or "What is...")</span>
                        </label>
                        <input 
                            type="text" 
                            wire:model="questionAnswer" 
                            placeholder="Who is Benjamin Franklin?"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                        @error('questionAnswer') 
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Points</label>
                            <input 
                                type="number" 
                                wire:model="questionPoints" 
                                min="0" 
                                step="100"
                                placeholder="200"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                            @error('questionPoints') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Time Limit (seconds)</label>
                            <input 
                                type="number" 
                                wire:model="questionTimeLimit" 
                                min="5" 
                                max="300"
                                placeholder="30"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                            @error('questionTimeLimit') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                            @enderror
                        </div>
                    </div>

                    <div class="flex gap-3 pt-4">
                        @if($editingQuestionId)
                            <button 
                                wire:click="saveQuestion" 
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors"
                            >
                                Update Question
                            </button>
                            <button 
                                wire:click="deleteQuestion" 
                                onclick="return confirm('Are you sure you want to delete this question?')"
                                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition-colors"
                            >
                                Delete
                            </button>
                        @else
                            <button 
                                wire:click="saveQuestion" 
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors"
                            >
                                Add Question
                            </button>
                        @endif
                        <button 
                            wire:click="closeQuestionModal" 
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>