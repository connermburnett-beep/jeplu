<div>
    @if(session()->has('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-3">
        <h5 class="font-semibold text-sm text-gray-700">
            Questions ({{ $questions->count() }} / {{ $game->max_questions_per_category }})
        </h5>
        <button 
            wire:click="toggleAddForm" 
            class="text-sm bg-green-600 hover:bg-green-700 text-white font-bold py-1 px-3 rounded"
        >
            {{ $showAddForm ? 'Cancel' : '+ Add Question' }}
        </button>
    </div>

    <!-- Add/Edit Question Form -->
    @if($showAddForm || $editingQuestionId)
        <div class="mb-4 p-4 bg-gray-50 rounded border">
            <h6 class="font-semibold mb-3 text-sm">{{ $editingQuestionId ? 'Edit Question' : 'Add New Question' }}</h6>
            
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Question/Clue</label>
                    <textarea 
                        wire:model="question" 
                        rows="2" 
                        placeholder="This founding father appears on the $100 bill"
                        class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    ></textarea>
                    @error('question') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Answer (in question form)</label>
                    <input 
                        type="text" 
                        wire:model="answer" 
                        placeholder="Who is Benjamin Franklin?"
                        class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                    @error('answer') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Points</label>
                        <input 
                            type="number" 
                            wire:model="points" 
                            min="0" 
                            step="100"
                            class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                        @error('points') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Time Limit (seconds)</label>
                        <input 
                            type="number" 
                            wire:model="time_limit" 
                            min="5" 
                            max="300"
                            class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                        @error('time_limit') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex gap-2">
                    @if($editingQuestionId)
                        <button 
                            wire:click="updateQuestion" 
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm"
                        >
                            Update Question
                        </button>
                        <button 
                            wire:click="cancelEdit" 
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded text-sm"
                        >
                            Cancel
                        </button>
                    @else
                        <button 
                            wire:click="addQuestion" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm"
                        >
                            Add Question
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Questions List -->
    <div class="space-y-2">
        @forelse($questions as $q)
            <div class="p-3 bg-gray-50 rounded border text-sm">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="font-semibold text-gray-900 mb-1">{{ $q->question }}</div>
                        <div class="text-gray-600 mb-2">Answer: {{ $q->answer }}</div>
                        <div class="flex gap-4 text-xs text-gray-500">
                            <span class="font-semibold text-yellow-600">${{ $q->points }}</span>
                            <span>⏱️ {{ $q->time_limit }}s</span>
                        </div>
                    </div>
                    <div class="flex gap-1 ml-2">
                        <button 
                            wire:click="moveUp({{ $q->id }})" 
                            class="text-gray-600 hover:text-gray-800 p-1"
                            title="Move Up"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                            </svg>
                        </button>
                        <button 
                            wire:click="moveDown({{ $q->id }})" 
                            class="text-gray-600 hover:text-gray-800 p-1"
                            title="Move Down"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <button 
                            wire:click="editQuestion({{ $q->id }})" 
                            class="text-blue-600 hover:text-blue-800 p-1"
                            title="Edit"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                        <button 
                            wire:click="deleteQuestion({{ $q->id }})" 
                            onclick="return confirm('Are you sure you want to delete this question?')"
                            class="text-red-600 hover:text-red-800 p-1"
                            title="Delete"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-gray-500 py-4 text-sm">
                <p>No questions yet. Click "Add Question" to get started!</p>
            </div>
        @endforelse
    </div>
</div>